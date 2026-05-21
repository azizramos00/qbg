<?php
/**
 * Title: Program section grid
 * Slug: queens-botanical-block/program-section-grid
 * Categories: queens-botanical-block, programs, featured
 * Keywords: program, grid, cards, listing
 * Block Types: core/group
 * Viewport Width: 1344
 *
 * Three-column program card row (Figma Kids Programs card grid).
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"site-program-section-grid site-container\u002d\u002dfull-width","style":{"spacing":{"margin":{"top":"var:preset|spacing|l","bottom":"var:preset|spacing|l"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group site-program-section-grid site-container--full-width" style="margin-top:var(--wp--preset--spacing--l);margin-bottom:var(--wp--preset--spacing--l)">
	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading"><?php esc_html_e( 'Programs', 'queens-botanical-block' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|m"},"margin":{"top":"var:preset|spacing|m"}}}} -->
	<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--m)">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:pattern {"slug":"queens-botanical-block/program-card"} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:pattern {"slug":"queens-botanical-block/program-card"} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:pattern {"slug":"queens-botanical-block/program-card"} /-->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
