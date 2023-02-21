<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>AdminLTE 3 | Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
     
    <!-- Google Font: Source Sans Pro -->
    <?php view::tagCss('fonts/google/sourceSansPro')  ?>
    
    <!-- Font Awesome Icons -->    
    <?php view::tagPluging('plugins/fontawesome-free/css/all')  ?>
   
    <!-- Ionicons 2.0.0  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
    <?php view::tagCss('fonts/ionicons/ionicons.min')  ?>
     <?php view::tagCss('fonts/font-awesome-4.5.0/css/font-awesome.min')  ?>
     <!-- Theme style -->
    <?php view::tagCss('dist/css/adminlte')  ?>
    
    
    <!-- jquery-ui -->    
    <?php //view::tagCssPublic('jquery-ui.theme')  ?>
    
    <!-- Bootstrap 3.3.4 -->
     <?php //view::tagCss('bootstrap')  ?>
    
 
    <?php if(isset($_layoutParams['css']) && count($_layoutParams['css'])):?>
    <?php for($i = 0; $i < count($_layoutParams['css']);$i++): ?>
           <link href="<?php echo $_layoutParams['css'][$i] ?>" rel="stylesheet" type="text/css">
    <?php endfor ?>
    <?php endif ?>
                
            
  </head>
  <body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php view::partial('barra', $_layoutParams) ?>
      
      <!-- Left side column. contains the logo and sidebar -->
      <?php view::partial('menu', $_layoutParams) ?>
		
      


		<!-- Content Wrapper. Contains page content -->
		  <div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
			  <div class="container-fluid">
				<div class="row mb-2">
				  <div class="col-sm-6">
					<h1 class="m-0"><?php if(isset($this->title))echo ucfirst($this->title) ?></h1>
				  </div><!-- /.col -->
				  <div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
					  <li class="breadcrumb-item"><a href="#">Home</a></li>
					  <li class="breadcrumb-item active">Dashboard v3</li>
					</ol>
				  </div><!-- /.col -->
				</div><!-- /.row -->
			  </div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<div class="content">
				<div class="container-fluid">
						<?php if(isset($this->error)): ?>
						<li class="alert alert-danger alert-dismissible " role="alert">
							 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<i class="fa fa-exclamation"></i> <?php echo $this->error; ?>
						</li>
					<?php endif; ?>
					<?php if(isset($this->mensaje)): ?>
						 <li class="alert alert-success alert-dismissible " role="alert">
							 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<i class="fa fa-comments"></i> <?php echo $this->mensaje; ?>
						</li>
					<?php endif; ?>
					<?php if(isset($this->info)): ?>
						 <li class="alert alert-warning alert-dismissible " role="alert">
							 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<i class="fa fa-info"></i> <?php echo $this->info; ?>
						</li>
					<?php endif; ?>









        
            	







