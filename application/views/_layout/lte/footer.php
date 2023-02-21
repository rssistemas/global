</div>
</div><!-- /.content-wrapper -->
</div>
<footer class="main-footer">
     <div class="pull-right hidden-xs">
		<b>Version</b> 1.0.0
     </div>
        <strong>Copyright &copy; 2018 <a href="#">RSSistemas</a>.</strong> All rights reserved.
</footer>

     <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
	  </aside><!-- /.control-sidebar -->
      
</div><!-- ./wrapper -->

        
           
    <!-- jQuery 2.1.4 -->
    <?php //view::tagJsPublic('jquery') ?>
 
    <!-- Bootstrap 3.3.2 JS -->
    <?php //view::tagJs('bootstrap') ?>
      
    <?php view::tagJs('plugins/jquery/jquery.min') ?>
    
    <?php view::tagJs('plugins/bootstrap/js/bootstrap.bundle.min') ?>
    
    <?php view::tagJs('dist/js/adminlte') ?>
    
    <?php //view::tagJs('plugins/chart.js/Chart.min') ?>
    
    <?php //view::tagJs('dist/js/demo') ?>
    
    <?php //view::tagJs('dist/js/pages/dashboard3') ?>
    
    
    <?php if(isset($_layoutParams['js']) && count($_layoutParams['js'])):?>
    <?php for($i = 0; $i < count($_layoutParams['js']);$i++): ?>
            <script src="<?php echo $_layoutParams['js'][$i] ?>" type="text/javascript"></script>
    <?php endfor ?>
    <?php endif ?>  
  </body>
</html>
