<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?> 
<?php if ($status == '1') { ?>
    <a class="label label-success changestatus" data-id="<?php echo $id; ?>" data-status="1" href="<?php echo site_url($url); ?>">Active</a>
<?php } else { ?>
    <a class="label label-danger changestatus" data-id="<?php echo $id; ?>" data-status="0" href="<?php echo site_url($url); ?>">Inactive</a> 
<?php } ?>