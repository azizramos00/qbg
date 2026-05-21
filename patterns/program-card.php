<?php
/**
 * Title: Program card
 * Slug: queens-botanical-block/program-card
 * Categories: queens-botanical-block, programs, featured
 * Keywords: program, card, tour, kids
 * Block Types: core/group
 * Viewport Width: 451
 *
 * Figma: Cards → Card Border → Variant=Program (1110:15564).
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"is-style-qbb-program-card","style":{"spacing":{"blockGap":"0","padding":{"top":"12px","right":"12px","bottom":"12px","left":"12px"}},"color":{"background":"var:preset|color|base"},"border":{"color":"#0000001a","width":"1px","radius":"16px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-qbb-program-card has-base-background-color has-background" style="border-color:#0000001a;border-width:1px;border-top-left-radius:16px;border-top-right-radius:16px;border-bottom-left-radius:16px;border-bottom-right-radius:16px;padding-top:12px;padding-right:12px;padding-bottom:12px;padding-left:12px">
	<!-- wp:image {"aspectRatio":"451/230","scale":"cover","sizeSlug":"large","linkDestination":"none","className":"qbb-program-card__media","style":{"border":{"radius":"8px"}}} -->
	<figure class="wp-block-image size-large qbb-program-card__media"><img src="https://s.w.org/images/core/5.8/outdoors.webp" alt="" style="border-radius:8px"/></figure>
	<!-- /wp:image -->

	<!-- wp:group {"className":"qbb-program-card__body","style":{"spacing":{"padding":{"top":"24px","right":"12px","bottom":"24px","left":"12px"},"blockGap":"10px"}},"layout":{"type":"default"}} -->
	<div class="wp-block-group qbb-program-card__body">
		<!-- wp:group {"className":"qbb-program-card__meta","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
		<div class="wp-block-group qbb-program-card__meta">
			<!-- wp:paragraph {"className":"qbb-program-card__label"} -->
			<p class="qbb-program-card__label"><?php esc_html_e( 'Tour', 'queens-botanical-block' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"qbb-program-card__price"} -->
			<p class="qbb-program-card__price"><?php esc_html_e( '$250', 'queens-botanical-block' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:heading {"level":3,"className":"qbb-program-card__title","style":{"typography":{"fontSize":"1.5625rem","lineHeight":"1.28"}}} -->
		<h3 class="wp-block-heading qbb-program-card__title" style="font-size:1.5625rem;line-height:1.28"><?php esc_html_e( 'Educational Resources', 'queens-botanical-block' ); ?></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"qbb-program-card__description","style":{"typography":{"fontSize":"1.25rem","lineHeight":"1.4"}}} -->
		<p class="qbb-program-card__description" style="font-size:1.25rem;line-height:1.4"><?php esc_html_e( 'Use our resource library as a starting place for answering your questions on composting processes, bins, gardening tips, and more.', 'queens-botanical-block' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-qbb-program-card-link"} -->
			<div class="wp-block-button is-style-qbb-program-card-link"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Learn more', 'queens-botanical-block' ); ?> →</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
