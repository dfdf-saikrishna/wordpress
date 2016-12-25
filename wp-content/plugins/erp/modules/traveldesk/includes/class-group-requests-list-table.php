<?php
namespace WeDevs\ERP\Traveldesk;
/**
 * PART 2. Defining Custom Table List
 * ============================================================================
 *
 * In this part you are going to define custom table list class,
 * that will display your database records in nice looking table
 *
 * http://codex.wordpress.org/Class_Reference/WP_List_Table
 * http://wordpress.org/extend/plugins/custom-list-table-example/
 */

//if (!class_exists('WP_List_Table')) {
    //require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
//}

/**
 * Custom_Table_Example_List_Table class that will display our custom table
 * records in nice table
 */
class Group_Requests_List extends \WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'workflow',
            'plural' => 'workflows',
        ));
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_Request_Code($item)
    {
        return "<a href='/wp-admin/admin.php?page=Group-Request-Details&reqid=$item[REQ_Id]'>".$item['REQ_Code']."</a>";
    }
    
    function column_Actions($item){
        if($item['REQ_Claim']){
					
                echo approvals(5);

        } else {

        if(!$item['REQ_PreToPostStatus']){
        return "<a href='/wp-admin/admin.php?page=Edit-Group-Request&reqid=$item[REQ_Id]'><button type='button' value='' class='button button-default' name='deleteRowbutton' id='editRowbutton' title='Edit'><i class='dashicons dashicons-edit'></i></button></a>";
        }
        }
        
    }
    
    function column_Cost_Per_Employee($item){
        global $wpdb;
        $totalcost = $wpdb->get_var("SELECT SUM(RD_Cost) AS total FROM request_details WHERE REQ_Id='$item[REQ_Id]' AND RD_Status=1");
        $cntEmp = count($wpdb->get_results("SELECT RE_Id FROM request_employee WHERE REQ_Id='$item[REQ_Id]' AND RE_Status=1"));
        return IND_money_format($totalcost/$cntEmp).".00";
    }
     
    function column_Claim_Status($item){
        global $wpdb;
         
        if($item['REQ_Claim']){
						
                return '<span class="label label-success" title="Claimed on: '.date("d/M/y",strtotime($item["REQ_ClaimDate"])).'">Claimed</span>';

        } else {
                
                if($selptc=$wpdb->get_row("SELECT PTC_Id, PTC_Status FROM pre_travel_claim WHERE REQ_Id='$item[REQ_Id]'")){

                        echo $appr=approvals($selptc->PTC_Status);

                } else {

                        echo approvals(5);
                }
        }
    }
    
    function column_Request_Date($item){
        return date('d-M-y',strtotime($item['REQ_Date']));
    }
    function column_Total_Cost($item){
        global $wpdb;
        $totalcost = $wpdb->get_var("SELECT SUM(RD_Cost) AS total FROM request_details WHERE REQ_Id='$item[REQ_Id]' AND RD_Status=1");
        return IND_money_format($totalcost).".00"; 
    }
    function column_employees($item){
       global $wpdb;
       $cntEmp = count($wpdb->get_results("SELECT RE_Id FROM request_employee WHERE REQ_Id='$item[REQ_Id]' AND RE_Status=1"));
       echo '<a href="" class="group_emp" value="'.$item['REQ_Id'].'">'.$cntEmp.'</a>';
        
        $selemps=$selsql=$wpdb->get_results("SELECT EMP_Code, EMP_Name FROM request_employee tdre, employees emp WHERE tdre.EMP_Id=emp.EMP_Id AND tdre.REQ_Id='$item[REQ_Id]' AND tdre.RE_Status=1");
        echo '<div style="display:none" class="show_group_emps">';
        echo "Request Code: <b>".$item['REQ_Code']."</b><br>";

        foreach($selemps as $vals)
        echo $vals->EMP_Code." - ".$vals->EMP_Name." <br> ";
        echo '</div>';
      
    }

    function get_columns()
    {
        $columns = array(
            //'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'Request_Code' => __('Request Code', 'group_request_list'),
            'Cost_Per_Employee' => __('Cost per Employee (Rs)', 'group_request_list'),
            'Total_Cost' => __('Total Cost', 'group_request_list'),
            'Request_Date' => __('Request Date', 'group_request_list'),
            'employees' => __('Employees', 'group_request_list'),
            'Claim_Status' => __('Claim Status', 'group_request_list'),
            'Actions' => __('Actions', 'group_request_list'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'Request_Code' => array('Request Code', true),
            'Cost_Per_Employee' => array('Cost per Employee (Rs)', false),
            'Total_Cost' => array('Total Cost', false),
            'Request_Date' => array('Request Date', false),
            'employees' => array('Employees', false),
            'Claim_Status' => array('Claim Status', false),
            'Actions' => array('Actions', false), 
        );
        return $sortable_columns;
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $compid = $_SESSION['compid'];
        $table_name = 'policy'; // do not forget about tables prefix

        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();
        
        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'REQ_Id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
		if(!empty($_POST["s"])) {
            $search = $_POST["s"];
			$query="";
			$searchcol= array(
			'REQ_Code'
			);
			$i =0;
			foreach( $searchcol as $col) {
				if($i==0) {
					$sqlterm = 'WHERE';
				} else {
					$sqlterm = 'OR';
				}
				if(!empty($_REQUEST["s"])) {$query .=  ' '.$sqlterm.' '.$col.' LIKE "'.$search.'"';}
				$i++;
			}
                        // will be used in pagination settings
                        $total_items = count($wpdb->get_results("SELECT * FROM requests ".$query." AND COM_Id='$compid' AND REQ_Active != 9 AND REQ_Type=4"));
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM requests ".$query." AND COM_Id='$compid' AND REQ_Active != 9 AND REQ_Type=4 ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
		}
		else{
                        // will be used in pagination settings
                        $total_items = count($wpdb->get_results("SELECT * FROM requests WHERE COM_Id='$compid' AND REQ_Active != 9 AND REQ_Type=4"));
			//$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE WP_Status=0 ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
                        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM requests WHERE COM_Id='$compid' AND REQ_Active != 9 AND REQ_Type=4 ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
		}
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function custom_table_example_validate_person($item)
{
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'custom_table_example');
    if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
    if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'custom_table_example');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

/**
 * Do not forget about translating your plugin, use __('english string', 'your_uniq_plugin_name') to retrieve translated string
 * and _e('english string', 'your_uniq_plugin_name') to echo it
 * in this example plugin your_uniq_plugin_name == custom_table_example
 *
 * to create translation file, use poedit FileNew catalog...
 * Fill name of project, add "." to path (ENSURE that it was added - must be in list)
 * and on last tab add "__" and "_e"
 *
 * Name your file like this: [my_plugin]-[ru_RU].po
 *
 * http://codex.wordpress.org/Writing_a_Plugin#Internationalizing_Your_Plugin
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 */
function custom_table_example_languages()
{
    load_plugin_textdomain('custom_table_example', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'custom_table_example_languages');