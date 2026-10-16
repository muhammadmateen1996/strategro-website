<?php
/**
 * Search essentials for a news site. If an SEO plugin (Rank Math, Yoast,
 * All in One SEO, SEOPress, The SEO Framework) is active, it handles titles,
 * descriptions and share tags, and the theme only adds what plugins don't:
 * the Google News sitemap, indexing rules for thin pages and author fields.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tv_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

function tv_meta_description() {
	if ( is_singular() ) {
		return wp_strip_all_tags( get_the_excerpt( get_queried_object() ) );
	}
	if ( is_category() || is_tag() ) {
		$text = wp_strip_all_tags( term_description() );
		/* translators: %s: category */
		return $text ? $text : sprintf( __( 'The latest %s news and analysis from TickerVelocity.', 'tickervelocity' ), single_term_title( '', false ) );
	}
	return get_bloginfo( 'description' );
}

add_action( 'wp_head', 'tv_seo_head', 3 );
function tv_seo_head() {
	if ( tv_seo_plugin_active() ) {
		return;
	}
	$description = trim( wp_trim_words( tv_meta_description(), 32, '…' ) );
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image       = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'tv-lead' ) : '';

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
	$tags = array(
		'og:site_name'   => get_bloginfo( 'name' ),
		'og:type'        => is_singular( 'post' ) ? 'article' : 'website',
		'og:title'       => $title,
		'og:description' => $description,
		'og:url'         => $url,
		'og:image'       => $image,
		'twitter:card'   => $image ? 'summary_large_image' : 'summary',
	);
	if ( is_singular( 'post' ) ) {
		$tags['article:published_time'] = get_the_date( 'c' );
		$tags['article:modified_time']  = get_the_modified_date( 'c' );
	}
	foreach ( $tags as $property => $content ) {
		if ( $content ) {
			$attr = 0 === strpos( $property, 'twitter:' ) ? 'name' : 'property';
			printf( '<meta %1$s="%2$s" content="%3$s">' . "\n", esc_attr( $attr ), esc_attr( $property ), esc_attr( $content ) );
		}
	}
}

/** NewsArticle and breadcrumb data on articles; Organization and WebSite on the homepage. */
add_action( 'wp_head', 'tv_schema', 20 );
function tv_schema() {
	if ( tv_seo_plugin_active() ) {
		return;
	}
	$publisher = array(
		'@type' => 'NewsMediaOrganization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);
	$logo      = has_custom_logo() ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : get_site_icon_url( 512 );
	if ( $logo ) {
		$publisher['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $logo,
		);
	}
	$graph     = array();

	if ( is_front_page() ) {
		$same_as = array_values( array_filter( array( tv_mod( 'tv_x_url' ), tv_mod( 'tv_linkedin_url' ) ) ) );
		$graph[] = array_merge(
			$publisher,
			array(
				'sameAs'                => $same_as,
				'ethicsPolicy'          => tv_page_url( 'editorial-standards' ),
				'correctionsPolicy'     => tv_page_url( 'editorial-standards' ) . '#corrections',
				'publishingPrinciples'  => tv_page_url( 'editorial-standards' ),
			)
		);
		$graph[] = array(
			'@type'           => 'WebSite',
			'name'            => get_bloginfo( 'name' ),
			'url'             => home_url( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={query}' ),
				'query-input' => 'required name=query',
			),
		);
	}

	if ( is_singular( 'post' ) ) {
		$post     = get_queried_object();
		$author   = (int) $post->post_author;
		$term     = tv_primary_category( $post );
		$person   = array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author ),
			'url'   => get_author_posts_url( $author ),
		);
		$same_as  = array_values( array_filter( array( get_the_author_meta( 'tv_x', $author ), get_the_author_meta( 'tv_linkedin', $author ) ) ) );
		if ( $same_as ) {
			$person['sameAs'] = $same_as;
		}
		$article = array(
			'@type'            => 'NewsArticle',
			'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
			'description'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
			'mainEntityOfPage' => get_permalink( $post ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'author'           => array( $person ),
			'publisher'        => $publisher,
			'articleSection'   => $term ? $term->name : '',
			'isAccessibleForFree' => true,
		);
		if ( has_post_thumbnail( $post ) ) {
			$article['image'] = array( get_the_post_thumbnail_url( $post, 'full' ) );
		}
		$sources = tv_sources( $post->ID );
		if ( $sources ) {
			$article['citation'] = array_values( array_filter( wp_list_pluck( $sources, 'url' ) ) );
		}
		$graph[] = $article;

		$crumbs = array( array( __( 'Home', 'tickervelocity' ), home_url( '/' ) ) );
		$top    = tv_top_category( $post );
		if ( $top ) {
			$crumbs[] = array( $top->name, get_category_link( $top ) );
		}
		$crumbs[] = array( wp_strip_all_tags( get_the_title( $post ) ), get_permalink( $post ) );
		$items    = array();
		foreach ( $crumbs as $i => $crumb ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $crumb[0],
				'item'     => $crumb[1],
			);
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	if ( $graph ) {
		echo '<script type="application/ld+json">' . wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . "</script>\n";
	}
}

function tv_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Keep thin pages out of Google: search results, date archives and tags
 * with fewer than five articles. Articles, categories and authors stay in.
 */
add_filter( 'wp_robots', 'tv_robots' );
function tv_robots( $robots ) {
	$thin_tag = is_tag() && get_queried_object() && get_queried_object()->count < 5;
	if ( is_search() || is_date() || $thin_tag ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	if ( is_singular( 'post' ) ) {
		$robots['max-image-preview'] = 'large'; // Needed for large images in Google Discover.
	}
	return $robots;
}

/* ---------------------------------------------------------------------------
 * Google News sitemap: /news-sitemap.xml (articles from the last 48 hours)
 * ------------------------------------------------------------------------ */

add_action( 'init', 'tv_news_sitemap_rewrite' );
function tv_news_sitemap_rewrite() {
	add_rewrite_rule( '^news-sitemap\.xml$', 'index.php?tv_news_sitemap=1', 'top' );
}

add_filter( 'query_vars', fn( $vars ) => array_merge( $vars, array( 'tv_news_sitemap' ) ) );

add_action( 'template_redirect', 'tv_news_sitemap', 0 ); // Before WordPress adds a trailing slash.
function tv_news_sitemap() {
	if ( ! get_query_var( 'tv_news_sitemap' ) ) {
		return;
	}
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 1000,
			'date_query'     => array( array( 'after' => '48 hours ago' ) ),
		)
	);
	status_header( 200 );
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex' );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";
	foreach ( $posts as $post ) {
		printf(
			"<url><loc>%s</loc><news:news><news:publication><news:name>%s</news:name><news:language>%s</news:language></news:publication><news:publication_date>%s</news:publication_date><news:title>%s</news:title></news:news></url>\n",
			esc_url( get_permalink( $post ) ),
			esc_html( get_bloginfo( 'name' ) ),
			esc_html( substr( get_bloginfo( 'language' ), 0, 2 ) ),
			esc_html( get_the_date( 'c', $post ) ),
			esc_html( wp_strip_all_tags( get_the_title( $post ) ) )
		);
	}
	echo '</urlset>';
	exit;
}

