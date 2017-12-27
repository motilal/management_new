<div class="row">
    <div class="col-xs-12"> 
        <div class="box">
            <div class="box-header">
                <i class="fa fa-globe"></i> 
                <h3 class="box-title"><?php echo isset($pageHeading) ? $pageHeading : '&nbsp;'; ?></h3>
                <div class="box-tools pull-right">
                    <div class="btn-group" data-toggle="btn-toggle">
                        <a href="#" data-toggle="modal" data-target="#modal-manage" class="btn btn-primary btn-sm add_new_item"><i class="fa fa-plus"></i> Add New Country </a>
                        <a href="<?php echo site_url('admin/countries?download=report'); ?>" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Export CSV</a>
                    </div>
                </div>
            </div>     
            <!-- /.box-header -->
            <div class="box-body"> 
                <table id="dataTables-grid" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <td>Sr.</td>
                            <th>Name</th>
                            <th>Short Name</th> 
                            <th>Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows() > 0) { ?>
                            <?php foreach ($result->result() as $key => $row): ?>
                                <tr id="row_<?php echo $row->id; ?>">
                                    <td><?php echo $key + 1; ?></td>
                                    <td><?php echo $row->name; ?></td>
                                    <td><?php echo $row->short_name; ?></td>
                                    <td>
                                        <?php echo $this->layout->element('admin/element/_module_status', array('status' => $row->status, 'id' => $row->id, 'url' => "admin/countries/changestatus"), true); ?>
                                    </td>
                                    <td>  
                                        <?php echo $this->layout->element('admin/element/_module_action', array('id' => $row->id, 'editUrl' => "admin/countries/manage", 'deleteUrl' => 'admin/countries/delete'), true); ?>
                                    </td>  
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
            <?php echo form_open('admin/countries/manage', array("id" => "manage-form", "method" => "post")); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Country</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group <?php echo!empty(form_error('name')) ? 'has-error' : ''; ?>">
                            <label class="control-label" for="name">Country Name <em>*</em></label>
                            <?php echo form_input("name", '', "id='name' class='form-control'"); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="short_name">Short Name</label>
                            <?php echo form_input("short_name", '', "id='short_name' class='form-control'"); ?>
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
    /*
     params 
     1 sorting remove from colomns
     2 default sort order of colomn set default []
     3 default paging
     4 show sr. number or not
     */
    var datatbl = datatable_init([0, 4], [[1, 'asc']], DEFAULT_PAGING, 1);

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
                        datatbl.row.add([0, res.data.name, res.data.short_name, res.data.statusButtons, res.data.actionButtons]).draw();
                        $('.changestatus[data-id="' + res.data.id + '"]').closest('tr').attr('id', 'row_' + res.data.id);
                    } else if (res.mode == 'edit') {
                        $('#row_' + res.data.id).find('td:nth-child(2)').text(res.data.name);
                        $('#row_' + res.data.id).find('td:nth-child(3)').text(res.data.short_name);
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
        $('#manage-form')[0].reset();
        $('#manage-form').find('[name="id"]').val('');
        $('.modal-title').text('Add New Country');
    });

    $(document).on('click', 'a.edit-row', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $.trim($(this).closest('tr').find('td:nth-child(2)').text());
        var short_name = $.trim($(this).closest('tr').find('td:nth-child(3)').text());
        $('#manage-form').find('[name="name"]').val(name);
        $('#manage-form').find('[name="short_name"]').val(short_name);
        $('#manage-form').find('[name="id"]').val(id);
        $('.modal-title').text('Edit Country');
        $('#modal-manage').modal('show');
    });

</script>