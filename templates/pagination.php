<div class="text-center">
	<ul class="pagination">
		<?php foreach ( $page_links as $page ) : ?>
		<li class="<?= $page['class']; ?>">
			<a href="<?= $page['link']; ?>"><?= $page['text']; ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>