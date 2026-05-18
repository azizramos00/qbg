<?php
/**
 * Title: Featured Events/Sticky
 * Slug: queens-botanical-block/featured-events-sticky
 * Categories: queens-botanical-block, featured-events
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"site-container\u002d\u002dfull-width","style":{"spacing":{"margin":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"},"padding":{"top":"var:preset|spacing|xxl","bottom":"var:preset|spacing|xxl"}}},"layout":{"type":"grid","columnCount":12,"minimumColumnWidth":null}} -->
<div class="wp-block-group site-container--full-width" style="margin-top:var(--wp--preset--spacing--xxl);margin-bottom:var(--wp--preset--spacing--xxl);padding-top:var(--wp--preset--spacing--xxl);padding-bottom:var(--wp--preset--spacing--xxl)"><!-- wp:group {"style":{"layout":{"columnSpan":4}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"className":"featured-events-title\u002d\u002dsticky","style":{"elements":{"link":{"color":{"text":"var:preset|color|mallow"}}},"layout":{"columnSpan":4}},"textColor":"mallow"} -->
<h2 class="wp-block-heading featured-events-title--sticky has-mallow-color has-text-color has-link-color">Featured <br>Events</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"layout":{"columnSpan":8},"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-right:0;padding-left:0"><!-- wp:query {"queryId":33,"query":{"perPage":3,"pages":0,"offset":0,"postType":"page","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[73]}},"metadata":{"categories":["posts"],"patternName":"core/query-medium-posts","name":"Image at left"}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"1","style":{"border":{"radius":{"topLeft":"16px","topRight":"16px","bottomLeft":"16px","bottomRight":"16px"}}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:post-title {"level":3,"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|rose"}}}},"textColor":"rose"} /-->

<!-- wp:post-excerpt {"moreText":"Learn more"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
