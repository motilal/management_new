<?php
global $UserInfo;
$segment_cntr = $this->uri->segment(2);
$segment_fun = $this->uri->segment(3);

$subadminIndex = ($segment_cntr == 'subadmins' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$subadminAdd = ($segment_cntr == 'subadmins' && $segment_fun == 'add') ? 'active' : '';

$permissionIndex = ($segment_cntr == 'permissions' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$permissionAdd = ($segment_cntr == 'permissions' && $segment_fun == 'manage') ? 'active' : '';

$pageIndex = ($segment_cntr == 'pages' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$pageAdd = ($segment_cntr == 'pages' && $segment_fun == 'manage') ? 'active' : '';

$eventIndex = ($segment_cntr == 'events' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$eventAdd = ($segment_cntr == 'events' && $segment_fun == 'manage') ? 'active' : '';

$countryIndex = ($segment_cntr == 'countries' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$countryAdd = ($segment_cntr == 'countries' && $segment_fun == 'manage') ? 'active' : '';

$stateIndex = ($segment_cntr == 'states' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$stateAdd = ($segment_cntr == 'states' && $segment_fun == 'manage') ? 'active' : '';

$cityIndex = ($segment_cntr == 'cities' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$cityAdd = ($segment_cntr == 'cities' && $segment_fun == 'manage') ? 'active' : '';

$settingIndex = ($segment_cntr == 'settings' && ($segment_fun == 'index' || $segment_fun == '')) ? 'active' : '';
$settingProfile = ($segment_cntr == 'settings' && $segment_fun == 'profile') ? 'active' : '';

$user_permissions = $this->session->userdata('_subadmin_module_permissions');
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo gravatar_url($UserInfo->email); ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info"> 
                <p><?php echo $UserInfo->first_name . ' ' . $UserInfo->last_name ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div> 
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li class="<?php echo $segment_cntr == 'dashboard' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('admin/dashboard'); ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span> 
                </a>
            </li>

            <?php if (is_allow_admin(FALSE)) { ?>
                <li class="treeview <?php echo $segment_cntr == 'subadmins' ? 'active menu-open' : ''; ?>">
                    <a href="#">
                        <i class="fa fa-user-secret"></i>
                        <span>SubAdmin Manager</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" style="display:<?php echo $segment_cntr == 'subadmins' ? 'block' : 'none'; ?>;">
                        <li class="<?php echo $subadminIndex; ?>"><a href="<?php echo site_url('admin/subadmins'); ?>"><i class="fa fa-th-list"></i> Manage SubAdmins</a></li>
                        <li class="<?php echo $subadminAdd; ?>"><a href="<?php echo site_url('admin/subadmins/add'); ?>"><i class="fa fa-plus"></i> Add New SubAdmins</a></li> 
                    </ul>
                </li>
            <?php } ?>

            <?php if (is_allow_admin(FALSE)) { ?>
                <li class="treeview <?php echo $segment_cntr == 'permissions' ? 'active menu-open' : ''; ?>">
                    <a href="#">
                        <i class="fa fa-lock"></i>
                        <span>Permission Manager</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" style="display:<?php echo $segment_cntr == 'permissions' ? 'block' : 'none'; ?>;">
                        <li class="<?php echo $permissionIndex; ?>"><a href="<?php echo site_url('admin/permissions'); ?>"><i class="fa fa-th-list"></i> Manage Permissions</a></li>
                        <li class="<?php echo $permissionAdd; ?>"><a href="<?php echo site_url('admin/permissions/manage'); ?>"><i class="fa fa-plus"></i> Add New Permission</a></li> 
                    </ul>
                </li>
            <?php } ?>

            <?php if (is_allow_module('page')) { ?>
                <li class="treeview <?php echo $segment_cntr == 'pages' ? 'active menu-open' : ''; ?>">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span>Static Pages</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" style="display:<?php echo $segment_cntr == 'pages' ? 'block' : 'none'; ?>;">
                        <?php if (is_allow_action('page-index')) { ?>
                            <li class="<?php echo $pageIndex; ?>"><a href="<?php echo site_url('admin/pages'); ?>"><i class="fa fa-th-list"></i> Manage Pages</a></li>
                        <?php } ?>
                        <?php if (is_allow_action('page-add')) { ?>
                            <li class="<?php echo $pageAdd; ?>"><a href="<?php echo site_url('admin/pages/manage'); ?>"><i class="fa fa-plus"></i> Add New Page</a></li> 
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

            <li class="treeview <?php echo $segment_cntr == 'events' ? 'active menu-open' : ''; ?>">
                <a href="#">
                    <i class="fa fa-calendar"></i>
                    <span>Event Manager</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display:<?php echo $segment_cntr == 'events' ? 'block' : 'none'; ?>;">
                    <li class="<?php echo $eventIndex; ?>"><a href="<?php echo site_url('admin/events'); ?>"><i class="fa fa-th-list"></i> Manage Events</a></li>
                    <li class="<?php echo $eventAdd; ?>"><a href="<?php echo site_url('admin/events/manage'); ?>"><i class="fa fa-plus"></i> Add New Event</a></li> 
                </ul>
            </li>
            <li class="treeview <?php echo in_array($segment_cntr, array('countries', 'states', 'cities')) ? 'active menu-open' : ''; ?>">
                <a href="#">
                    <i class="fa fa-globe"></i> <span>Location</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo $countryIndex; ?>"><a href="<?php echo site_url('admin/countries'); ?>"><i class="fa fa-th-list"></i> Manage Country</a></li>
                    <li class="<?php echo $stateIndex; ?>"><a href="<?php echo site_url('admin/states'); ?>"><i class="fa fa-th-list"></i> Manage State</a></li>
                    <li class="<?php echo $cityIndex; ?>"><a href="<?php echo site_url('admin/cities'); ?>"><i class="fa fa-th-list"></i> Manage City</a></li>
                </ul>
            </li>

            <li class="<?php echo $segment_cntr == 'galleries' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('admin/galleries'); ?>">
                    <i class="fa fa-image"></i> <span>Gallery</span> 
                </a>
            </li>
            <li class="<?php echo $segment_cntr == 'email_templates' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('admin/email_templates'); ?>">
                    <i class="fa fa-envelope-o"></i> <span>Email Templates</span> 
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>