<?php
/**
 * Title: Garden card
 * Slug: queens-botanical-block/garden-card
 * Categories: queens-botanical-block, featured
 * Keywords: card, image, teaser, garden
 * Block Types: core/group
 * Viewport Width: 400
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"wide","className":"is-style-qbb-garden-card","style":{"spacing":{"blockGap":"0"},"color":{"background":"var:preset|color|base"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide is-style-qbb-garden-card has-base-background-color has-background">
	<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"large","linkDestination":"none","className":"qbb-garden-card__media"} -->
	<figure class="wp-block-image size-large qbb-garden-card__media"><img src="https://s.w.org/images/core/5.8/outdoors.webp" alt="" /></figure>
	<!-- /wp:image -->

	<!-- wp:group {"className":"qbb-garden-card__body","style":{"spacing":{"padding":{"top":"var:preset|spacing|m","bottom":"var:preset|spacing|m","left":"var:preset|spacing|m","right":"var:preset|spacing|m"},"blockGap":"var:preset|spacing|s"}},"layout":{"type":"default"}} -->
	<div class="wp-block-group qbb-garden-card__body">
		<!-- wp:paragraph {"className":"qbb-garden-card__eyebrow"} -->
		<p class="qbb-garden-card__eyebrow"><?php esc_html_e( 'In the garden', 'queens-botanical-block' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":3,"fontSize":"x-large"} -->
		<h3 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Card title', 'queens-botanical-block' ); ?></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size"><?php esc_html_e( 'Short supporting copy for this card. Replace image and text in the editor.', 'queens-botanical-block' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Learn more', 'queens-botanical-block' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
