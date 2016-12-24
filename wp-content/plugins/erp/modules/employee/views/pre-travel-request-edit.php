<?php
global $showProCode;
global $totalcost;
$etEdit = 1;
require_once WPERP_EMPLOYEE_PATH . '/includes/functions-pre-travel-req.php';
global $wpdb;
$compid = $_SESSION['compid'];
$empuserid = $_SESSION['empuserid'];
$reqid = $_GET['reqid'];	
$selexpcat=$wpdb->get_results("SELECT * FROM expense_category WHERE EC_Id IN (1,2,4)");
$selmode=$wpdb->get_results("SELECT * FROM mode WHERE EC_Id IN (1,2,4) AND COM_Id IN (0, '$compid') AND MOD_Status=1");
?>
<style type="text/css">
#my_centered_buttons { text-align: center; width:100%; margin-top:60px; }
</style>
<div class="postbox">
    <div class="inside">
        <div class="wrap pre-travel-request erp" id="wp-erp">
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
            <?php
                $row = 0;
                require WPERP_EMPLOYEE_VIEWS."/employee-details.php";
            ?>
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
            <form id="request_edit_form" name="input" action="#" method="post">
            <table class="wp-list-table widefat striped admins" border="0" id="table-pre-travel">
                  <thead class="cf">
                    <tr>
                      <th class="column-primary">Date</th>
                      <th class="column-primary">Expense Description</th>
                      <th class="column-primary" colspan="2">Expense Category</th>
                      <th class="column-primary" >Place</th>
                      <th class="column-primary">Estimated Cost</th>
                      <th class="column-primary">Get Quote</th>
                      <th class="column-primary">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php 

                        $rows=1;

                        $selrequest=$wpdb->get_results("SELECT * FROM request_details WHERE REQ_Id='$reqid' AND RD_Status=1 ORDER BY RD_Dateoftravel ASC");

                        $rdidarry=array();


                        foreach($selrequest as $rowrequest)
                        {

                                $disabled=0;

                                if(count($selrequest)==1){

                                        $disabled=1;

                                } else {

                                        if(($rowrequest->MOD_Id == 1) || ($rowrequest->MOD_Id == 2) || ($rowrequest->MOD_Id == 5)){

                                                
                                                if($selrdbs=$wpdb->get_row("SELECT * FROM booking_status WHERE RD_Id='$rowrequest->RD_Id' AND BS_Status=1 AND BS_Active=1")){

                                                        if( ($selrdbs->BA_Id != 1) /*&& ($selrdbs['BA_Id'] != 3)*/){

                                                                $disabled=1;
                                                        }

                                                } else {

                                                        $disabled=0;

                                                }

                                        } else {

                                                $disabled=0;

                                        }


                                }


                        ?>
                    <tr>
                      <td data-title="Date" class=""><input name="txtDate[]" id="txtDate<?php echo $rows; ?>" <?php echo $disabled ? 'class="" readonly="readonly"' : 'class="pretraveldate"'; ?> class="pretraveldate" placeholder="dd/mm/yyyy" autocomplete="off" value="<?php if($rowrequest->RD_Dateoftravel=="0000-00-00") echo ""; else echo date('d-m-Y',strtotime($rowrequest->RD_Dateoftravel)); ?>"/>
                      <input name="txtStartDate[]" id="txtStartDate<?php echo $rows; ?>" class="" placeholder="dd/mm/yyyy" autocomplete="off" style="width:105px; display:none;" value="n/a" /><input name="txtEndDate[]" id="txtEndDate<?php echo $rows; ?>" class="" placeholder="dd/mm/yyyy" autocomplete="off" style="width:105px; display:none;" value="n/a" />
                      <input type="text" name="textBillNo[]" id="textBillNo<?php echo $rows; ?>" autocomplete="off"  class="" style="width:105px; display:none;" value="n/a"/>
                      </td>
                      <td data-title="Description"><textarea name="txtaExpdesc[]" <?php echo $disabled ? 'readonly="readonly"':'';?> id="txtaExpdesc<?php echo $rows; ?>" class="" autocomplete="off"><?php echo stripslashes($rowrequest->RD_Description); ?></textarea><input type="text" class="" name="txtdist[]" id="txtdist1" autocomplete="off" style="display:none;" value="n/a"/></td>
                      <td data-title="Category"><input type="hidden" <?php if($disabled){?> name="selExpcat[]" id="selExpcat<?php echo $rows; ?>" value="<?php echo $rowrequest->EC_Id?>" onchange="javascript:getMotPreTravel(this.value,1)"  <?php } ?>/>
                          <select <?php if($disabled){?> disabled="disabled" <?php } else ?> name="selExpcat[]" id="selExpcat<?php echo $rows; ?>" onchange="javascript:getMotPreTravel(this.value,<?php echo $rows; ?>)" class="">
                          <option value="">Select</option>
                          <?php
                          foreach($selexpcat as $rowexpcat)
				  {
				  ?>
                          <option value="<?php echo $rowexpcat->EC_Id?>" <?php if($rowexpcat->EC_Id==$rowrequest->EC_Id) echo 'selected="selected"'; ?>><?php echo $rowexpcat->EC_Name; ?></option>
                          <?php } ?>
                         
                        </select></td>
                      <td data-title="Category"><span id="modeoftr<?php echo $rows; ?>acontent"><input type="hidden" <?php if($disabled){ ?> name="selModeofTransp[]" id="selModeofTransp<?php echo $rows; ?>" value="<?php echo $rowrequest->MOD_Id; ?>" <?php } ?> />
                        <span id="modeoftr<?php echo $rows; ?>1acontent">
                        <select <?php if($disabled){ ?> disabled="disabled" <?php } else ?> name="selModeofTransp[]"  id="selModeofTransp<?php echo $rows; ?>" class="">
                          <option value="">Select</option>
                          <?php
                          foreach($selmode as $rowsql)
					  {
					  ?>
                          <option value="<?php echo $rowsql->MOD_Id; ?>" <?php if($rowsql->MOD_Id==$rowrequest->MOD_Id) echo 'selected="selected"'; ?>><?php echo $rowsql->MOD_Name; ?></option>
                          <?php } ?>
                        </select>
                        </span></td>
                        <td data-title="Place"><span id="city<?php echo $rows; ?>container">
                        <input  name="from[]" id="from<?php echo $rows; ?>" type="text" placeholder="From" value="<?php echo $rowrequest->RD_Cityfrom?>" <?php echo ($disabled) ? 'readonly="readonly"': ''; ?>>
                        <input  name="to[]" id="to<?php echo $rows; ?>" <?php if($rowrequest->EC_Id==2 || $rowrequest->EC_Id==4){ echo 'value="n/a" style="display:none;"'; } else { echo 'value="'.$rowrequest->RD_Cityto.'"'; } ?> <?php echo ($disabled) ? 'readonly="readonly"': ''; ?> type="text" placeholder="To" class="">
                        </span></td>
                        <td data-title="Estimated Cost"><span id="cost1container">
                        <input type="text" class="" name="txtCost[]" id="txtCost" onkeyup="valPreCost(this.value);" onchange="valPreCost(this.value);" value="<?php echo $rowrequest->RD_Cost;?>" <?php echo ($disabled) ? 'readonly="readonly"' : ''; ?> autocomplete="off"/>
                        </br><span class="red" id="show-exceed"></span>
                        <input type="hidden" value="1" name="ectype" id="ectype"/>
                        <input type="hidden" value="0" name="expenseLimit" id="expenseLimit"/>
                        <input type="hidden" value="<?php echo $rowrequest->RD_Id; ?>" name="rdids[]"/>
                        <input type="hidden" name="action" id="send_pre_travel_request_edit" value="send_pre_travel_request_edit">
                        <input type="hidden" value="<?php echo $reqid; ?>" name="reqid" id="reqid"/>
                        </span></td>
                      <td data-title="Get Quote"><button type="button" name="getQuote" id="getQuote1" class="button button-primary" onclick="getQuotefunc(1)" <?php echo ($disabled) ? 'disabled="disabled" ' : ' title="Get Quote"'; ?>>Get Quote</button></td>
                      <td><button type="button" value="<?php echo $rowrequest->RD_Id; ?>" class="button button-default" name="deleteRowbutton" id="deleteRowbutton" title="delete row" <?php echo ($disabled) ? 'disabled="disabled" ' : ' onclick="return checkDeletRow();" title="delete row"'; ?> ><i class="fa fa-times"></i></button></td>
                    </tr>
                    <?php 
                    $rows++; 
                    $totalcost+=$rowrequest->RD_Cost;
                    array_push($rdidarry, $rowrequest->RD_Id);

                    } ?>
                  </tbody>
                </table>
                <table class="wp-list-table widefat striped admins" style="font-weight:bold;">
                  <tr>
                    <td align="right" width="85%">Total Estimated Cost	</td>
                    <td align="center" width="5%">:</td>
                    <td align="right" ><?php echo IND_money_format($totalcost).".00"; ?></td>
                  </tr>
                </table>
                <input type="hidden" id="hidrowno" name="hidrowno" value="<?php echo $rows-1; ?>" />
                <div style="float:right;"><a title="Add Rows" class="btn btn-default"><span id="add-row-pretravel-edit" class="dashicons dashicons-plus-alt"></span></a><span id="removebuttoncontainer"></span></div>
                <span id="totaltable"> </span>
                
            </div>
            <div id="my_centered_buttons">
            <input type="submit" value="Update" name="submit" id="submit-pre-travel-request" class="button button-primary">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button" name="reset" id="reset" class="button">Reset</button>
            </div>
            </form>
            <div style="margin-top:60px" id="grade-limit" class="postbox leads-actions closed">
                <div class="handlediv" title="<?php _e( 'Click to toggle', 'erp' ); ?>"><br></div>
                <h3 class="hndle"><span><?php _e( 'Grade Limits', 'erp' ); ?></span></h3>
                <div class="inside">
                   <!-- Grade Limits -->
                   <?php _e(gradeLimits(''));?>
                </div>
            </div><!-- .postbox -->
        </div>
    </div>
    
</div>
