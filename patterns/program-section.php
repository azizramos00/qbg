<?php
/**
 * Title: Program section
 * Slug: queens-botanical-block/program-section
 * Categories: queens-botanical-block, programs, featured
 * Keywords: program, section, kids, detail
 * Block Types: core/group
 * Viewport Width: 921
 *
 * Single program detail block (image, eyebrow, title, description).
 * Synced from the Kids Programs page editor layout; use inside Programs template post content.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"site-program-section","style":{"spacing":{"margin":{"top":"var:preset|spacing|l","bottom":"var:preset|spacing|l"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group site-program-section" style="margin-top:var(--wp--preset--spacing--l);margin-bottom:var(--wp--preset--spacing--l)">
	<!-- wp:group {"layout":{"type":"default"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top","justifyContent":"space-between"}} -->
		<div class="wp-block-group">
			<!-- wp:image {"aspectRatio":"3/2","scale":"cover","sizeSlug":"large","linkDestination":"none","className":"site-program-section__media","style":{"border":{"radius":{"topLeft":"16px","topRight":"16px","bottomLeft":"16px","bottomRight":"16px"}},"layout":{"selfStretch":"fill","flexSize":null}}} -->
			<figure class="wp-block-image size-large site-program-section__media"><img src="https://s.w.org/images/core/5.8/outdoors.webp" alt="" style="border-top-left-radius:16px;border-top-right-radius:16px;border-bottom-left-radius:16px;border-bottom-right-radius:16px;aspect-ratio:3/2;object-fit:cover"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"site-program-section__intro","style":{"spacing":{"blockGap":"0","margin":{"top":"var:preset|spacing|s"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group site-program-section__intro" style="margin-top:var(--wp--preset--spacing--s)">
			<!-- wp:paragraph {"className":"site-program-section__eyebrow","fontSize":"small"} -->
			<p class="site-program-section__eyebrow has-small-font-size"><?php esc_html_e( 'For toddlers ages 2 & 3 yrs old', 'queens-botanical-block' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php esc_html_e( 'Garden Buds', 'queens-botanical-block' ); ?></h3>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|s"}}}} -->
		<p style="margin-top:var(--wp--preset--spacing--s)"><?php esc_html_e( 'Tailored to our youngest gardeners (ages 2 and 3), we invite you to connect with your little one to explore the wonder of nature through hands-on work and play in the garden. Together, you’ll harvest garden produce, create botanical crafts, and share stories and songs.', 'queens-botanical-block' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
