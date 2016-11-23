<?php 
global $wpdb;
$compid = $_SESSION['compid'];
$empuserid = $_SESSION['empuserid'];
$empdetails=$wpdb->get_row("SELECT * FROM employees emp, company com, department dep, designation des, employee_grades eg WHERE emp.EMP_Id='$empuserid' AND emp.COM_Id=com.COM_Id AND emp.DEP_Id=dep.DEP_Id AND emp.DES_Id=des.DES_Id AND emp.EG_Id=eg.EG_Id");
$repmngname = $wpdb->get_row("SELECT EMP_Name FROM employees WHERE EMP_Code='$empdetails->EMP_Reprtnmngrcode' AND COM_Id='$compid'");
					
?>
<style type="text/css">
#my_centered_buttons { text-align: center; width:100%; margin-top:60px; }
</style>
<div class="postbox">
    <div class="inside">
        <div class="wrap pre-travel-request" id="wp-erp">
            <h2><?php _e( 'Pre Travel Expense Request', 'employee' ); ?></h2>
            <code class="description">ADD Request</code>
            <!-- Messages -->
            <div style="display:none" id="failure" class="notice notice-error is-dismissible">
            <p id="p-failure"></p>
            </div>

            <div style="display:none" id="notice" class="notice notice-warning is-dismissible">
                <p id="p-notice"></p>
            </div>

            <div style="display:none" id="success" class="notice notice-success is-dismissible">
                <p id="p-success"></p>
            </div>

            <div style="display:none" id="info" class="notice notice-info is-dismissible">
                <p id="p-info"></p>
            </div>
            <div style="margin-top:60px;">
            <table class="wp-list-table widefat striped admins">
              <tr>
                <td width="20%">Employee Code</td>
                <td width="5%">:</td>
                <td width="25%"><?php echo $empdetails->EMP_Code?> (<?php echo $empdetails->EG_Name?>)</td>
                <td width="20%">Company Name</td>
                <td width="5%">:</td>
                <td width="25%"><?php echo stripslashes($empdetails->COM_Name); ?></td>
              </tr>
              <tr>
                <td width="20%">Employee Name</td>
                <td width="5%">:</td>
                <td width="25%"><?php echo $empdetails->EMP_Name; ?></td>
                <td width="20%">Reporting Manager Code</td>
                <td width="5%">:</td>
                <td width="25%"><?php echo $empdetails->EMP_Reprtnmngrcode; ?></td>
              </tr>
              <tr>
                <td>Employee Designation </td>
                <td>:</td>
                <td><?php echo $empdetails->DES_Name; ?></td>
                <td>Reporting Manager Name</td>
                <td>:</td>
                <td><?php echo $repmngname->EMP_Name;?></td>
              </tr>
              <tr>
                <td width="20%">Employee Department</td>
                <td width="5%">:</td>
                <td width="25%"><?php echo $empdetails->DEP_Name; ?></td>

              </tr>
            </table>
            </div>
            <div style="margin-top:60px;">
            <form id="request_form" name="input" action="#" method="post">
            <table class="wp-list-table widefat striped admins" border="0" id="table1">
                  <thead class="cf">
                    <tr>
                      <th class="column-primary">Date</th>
                      <th class="column-primary">Expense Description</th>
                      <th class="column-primary" colspan="2">Expense Category</th>
                      <th class="column-primary" >Place</th>
                      <th class="column-primary">Estimated Cost</th>
                      <th class="column-primary">Get Quote</th>
                    </tr>
                  </thead>
                  <tbody <?php /*?>align="center"<?php */?>>
                    <tr>
                      <td data-title="Date" class=""><input name="txtDate[]" id="txtDate1" class="erp-leave-date-field" placeholder="dd/mm/yyyy" autocomplete="off"/>
                      <input name="txtStartDate[]" id="txtStartDate1" class="" placeholder="dd/mm/yyyy" autocomplete="off" style="width:105px; display:none;" value="n/a" /><input name="txtEndDate[]" id="txtEndDate1" class="" placeholder="dd/mm/yyyy" autocomplete="off" style="width:105px; display:none;" value="n/a" />
                      <input type="text" name="textBillNo[]" id="textBillNo1" autocomplete="off"  class="" style="width:105px; display:none;" value="n/a"/>
                      </td>
                      <td data-title="Description"><textarea name="txtaExpdesc[]" id="txtaExpdesc1" class="" autocomplete="off"></textarea><input type="text" class="" name="txtdist[]" id="txtdist1" autocomplete="off" style="display:none;" value="n/a"/></td>
                      <td data-title="Category"><select name="selExpcat[]" id="selExpcat1" class="" onchange="javascript:getMotPreTravel(this.value,1)">
                          <option value="">Select</option>
                         
                        </select></td>
                      <td data-title="Category"><span id="modeoftr1acontent">
                        <select name="selModeofTransp[]"  id="selModeofTransp1" class="" onchange="setFromTo(this.value, 1);">
                          <option value="">Select</option>
                          
                        </select>
                        </span></td>
                      <td data-title="Place"><span id="city1container">
                        <input  name="from[]" id="from1" type="text" placeholder="From" class="">
                        <input  name="to[]" id="to1" type="text" placeholder="To" class="">
                        </span></td>
                      <td data-title="Estimated Cost"> <span id="cost1container">
                        <input type="text" class="" name="txtCost[]" id="txtCost1" onkeyup="valPreCost(this.value);" onchange="valPreCost(this.value);" autocomplete="off"/>
                        </span></td>
                      <td data-title="Get Quote"><button type="button" name="getQuote" id="getQuote1" class="button button-primary" onclick="getQuotefunc(1)">Get Quote</button></td>
                    </tr>
                  </tbody>
                </table>
                </form>
            </div>
            <div id="my_centered_buttons">
            <button type="button" name="getQuote" id="" class="button button-primary" onclick="">Submit</button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button" name="getQuote" id="reset" class="button">Reset</button>
            </div>
        </div>
        
        <!-- Grade Limits -->
        
        <?php
        $mydetails = myDetails();
        $selgrdLim=$wpdb->get_row("SELECT * FROM grade_limits WHERE EG_Id='$mydetails->EG_Id' AND GL_Status=1");
        //$selgrdLim=select_query("grade_limits", "*", "EG_Id='$mydetails[EG_Id]' AND GL_Status=1", $filename, 0);
        
        $selgrdLim = json_decode(json_encode($selgrdLim), True);
        //print_r($selgrdLim);
	    $selgrdLim=array_values($selgrdLim);
        //print_r($selgrdLim);

        ?>
        <table id="expenseLimitId" class="wp-list-table widefat fixed striped admins">
        <tr>


            <h4>Expense limits:</h4>

            <?php 
            $i=0;

            $selmod=$wpdb->get_results("SELECT MOD_Name FROM mode WHERE COM_Id = 0");

            $i = $gradelimitm = $totalLimitAmnt = 0;

            foreach($selmod as $rowmod){

                    $k=$i+4;

                    if($selgrdLim[$k]){

            ?>
              <td>
                  <?php echo $rowmod->MOD_Name ?> Expense Limit - <span class="oval-1" ><?php echo $selgrdLim[$k] ? IND_money_format($selgrdLim[$k]).".00" : 'No Limit'; ?></span>
              <?php	
                        $gradelimitm++;
                        $totalLimitAmnt += $selgrdLim[$k]; 

                    }	

                    if($gradelimitm%3==0)
                    echo '<tr>';

                    $i++; 	
            } 
                    ?>
                    </td>

            <?php 
            if($totalLimitAmnt < 1) echo '<script>$("#expenseLimitId").css("display", "none");</script>';

            ?>
    </div>
    
</div>
