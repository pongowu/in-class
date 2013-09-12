<aside id="sidebar">
	<section class="widget">
		<h3>Shop by Brand:</h3>
		<ul>
			<?php 
			// add 'shop' to get_sidebar()
			wp_list_categories( array(
				'taxonomy' => 'brand',
				'title_li' => '',
				'show_count' => true,
			) ); ?>
		</ul>
	</section>	

	<section class="widget">
		<h3>Shop by Feature:</h3>
		<ul>
			<?php 
			// add 'shop' to get_sidebar()
			wp_list_categories( array(
				'taxonomy' => 'feature',
				'title_li' => '',
				'show_count' => true, // show how many products are in each
			) ); ?>
		</ul>
	</section>	

</aside>	