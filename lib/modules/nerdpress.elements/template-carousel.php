<?php
$carousel_id = 'carousel_' . rand(0, 101389492);

$fields_to_get = array(
	'carousel',
);

foreach ( $fields_to_get as $field ) {
	${ $field } = get_field( $field );
}

$s = 0;
?>
<div id="<?= $carousel_id; ?>" class="carousel slide">
  <!-- Indicators -->
  <ol class="carousel-indicators">
  <? while ( has_sub_field( 'carousel' ) ) : ?>
    <li data-target="#<?= $carousel_id; ?>" data-slide-to="<?= $s; ?>"<?= ( $s == 0 ? ' class="active"' : '' ); ?>></li>
  <? $s++; endwhile; ?>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <?php
    $s = 0;
    
    while ( has_sub_field( 'carousel' ) ) :
    	$subfields_to_get = array(
    		'slide_image',
    		'slide_caption',
    		'slide_link',
    	);
    	
    	foreach ( $subfields_to_get as $subfield ) {
			${ $subfield } = get_sub_field( $subfield );
		}
    ?>
    <div class="item<?= ( $s == 0 ? ' active' : '' ); ?>">
      <? if ( $slide_link ) : ?>
      <a href="<?= $slide_link; ?>">
      <? endif; ?>
      	<img src="<?= $slide_image; ?>" alt="<?= $slide_caption; ?>">
      <? if ( $slide_link ) : ?>
      </a>
      <? endif; ?>
      <div class="carousel-caption">
        <?= $slide_caption; ?>
      </div>
    </div>
    <? $s++; endwhile; ?>
    
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#<?= $carousel_id; ?>" data-slide="prev">
    <span class="icon-prev"></span>
  </a>
  <a class="right carousel-control" href="#<?= $carousel_id; ?>" data-slide="next">
    <span class="icon-next"></span>
  </a>
</div>