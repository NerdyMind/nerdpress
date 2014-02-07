<ul class="breadcrumb">
<?php
global $breadcrumbs;

foreach ( $breadcrumbs as $crumb => $data ) :
?>
	<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
		<?php if ( $data['url'] ) :?>
		<a href="<?= $data['url']; ?>">
		<?php endif; ?>
			<?= $data['text']; ?>
		<?php if ( $data['url'] ) : ?>
		</a>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>