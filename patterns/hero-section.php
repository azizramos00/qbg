<?php
/**
 * Title: Hero Section
 * Slug: queens-botanical-block/hero-section
 * Categories: featured, banner
 * Keywords: hero, banner, call to action
 * Block Types: core/template-part/header
 * Viewport Width: 1400
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:cover {"overlayColor":"mallow","dimRatio":60,"isUserOverlayColor":true,"minHeight":360,"minHeightUnit":"px","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}}} -->
<div class="wp-block-cover alignfull">
	<span aria-hidden="true" class="wp-block-cover__background has-mallow-background-color has-background-dim-60 has-background-dim"></span>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"textAlign":"center","level":1,"textColor":"base","fontSize":"huge"} -->
			<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color has-huge-font-size"><?php esc_html_e( 'Welcome', 'queens-botanical-block' ); ?></h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"large"} -->
			<p class="has-text-align-center has-base-color has-text-color has-large-font-size"><?php esc_html_e( 'Explore the garden and plan your visit.', 'queens-botanical-block' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"backgroundColor":"rose","textColor":"base"} -->
				<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-rose-background-color has-text-color has-background wp-element-button"><?php esc_html_e( 'Plan your visit', 'queens-botanical-block' ); ?></a></div>
				<!-- /wp:button -->

				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Learn more', 'queens-botanical-block' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
