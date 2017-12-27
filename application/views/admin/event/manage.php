<div class="row">
    <div class="col-xs-12"> 
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"><?php echo isset($pageHeading) ? $pageHeading : ''; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                <div class="row">                     
                    <?php echo form_open(null, array("id" => "manage-event-form", "method" => "post")); ?>
                    <div class="col-lg-8">
                        <div class="form-group <?php echo!empty(form_error('title')) ? 'has-error' : ''; ?>">
                            <label class="control-label" for="title">Event Title <em>*</em></label>
                            <?php echo form_input("title", set_value("title", isset($data->title) ? $data->title : "",FALSE), "id='title' class='form-control'"); ?>
                            <?php echo form_error('title'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <?php isset($data->start_date) ? $data->start_date = date(DATETIME_FORMATE, strtotime($data->start_date)) : ""; ?>   
                    <?php
                    if ($this->input->get('date')) {
                        @$data->start_date = date(DATETIME_FORMATE, strtotime($this->input->get('date')));
                    }
                    ?>
                    <div class="col-lg-4">  
                        <h5><strong class="<?php echo (!empty(form_error('start_date'))) ? 'text-red' : ''; ?>">Start Date <em>*</em></strong></h5>
                        <div class="form-group input-group date <?php echo (!empty(form_error('start_date'))) ? 'has-error' : ''; ?>"> 
                            <?php echo form_input("start_date", set_value("start_date", isset($data->start_date) ? date('d-m-Y H:i', strtotime($data->start_date)) : ""), "id='start_date' class='form-control' placeholder='Start Date'"); ?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>                                
                        </div>
                        <?php echo form_error('start_date'); ?>
                    </div>
                    <?php isset($data->end_date) ? $data->end_date = date(DATETIME_FORMATE, strtotime($data->end_date)) : ''; ?>
                    <div class="col-lg-4"> 
                        <h5><strong class="<?php echo (!empty(form_error('end_date'))) ? 'text-red' : ''; ?>">End Date <em>*</em></strong></h5>
                        <div class="form-group input-group date <?php echo (!empty(form_error('end_date'))) ? 'has-error' : ''; ?>">
                            <?php echo form_input("end_date", set_value("end_date", isset($data->end_date) ? date('d-m-Y H:i', strtotime($data->end_date)) : ""), "id='end_date' class='form-control' placeholder='End Date'"); ?>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>   
                        </div>
                        <?php echo form_error('end_date'); ?>
                    </div> 
                    <div class="clearfix"></div>
                    <div class="col-lg-8">
                        <div class="form-group <?php echo!empty(form_error('description')) ? 'has-error' : ''; ?>">
                            <label class="control-label" for="description">Description*</label>
                            <?php echo form_textarea("description", set_value("description", isset($data->description) ? $data->description : "", FALSE), "id='description' class='form-control editor'"); ?>
                            <?php echo form_error('description'); ?>
                        </div>

                        <?php echo form_hidden('id', set_value('id', isset($data->id) ? $data->id : "")); ?>            
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" onclick="window.location.href = '<?php echo site_url("admin/events"); ?>'">Cancel</button>
                    </div>

                    <?php echo form_close(); ?>
                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row --> 
<script>
    $(function () {
        $('#start_date').datetimepicker({
            format: 'd-m-Y H:i',
            mask: '39-19-9999 29:59'
        });
        $('#end_date').datetimepicker({
            format: 'd-m-Y H:i',
            mask: '39-19-9999 29:59'
        });
    });
</script>