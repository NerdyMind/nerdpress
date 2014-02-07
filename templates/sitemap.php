<div class="row">
	<div class="col-sm-6">
		<h2>Pages</h2>
		
		<ul>
		<?php
		$page_args = array(
			'post_type' 	=> 'page',
			'order' 			=> 'ASC',
			'orderby' 		=> 'title',
			'posts_per_page' => -1,
			'meta_query' 	=> array(
				array(
					'key' 		=> 'nrd_hide_sitemap',
					'value' 	=> '1',
					'compare' => '!=',
				)
			),
		);
		
		$page_query = new WP_Query( $page_args );
		
		if ( $page_query->have_posts() ) :
		
			while ( $page_query->have_posts() ) : $page_query->the_post();
		?>
			<li>
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
			</li>
		<?php
			endwhile; wp_reset_query();
		
		endif;
		?>
		</ul>
	</div><!-- /.col-sm-6 -->
	
	<div class="col-sm-6">
	
		<h2>Posts</h2>
		
		<ul>
			<?php wp_list_categories('title_li=&hierarchical=0&show_count=1'); ?>
		</ul>
	
	</div><!-- /.col-sm-6 -->
</div><!-- /.row -->

<h2>Monthly Archives</h2>

<ul>
	<?php wp_get_archives('type=monthly&limit=12'); ?>
</ul>