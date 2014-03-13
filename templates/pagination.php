<?php
// Don't delete this, k?
global $page_links;
?>
<div class="text-center">
	<ul class="pagination">
		<?php foreach ( $page_links as $page ) : ?>
		<li class="<?= $page['class']; ?>">
			<?php if ( $page['link'] ) : ?>
			<a href="<?= $page['link']; ?>">
			<?php endif; ?>
				<?= $page['text']; ?>
			<?php if ( $page['link'] ) : ?>
			</a>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
</div>