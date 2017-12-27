<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>   
<div class="action-link">
    <?php if (isset($editUrl)) { ?>
        <a href="<?php echo site_url($editUrl); ?>" data-id="<?php echo $id; ?>" class="btn btn-block btn-warning btn-xs edit-row" data-toggle="tooltip" title="" data-original-title="Edit"> <span class="fa fa-edit"></span> </a>
    <?php } ?>
    <?php if (isset($deleteUrl)) { ?>
        <a href="<?php echo site_url($deleteUrl); ?>" data-id="<?php echo $id; ?>" class="btn btn-block btn-danger btn-xs delete-row"  data-toggle="tooltip" title="" data-original-title="Delete"> <span class="fa fa-trash-o"></span> </a>
    <?php } ?>
    <?php if (isset($viewUrl)) { ?>
        <a href="<?php echo site_url($viewUrl); ?>" data-id="<?php echo $id; ?>" class="btn btn-block btn-primary btn-xs view-row"  data-toggle="tooltip" title="" data-original-title="View"> <span class="fa fa-eye"></span> </a>
    <?php } ?>
</div> 