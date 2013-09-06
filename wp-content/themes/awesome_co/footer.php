</div><!-- end wrapper -->

<footer class="clearfix" id="colophon" role="contentinfo">

	<?php dynamic_sidebar('footer-area'); // this is a widget area ?>

    <div class="widget-container">        
        &copy; 2013 <?php bloginfo('name'); ?>        
    </div>
    
    <?php wp_nav_menu( array( 
			'theme_location' => 'footer_menu',
			'container' => 'nav', // the theme location is the area you made in admin, and container lets you set what html tag you need.	
		 ) ); ?>
	 
</footer><!-- end footer -->

<?php 
//must call wp_footer right before </body> for JS and plugins to run!
wp_footer();  ?>
</body>
</html>