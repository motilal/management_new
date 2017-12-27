<div class="row">
    <div class="col-xs-12"> 
        <div class="box">
            <div class="box-header">
                <i class="fa fa-globe"></i> 
                <h3 class="box-title"><?php echo isset($pageHeading) ? $pageHeading : '&nbsp;'; ?></h3>
                <div class="box-tools pull-right">
                    <div class="btn-group" data-toggle="btn-toggle">
                        <a href="#" data-toggle="modal" data-target="#modal-manage" class="btn btn-primary btn-sm add_new_item"><i class="fa fa-plus"></i> Add New City </a>
                        <a href="<?php echo site_url('admin/cities?download=report'); ?>" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Export CSV</a>
                    </div>
                </div>
            </div>     
            <!-- /.box-header -->
            <div class="box-body"> 
                <table id="dataTables-grid" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr> 
                            <th>Sr.</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Country</th>
                            <th>Status</th> 
                            <th width="10%">Action</th> 
                        </tr>
                    </thead> 
                    <tbody> 
                        <?php if (isset($result) && $result != "") { ?>
                            <?php foreach ($result as $key => $row): ?>
                                <tr>
                                    <td><?php echo $row[0]; ?></td>
                                    <td><?php echo $row[1]; ?></td>
                                    <td><?php echo $row[2]; ?></td>
                                    <td><?php echo $row[3]; ?></td>
                                    <td><?php echo $row[4]; ?></td>
                                    <td><?php echo $row[5]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?> 
                    </tbody> 
                </table>
            </div>
            <!-- /.box-body --> 
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div> 

<div class="modal fade" id="modal-manage">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo form_open('admin/cities/manage', array("id" => "manage-form", "method" => "post")); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New City</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        $country_options = array('' => 'Select Country');
                        if (isset($country_dropdown) && $country_dropdown->num_rows() > 0):
                            foreach ($country_dropdown->result() as $key => $value) :
                                $country_options[$value->id] = $value->name;
                            endforeach;
                        endif;
                        ?> 
                        <div class="form-group">
                            <label class="control-label" for="country_id">Select Country <em>*</em></label>
                            <?php echo form_dropdown('country_id', $country_options, '', 'class="form-control select2" id="country" style="width:100%;"'); ?> 
                        </div>

                        <?php
                        $state_options = array('' => 'Select State');
                        if (isset($state_dropdown) && $state_dropdown->num_rows() > 0):
                            foreach ($state_dropdown->result() as $key => $value) :
                                $state_options[$value->id] = $value->name;
                            endforeach;
                        endif;
                        ?> 
                        <div class="form-group">
                            <label class="control-label" for="state_id">Select State <em>*</em></label>
                            <?php echo form_dropdown('state_id', $state_options, '', 'class="form-control select2" id="state" style="width:100%;"'); ?> 
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="name">City Name <em>*</em></label>
                            <?php echo form_input("name", '', "id='name' class='form-control'"); ?> 
                        </div>  

                        <?php echo form_hidden('id'); ?> 
                    </div>
                </div>
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo form_close(); ?> 
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
    var current_url = '<?php echo current_url(); ?>';
    /*
     params 
     1 sorting remove from colomns
     2 default sort order of colomn set default []
     3 default paging 
     */
    var datatbl = dynamic_datatable_init(current_url, [0, 5], [[1, 'asc']], DEFAULT_PAGING);
    $('#country').on('select2:select', function (e) {
        loadStates();
    });

    $(document).ready(function () {
        if($('#country').val()!=""){
            loadStates();
        }
        
        $('#manage-form').submit(function (e) {
            var _this = $(this);
            _this.find("[type='submit']").prop('disabled', true);
            $('.form-group .help-block').remove();
            $('.form-group').removeClass('has-error');
            e.preventDefault();
            $.ajax({
                url: _this.attr('action'),
                type: "POST",
                data: _this.serialize(),
                success: function (res)
                {
                    _this.find("[type='submit']").prop('disabled', false);
                    if (res.validation_error) {
                        $.each(res.validation_error, function (index, value) {
                            var elem = _this.find('[name="' + index + '"]');
                            var error = '<div class="help-block">' + value + '</div>';
                            elem.closest('.form-group').append(error);
                            elem.closest('.form-group').addClass('has-error');
                        });
                    } else if (res.success && res.msg && res.data) {
                        if (res.mode == 'add') {
                            datatbl.row.add([0, res.data.name, res.data.state_name, res.data.country_name, res.data.statusButtons, res.data.actionButtons]).draw();
                            $('.changestatus[data-id="' + res.data.id + '"]').closest('tr').attr('id', 'row_' + res.data.id);
                        } else if (res.mode == 'edit') {
                            $('[data-id="' + res.data.id + '"]').closest('tr').find('td:nth-child(2)').text(res.data.name);
                            $('[data-id="' + res.data.id + '"]').closest('tr').find('td:nth-child(3)').text(res.data.short_name);
                        }
                        showMessage('success', {message: res.msg});
                        $('#modal-manage').modal('hide');
                    } else if (res.error) {
                        showMessage('error', {message: res.error});
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    showMessage('error', 'Internal error: ' + jqXHR.responseText);
                }
            });
        });

        $('#modal-manage').on('hidden.bs.modal', function (e) {
            $('.form-group .help-block').remove();
            $('.form-group').removeClass('has-error');
            $('#manage-form').find('[name="id"],[name="name"]').val('');
            $('.modal-title').text('Add New City');
        });

        $(document).on('click', 'a.edit-row', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $.trim($(this).closest('tr').find('td:nth-child(2)').text());
            var state_id = $.trim($(this).closest('tr').find('td:nth-child(3)').find('span').data('stateid'));
            var country_id = $.trim($(this).closest('tr').find('td:nth-child(4)').find('span').data('countryid'));
            $('#manage-form').find('[name="name"]').val(name);
            $('#manage-form').find('[name="id"]').val(id);
            $('#country').val(country_id).trigger('change');
            loadStates(state_id);
            $('.modal-title').text('Edit City');
            $('#modal-manage').modal('show');
        });
    });
    function loadStates(selected) {
        $.ajax({
            url: <?php echo json_encode(site_url('admin/cities/state_dropdown')); ?>,
            type: "POST",
            data: {id: $('#country').val()},
            success: function (response)
            {
                $("[name='state_id']").empty();
                $.each(response, function (key, value) {
                    $("[name='state_id']").append(value);
                });
                if (selected && selected != "") {
                    $('#state').val(selected).trigger('change');
                }
            }
        });
    }
</script>