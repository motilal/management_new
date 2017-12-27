<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo isset($title) ? $title : ''; ?></title> 
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"> 
        <link href="<?php echo base_url('asset/admin/plugin/bootstrape/css/bootstrap.min.css'); ?>" rel="stylesheet">  
        <link rel="stylesheet" href="<?php echo base_url('asset/admin/css/font-awesome.min.css'); ?>"> 
        <link rel="stylesheet" href="<?php echo base_url('asset/admin/css/ionicons.min.css'); ?>"> 
        <link rel="stylesheet" href="<?php echo base_url('asset/admin/css/admin.min.css'); ?>">   
        <script src="<?php echo base_url('asset/admin/js/jquery.min.js'); ?>"></script> 
        <script src="<?php echo base_url('asset/admin/plugin/bootstrape/js/bootstrap.min.js'); ?>"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="../../index2.html"><b>Admin</b>LTE</a>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                <?php echo $content_for_layout; ?>  
            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box --> 
    </body>
</html>
