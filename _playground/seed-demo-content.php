<?php
/**
 * Seeds Playground demo pages, posts, and events. Loaded by blueprint runPHP.
 *
 * @package Queens_Botanical_Block
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * Insert or fetch a published page by slug.
 *
 * @param string $slug    Post name.
 * @param string $title   Post title.
 * @param string $content Block markup.
 * @return int Post ID or 0 on failure.
 */
function qbb_playground_upsert_page( string $slug, string $title, string $content ): int {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing instanceof WP_Post ) {
		return (int) $existing->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		),
		true
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Insert or fetch a published post by slug.
 *
 * @param string $slug    Post name.
 * @param string $title   Post title.
 * @param string $content Block markup.
 * @return int Post ID or 0 on failure.
 */
function qbb_playground_upsert_post( string $slug, string $title, string $content ): int {
	$existing = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing[0] ) ) {
		return (int) $existing[0];
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		),
		true
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Create a The Events Calendar event if the plugin is available.
 *
 * @param string $slug    Post name.
 * @param string $title   Event title.
 * @param string $content Description.
 * @param string $start   Start datetime (Y-m-d H:i:s).
 * @param string $end     End datetime.
 * @return int Event post ID or 0.
 */
function qbb_playground_upsert_event(
	string $slug,
	string $title,
	string $content,
	string $start,
	string $end
): int {
	$existing = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'tribe_events',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing[0] ) ) {
		return (int) $existing[0];
	}

	if ( function_exists( 'tribe_create_event' ) ) {
		$id = tribe_create_event(
			array(
				'post_title'     => $title,
				'post_content'   => $content,
				'post_status'    => 'publish',
				'post_name'      => $slug,
				'EventStartDate' => $start,
				'EventEndDate'   => $end,
			)
		);

		return is_wp_error( $id ) ? 0 : (int) $id;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'tribe_events',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		),
		true
	);

	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	update_post_meta( $id, '_EventStartDate', $start );
	update_post_meta( $id, '_EventEndDate', $end );
	update_post_meta( $id, '_EventTimezone', wp_timezone_string() );

	return (int) $id;
}

$home_id = qbb_playground_upsert_page(
	'home',
	'Home',
	'<!-- wp:cover {"overlayColor":"mallow","dimRatio":60,"isUserOverlayColor":true,"minHeight":420,"minHeightUnit":"px","align":"full"} -->
<div class="wp-block-cover alignfull"><span aria-hidden="true" class="wp-block-cover__background has-mallow-background-color has-background-dim-60 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"base","fontSize":"huge"} -->
<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color has-huge-font-size">Queens Botanical Garden</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"large"} -->
<p class="has-text-align-center has-base-color has-text-color has-large-font-size">Explore 39 acres of gardens, programs, and seasonal beauty in Flushing, Queens.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"rose","textColor":"base"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-rose-background-color has-text-color has-background wp-element-button" href="/about/">Plan your visit</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/events/">See events</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Visit the garden</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Stroll seasonal displays, family programs, and quiet paths through our living collections.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/about/">About the garden →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Upcoming events</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Workshops, tours, and celebrations for all ages.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/events/">Events calendar →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Garden stories</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>News, tips, and behind-the-scenes looks at what is growing at QBG.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/blog/">Read the blog →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->'
);

$blog_id = qbb_playground_upsert_page(
	'blog',
	'Blog',
	'<!-- wp:paragraph -->
<p>Garden news, seasonal tips, and stories from Queens Botanical Garden.</p>
<!-- /wp:paragraph -->'
);

qbb_playground_upsert_page(
	'about',
	'About',
	'<!-- wp:heading -->
<h2 class="wp-block-heading">Our mission</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Queens Botanical Garden is an urban oasis where people, plants, and cultures are celebrated through inspiring gardens, innovative programs, and displays of world-class horticulture.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>We welcome visitors year-round to explore collections, attend programs, and connect with nature in the heart of Queens.</p>
<!-- /wp:paragraph -->'
);

qbb_playground_upsert_page(
	'contact',
	'Contact',
	'<!-- wp:heading -->
<h2 class="wp-block-heading">Get in touch</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><strong>Queens Botanical Garden</strong><br>43-50 Main Street<br>Flushing, NY 11355</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Phone:</strong> (718) 886-3800<br><strong>Email:</strong> info@queensbotanical.org</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>Hours vary by season. Visit our homepage for today&#8217;s garden hours.</p>
<!-- /wp:paragraph -->'
);

qbb_playground_upsert_page(
	'garden-programs',
	'Programs & Events',
	'<!-- wp:heading -->
<h2 class="wp-block-heading">Programs &amp; events</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>From guided tours to family workshops, there is always something happening at the garden. Browse featured events on the calendar or join us for seasonal celebrations.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="/events/">View the events calendar →</a></p>
<!-- /wp:paragraph -->'
);

qbb_playground_upsert_event(
	'spring-garden-tour',
	'Spring Garden Tour',
	'<!-- wp:paragraph --><p>Join a guided walk through spring blooms and learn about seasonal plantings across the garden.</p><!-- /wp:paragraph -->',
	'2026-06-15 10:00:00',
	'2026-06-15 12:00:00'
);

qbb_playground_upsert_event(
	'family-nature-walk',
	'Family Nature Walk',
	'<!-- wp:paragraph --><p>A gentle, family-friendly exploration of wildlife habitats and pollinator plants.</p><!-- /wp:paragraph -->',
	'2026-07-20 11:00:00',
	'2026-07-20 13:00:00'
);

$posts = array(
	array(
		'slug'    => 'spring-blooms-at-qbg',
		'title'   => 'Spring Blooms at QBG',
		'excerpt' => 'Cherry blossoms, tulips, and early perennials are putting on a show in the annual border.',
	),
	array(
		'slug'    => 'composting-basics-workshop-recap',
		'title'   => 'Composting Basics Workshop Recap',
		'excerpt' => 'Highlights from our community composting workshop and how to start at home.',
	),
	array(
		'slug'    => 'meet-the-bee-garden',
		'title'   => 'Meet the Bee Garden',
		'excerpt' => 'Why pollinator habitat matters and what you will find in our bee garden this season.',
	),
	array(
		'slug'    => 'summer-evening-strolls',
		'title'   => 'Summer Evening Strolls',
		'excerpt' => 'Longer days mean golden-hour walks—here is what to look for after 6 p.m.',
	),
	array(
		'slug'    => 'volunteer-spotlight-march',
		'title'   => 'Volunteer Spotlight: March',
		'excerpt' => 'Thank you to the volunteers who helped prepare beds and trails for the growing season.',
	),
);

foreach ( $posts as $post ) {
	qbb_playground_upsert_post(
		$post['slug'],
		$post['title'],
		sprintf(
			'<!-- wp:paragraph --><p>%s</p><!-- /wp:paragraph -->',
			esc_html( $post['excerpt'] )
		)
	);
}

if ( $home_id ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
}

if ( $blog_id ) {
	update_option( 'page_for_posts', $blog_id );
}

flush_rewrite_rules();