add_filter( 'robots_txt', 'tv_robots_txt', 10, 2 );
function tv_robots_txt( $output, $public ) {
	if ( $public ) {
		$rule   = "Disallow: /go/\n";
		$output = false !== strpos( $output, "Allow: /wp-admin/admin-ajax.php\n" ) ? str_replace( "Allow: /wp-admin/admin-ajax.php\n", "Allow: /wp-admin/admin-ajax.php\n" . $rule, $output ) : $rule . $output;
		$output .= 'Sitemap: ' . home_url( '/news-sitemap.xml' ) . "\n";
	}
	return $output;
}

/* ---------------------------------------------------------------------------
 * Author profiles (Users > Profile): job title, expertise, X, LinkedIn
 * ------------------------------------------------------------------------ */

function tv_author_fields() {
	return array(
		'tv_role'      => __( 'Job title (for example: Markets Editor)', 'tickervelocity' ),
		'tv_expertise' => __( 'Covers (for example: Central banks, rates, FX)', 'tickervelocity' ),
		'tv_x'         => __( 'X (Twitter) profile URL', 'tickervelocity' ),
		'tv_linkedin'  => __( 'LinkedIn profile URL', 'tickervelocity' ),
	);
}

add_action( 'show_user_profile', 'tv_author_profile_fields' );
add_action( 'edit_user_profile', 'tv_author_profile_fields' );
function tv_author_profile_fields( $user ) {
	echo '<h2>' . esc_html__( 'TickerVelocity author page', 'tickervelocity' ) . '</h2><table class="form-table" role="presentation">';
	foreach ( tv_author_fields() as $key => $label ) {
		printf(
			'<tr><th><label for="%1$s">%2$s</label></th><td><input class="regular-text" type="text" id="%1$s" name="%1$s" value="%3$s"></td></tr>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( get_the_author_meta( $key, $user->ID ) )
		);
	}
	echo '</table>';
	wp_nonce_field( 'tv_author', 'tv_author_nonce' );
}

add_action( 'personal_options_update', 'tv_save_author_fields' );
add_action( 'edit_user_profile_update', 'tv_save_author_fields' );
function tv_save_author_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['tv_author_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tv_author_nonce'] ) ), 'tv_author' ) ) {
		return;
	}
	foreach ( array_keys( tv_author_fields() ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
			update_user_meta( $user_id, $key, in_array( $key, array( 'tv_x', 'tv_linkedin' ), true ) ? esc_url_raw( $value ) : $value );
		}
	}
}
