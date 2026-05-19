<?php
/**
 * Title: Call to action card
 * Slug: queens-botanical-block/call-to-action-card
 * Categories: queens-botanical-block
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$qbb_cta_image_url = esc_url( get_template_directory_uri() . '/assets/images/cta/call-to-action-membership.jpg' );
?>
<!-- wp:group {"className":"site-container\u002d\u002dfull-width callAction\u002d\u002dcard","style":{"spacing":{"margin":{"top":"var(--qbb-space-l)","bottom":"var(--qbb-space-m)"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group site-container--full-width callAction--card" style="margin-top:var(--qbb-space-l);margin-bottom:var(--qbb-space-m)"><!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|m","left":"var:preset|spacing|m","top":"var:preset|spacing|m","bottom":"var:preset|spacing|m"}},"color":{"background":"#faf7e1"},"border":{"radius":{"topLeft":"24px","topRight":"24px","bottomLeft":"24px","bottomRight":"24px"}}},"layout":{"type":"grid","columnCount":12,"minimumColumnWidth":null}} -->
<div class="wp-block-group has-background" style="border-top-left-radius:24px;border-top-right-radius:24px;border-bottom-left-radius:24px;border-bottom-right-radius:24px;background-color:#faf7e1;padding-top:var(--wp--preset--spacing--m);padding-right:var(--wp--preset--spacing--m);padding-bottom:var(--wp--preset--spacing--m);padding-left:var(--wp--preset--spacing--m)"><!-- wp:group {"style":{"layout":{"columnSpan":5}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|peony"}}}},"textColor":"peony"} -->
<h2 class="wp-block-heading has-peony-color has-text-color has-link-color">Become a member</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Free admission year-round and entry on select events, 10% off at Queens Botanical Garden store, Members-only events, reciprocal benefits at gardens nationwide, and more!</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"peony","className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-peony-background-color has-background wp-element-button">Sign up Today</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"callAction\u002d\u002dmedia-asset","style":{"layout":{"columnSpan":7}},"layout":{"type":"default"}} -->
<div class="wp-block-group callAction--media-asset"><!-- wp:uagb/image {"block_id":"29d41216","url":"<?php echo esc_url( $qbb_cta_image_url ); ?>","urlTablet":"<?php echo esc_url( $qbb_cta_image_url ); ?>","urlMobile":"<?php echo esc_url( $qbb_cta_image_url ); ?>","linkDestination":"none","title":"Queens Botanical Garden","naturalWidth":3070,"naturalHeight":2048,"sizeSlug":"large","sizeSlugTablet":"large","sizeSlugMobile":"large","imageBorderTopLeftRadius":16,"imageBorderTopRightRadius":16,"imageBorderBottomLeftRadius":16,"imageBorderBottomRightRadius":16,"style":{"layout":{"columnSpan":7}}} -->
<div class="wp-block-uagb-image uagb-block-29d41216 wp-block-uagb-image--layout-default wp-block-uagb-image--effect-static wp-block-uagb-image--align-none"><figure class="wp-block-uagb-image__figure"><img src="<?php echo esc_url( $qbb_cta_image_url ); ?>" alt="Visitors at Queens Botanical Garden" class="uag-image-qbb-cta-membership" width="1024" height="683" loading="lazy" role="img"/></figure></div>
<!-- /wp:uagb/image --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
