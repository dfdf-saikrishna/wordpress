<div class="postbox">
    <div class="inside emp-import">
        <?php if (isset($_GET['error'])) { ?>
            <div id="failure" class="notice notice-error is-dismissible">
                <p id="p-failure">Please Upload Excel File</p>
            </div>
        <?php } ?>
        <h3><?php _e('Import Excel', 'crp'); ?></h3>

        <form method="post" action="admin.php?page=Upload-Employees" enctype="multipart/form-data" id="import_form">

            <table class="form-table">
                <tbody>

                    <tr>
                        <th>
                            <label for="type"><?php _e('Excel File', 'crp'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="file" name="csv_file" id="csv_file" />
                            <p class="description"><?php _e('Upload a Excel file.', 'crp'); ?></p>
                            <p id="download_sample_wrap">
                                <input type="hidden" value="" />
                                <a href="https://expirebox.com/files/8087511b301a051a8372d124d7d5c1e2.xls">Download Sample Excel</a>
                            </p>
                        </td>
                    </tr>
                </tbody>


                <tbody id="fields_container" style="display: none;">

                </tbody>
            </table>

            <p class="submit">
                <span class="erp-loader" style="margin-left:67px;margin-top: 4px;display:none"></span>
                <input type="submit" name="crp_import_excel" id="crp_import_excel" class="button button-primary" value="Import">
            </p>
        </form>
    </div><!-- .inside -->
</div><!-- .postbox -->

<?php ?>
<div class="wrap erp-company-upload" id="wp-erp">
    <?php
    global $wpdb;
    $table = new WeDevs\ERP\Company\Upload_List_Table();
    $table->prepare_items();
    ?>
    <div class="list-table-wrap erp-company-departments-wrap">
        <div class="list-table-inner erp-company-departments-wrap-inner">
            <form method="GET">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                <?php $table->display() ?>
            </form>

        </div>
    </div>
</div>