<?php
wp_enqueue_script( 'nerdpress-social-share', get_template_directory_uri() . '/assets/js/plugins/nerdpress-social-share.js', array( 'jquery'), null, true );

$this_url = ( (!empty( $_SERVER['HTTPS'] ) ) ? "https://": "http://" ) . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$this_url = 'http://www.theverge.com/2014/3/12/5497728/mondo-and-the-lost-art-of-the-movie-poster';
$json = wp_remote_get(' http://api.sharedcount.com/?url=' . rawurlencode( $this_url ) . '&apikey=37433f776900024b3db1a402feba1b7d76fd62ae' );

if ( ! is_wp_error( $json ) ) $shared_counts = json_decode( $json['body'], true );
	
$social_title = urlencode( get_the_title() );
$social_url = urlencode( get_permalink() );
$social_summary = urlencode( get_the_excerpt() );
$social_image = urlencode( wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ) );
?>
<div class="nerdpress-social-share">
	<ul class="list-inline">
		<li>
			<a href="#share-facebook" class="btn btn-social btn-facebook" data-share="http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?= $social_title;?>&amp;p[summary]=<?= $social_summary;?>&amp;p[url]=<?= $social_url; ?>&amp;&p[images][0]=<?= $social_image;?>">
				<i class="fa fa-facebook fa-lg"></i> Share <span class="badge"><?= ( $shared_counts['Facebook']['share_count'] > 0 ) ? $shared_counts['Facebook']['share_count'] : ''; ?></span>
			</a>
		</li>
		
		<li>
			<a href="#share-twitter" class="btn btn-social btn-twitter" data-share="https://twitter.com/share?url=<?= $social_url; ?>">
				<i class="fa fa-twitter fa-lg"></i> Tweet <span class="badge"><?= ( $shared_counts['Twitter'] > 0 ) ? $shared_counts['Twitter'] : ''; ?></span>
			</a>
		</li>
		
		<li>
			<a href="#share-google-plus" class="btn btn-social btn-google-plus" data-share="https://plus.google.com/share?url=<?= $social_url; ?>">
				<i class="fa fa-google-plus fa-lg"></i> +1 <span class="badge"><?= ( $shared_counts['GooglePlusOne'] > 0 ) ? $shared_counts['GooglePlusOne'] : ''; ?></span>
			</a>
		</li>
		
		<li>
			<a href="#share-linked-in" class="btn btn-social btn-linkedin" data-share="http://www.linkedin.com/shareArticle?mini=true&url=<?= $social_url; ?>&title=<?= $social_title;?>&summary=<?= $social_summary;?>&source=<? bloginfo( 'name' ); ?>">
				<i class="fa fa-linkedin fa-lg"></i> Share <span class="badge"><?= ( $shared_counts['LinkedIn'] > 0 ) ? $shared_counts['LinkedIn'] : ''; ?></span>
			</a>
		</li>
	</ul>
</div>