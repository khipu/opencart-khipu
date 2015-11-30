<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-khipu_manual" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-khipu_manual" class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-khipu_manual_receiverid"><span class="required">*</span> <?php echo $entry_receiverid; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="khipu_manual_receiverid" value="<?php echo $khipu_manual_receiverid; ?>" placeholder="<?php echo $entry_receiverid; ?>" id="khipu_manual_receiverid" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-khipu_manual_secret"><span class="required">*</span> <?php echo $entry_secret; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="khipu_manual_secret" value="<?php echo $khipu_manual_secret; ?>" placeholder="<?php echo $entry_secret; ?>" id="khipu_manual_secret" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-khipu_manual_completed_status_id"><?php echo $entry_completed_status; ?></label>
                        <div class="col-sm-10">
                            <select name="khipu_manual_completed_status_id" id="input-khipu_manual_completed_status_id" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $khipu_manual_completed_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-khipu_manual_geo_zone_id"><?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-10">
                            <select name="khipu_manual_geo_zone_id" id="input-khipu_manual_geo_zone_id" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $khipu_manual_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-khipu_manual_status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="khipu_manual_status" id="input-khipu_manual_status" class="form-control">
                                <?php if ($khipu_manual_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-khipu_manual_sort_order"><?php echo $entry_sort_order ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="khipu_manual_sort_order" value="<?php echo $khipu_manual_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-khipu_manual_sort_order" class="form-control" />
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <?php echo $footer; ?> 
