<?php
/**
 * Blog helpers: reading time, categories, cover art for posts without a
 * featured image, the article table of contents, share links, structured
 * data and a few fixes that keep the Insights page working.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Minutes to read a post, at 225 words a minute. */
function strategro_reading_time( $post = null ) {
	$text  = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post ) ) );
	$words = count( preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 225 ) );
}

function strategro_reading_label( $post = null ) {
	/* translators: %d: minutes */
	return sprintf( _n( '%d min read', '%d min read', strategro_reading_time( $post ), 'strategro' ), strategro_reading_time( $post ) );
}

/**
 * The post's main category: the SEO plugin's "primary" pick if set,
 * otherwise the first real one. "Uncategorized" is never shown.
 */
function strategro_primary_category( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	foreach ( array( '_yoast_wpseo_primary_category', 'rank_math_primary_category' ) as $key ) {
		$term = get_term( (int) get_post_meta( $post->ID, $key, true ), 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			return $term;
		}
	}
	foreach ( get_the_category( $post->ID ) as $term ) {
		if ( 'uncategorized' !== $term->slug ) {
			return $term;
		}
	}
	return null;
}

/** Categories worth offering as topic filters (have posts, not "Uncategorized"). */
function strategro_blog_topics( $limit = 8 ) {
	$terms = get_categories(
		array(
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => $limit + 1,
		)
	);
	$terms = array_filter(
		$terms,
		function ( $term ) {
			return 'uncategorized' !== $term->slug;
		}
	);
	return array_slice( $terms, 0, $limit );
}

function strategro_blog_url() {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

/** Blog headline from the Customizer; *words in asterisks* shimmer gold. */
function strategro_blog_headline() {
	$text = esc_html( strategro_mod( 'strategro_blog_title' ) );
	return preg_replace( '/\*(.+?)\*/', '<span class="sg-gradient-text">$1</span>', $text );
}

/** The post's featured image, or generated cover art in the brand colours. */
function strategro_post_media( $size = 'large', $post = null ) {
	$post = get_post( $post );
	if ( has_post_thumbnail( $post ) ) {
		echo get_the_post_thumbnail( $post, $size, array( 'loading' => 'lazy' ) );
		return;
	}
	static $count = 0;
	$term = strategro_primary_category( $post );
	$word = $term ? $term->name : __( 'Insight', 'strategro' );
	// Cycle the colour scheme so neighbouring cards never look the same.
	$tone = $count++ % 3;
	?>
	<span class="sg-art sg-art--<?php echo esc_attr( $tone ); ?>" aria-hidden="true">
		<span class="sg-art__grid"></span>
		<span class="sg-art__orb"></span>
		<span class="sg-art__word"><?php echo esc_html( $word ); ?></span>
		<span class="sg-art__mark"><?php strategro_mark(); ?></span>
	</span>
	<?php
}

/**
 * The article body with an id on every h2/h3, plus the matching table of
 * contents: array( 'html' => string, 'toc' => array( array( id, text, level ) ) ).
 */
function strategro_article_parts() {
	$html = apply_filters( 'the_content', get_the_content() );
	$html = str_replace( ']]>', ']]&gt;', $html );
	$toc  = array();
	$used = array();

	$html = preg_replace_callback(
		'#<h([23])([^>]*)>(.*?)</h\1>#is',
		function ( $m ) use ( &$toc, &$used ) {
			$text = trim( wp_strip_all_tags( $m[3] ) );
			if ( '' === $text ) {
				return $m[0];
			}
			$attrs = $m[2];
			if ( preg_match( '/\sid=["\']([^"\']+)["\']/', $attrs, $id_match ) ) {
				$id = $id_match[1];
			} else {
				$base = sanitize_title( $text );
				$base = $base ? $base : 'section';
				$id   = $base;
				for ( $i = 2; isset( $used[ $id ] ); $i++ ) {
					$id = $base . '-' . $i;
				}
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}
			$used[ $id ] = true;
			$toc[]       = array(
				'id'    => $id,
				'text'  => $text,
				'level' => (int) $m[1],
			);
			return '<h' . $m[1] . $attrs . '>' . $m[3] . '</h' . $m[1] . '>';
		},
		$html
	);

	return array(
		'html' => $html,
		'toc'  => count( $toc ) >= 2 ? $toc : array(),
	);
}

/** Prints the table of contents list. */
function strategro_toc_list( $toc ) {
	echo '<ol class="sg-toc__list">';
	foreach ( $toc as $item ) {
		printf(
			'<li class="sg-toc__item sg-toc__item--h%1$d"><a href="#%2$s">%3$s</a></li>',
			(int) $item['level'],
			esc_attr( $item['id'] ),
			esc_html( $item['text'] )
		);
	}
	echo '</ol>';
}

/** Share targets for the current post. */
function strategro_share_links() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	return array(
		'linkedin' => array( 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url, 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45z' ),
		'x'        => array( 'X', 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title, 'M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.21-6.82-5.97 6.82H1.67l7.73-8.84L1.25 2.25h6.83l4.71 6.23 5.45-6.23zm-1.16 17.52h1.83L7.08 4.13H5.12z' ),
		'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text=' . $title . '%20' . $url, 'M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.12-.27-.2-.57-.35zM12.05 21.5h-.01a9.4 9.4 0 0 1-4.79-1.31l-.34-.2-3.56.93.95-3.47-.22-.36a9.43 9.43 0 0 1-1.44-5.02c0-5.2 4.24-9.44 9.45-9.44a9.38 9.38 0 0 1 6.68 2.77 9.38 9.38 0 0 1 2.76 6.68c0 5.21-4.24 9.44-9.44 9.44zm8.04-17.48A11.3 11.3 0 0 0 12.04.7C5.78.7.69 5.79.68 12.05c0 2 .52 3.95 1.52 5.67L.59 23.3l5.72-1.5a11.33 11.33 0 0 0 5.42 1.38h.01c6.26 0 11.35-5.09 11.36-11.35 0-3.03-1.18-5.88-3.32-8.02z' ),
		'email'    => array( __( 'Email', 'strategro' ), 'mailto:?subject=' . $title . '&body=' . $url, 'M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm1 2.4V17h16V7.4l-8 5.6-8-5.6zM5.2 7l6.8 4.76L18.8 7H5.2z' ),
	);
}

/** Share buttons for the current post, including "copy link". */
function strategro_share_buttons( $class = '' ) {
	?>
	<div class="sg-share <?php echo esc_attr( $class ); ?>">
		<?php foreach ( strategro_share_links() as $key => $link ) : ?>
			<a class="sg-share__btn" href="<?php echo esc_url( $link[1] ); ?>" <?php echo 'email' === $key ? '' : 'target="_blank" rel="noopener noreferrer"'; ?>>
				<svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true"><path d="<?php echo esc_attr( $link[2] ); ?>"/></svg>
				<span class="screen-reader-text">
					<?php
					/* translators: %s: network name */
					echo esc_html( sprintf( __( 'Share on %s', 'strategro' ), $link[0] ) );
					?>
				</span>
			</a>
		<?php endforeach; ?>
		<button class="sg-share__btn" type="button" data-sg-copy="<?php echo esc_url( get_permalink() ); ?>" data-copied="<?php esc_attr_e( 'Link copied', 'strategro' ); ?>">
			<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/></svg>
			<span class="sg-share__label"><?php esc_html_e( 'Copy link', 'strategro' ); ?></span>
		</button>
	</div>
	<?php
}

/** Pagination with arrow buttons. */
function strategro_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => '<span aria-hidden="true">&larr;</span><span class="screen-reader-text">' . esc_html__( 'Newer articles', 'strategro' ) . '</span>',
			'next_text'          => '<span aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . esc_html__( 'Older articles', 'strategro' ) . '</span>',
			'class'              => 'sg-pagination',
			'screen_reader_text' => __( 'More articles', 'strategro' ),
		)
	);
}

/* ---------------------------------------------------------------------------
 * Article assets and structured data
 * ------------------------------------------------------------------------ */

add_action( 'wp_enqueue_scripts', 'strategro_blog_assets' );
function strategro_blog_assets() {
	if ( is_singular( 'post' ) ) {
		wp_enqueue_script( 'strategro-article', STRATEGRO_URI . '/assets/js/article.js', array(), STRATEGRO_VERSION, true );
	}
}

/**
 * Article and breadcrumb structured data for search engines, unless an SEO
 * plugin already prints its own.
 */
add_action( 'wp_head', 'strategro_article_schema' );
function strategro_article_schema() {
	if ( ! is_singular( 'post' ) || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	$post    = get_queried_object();
	$article = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'BlogPosting',
		'headline'      => wp_strip_all_tags( get_the_title( $post ) ),
		'description'   => wp_strip_all_tags( get_the_excerpt( $post ) ),
		'url'           => get_permalink( $post ),
		'datePublished' => get_the_date( 'c', $post ),
		'dateModified'  => get_the_modified_date( 'c', $post ),
		'author'        => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $post->post_author ),
		),
		'publisher'     => array(
			'@type' => 'Organization',
			'name'  => strategro_mod( 'strategro_legal_name' ),
			'url'   => home_url( '/' ),
		),
	);
	if ( has_post_thumbnail( $post ) ) {
		$article['image'] = get_the_post_thumbnail_url( $post, 'full' );
	}
	$crumbs = array(
		array( __( 'Home', 'strategro' ), home_url( '/' ) ),
		array( get_the_title( (int) get_option( 'page_for_posts' ) ) ? get_the_title( (int) get_option( 'page_for_posts' ) ) : __( 'Insights', 'strategro' ), strategro_blog_url() ),
		array( wp_strip_all_tags( get_the_title( $post ) ), get_permalink( $post ) ),
	);
	$list = array();
	foreach ( $crumbs as $i => $crumb ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb[0],
			'item'     => $crumb[1],
		);
	}
	$breadcrumb = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);
	foreach ( array( $article, $breadcrumb ) as $data ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
}

/* ---------------------------------------------------------------------------
 * Keeping the Insights page working
 * ------------------------------------------------------------------------ */

/**
 * Old blog addresses (/blog/, /blogs/, /news/) that no longer exist go to
 * the Insights page instead of a 404. Real pages at those addresses win.
 */
add_action( 'template_redirect', 'strategro_old_blog_redirect' );
function strategro_old_blog_redirect() {
	if ( ! is_404() || ! get_option( 'page_for_posts' ) ) {
		return;
	}
	$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( in_array( strtolower( $path ), array( 'blog', 'blogs', 'news' ), true ) ) {
		wp_safe_redirect( strategro_blog_url(), 301 );
		exit;
	}
}

/**
 * The Insights page lists posts automatically, so Elementor can't edit it
 * (trying shows "the content area was not found"). Hide its "Edit with
 * Elementor" button and point to where the page is changed instead.
 */
add_filter( 'elementor/utils/is_post_support', 'strategro_posts_page_not_elementor', 10, 2 );
function strategro_posts_page_not_elementor( $supported, $post_id ) {
	if ( $post_id && (int) get_option( 'page_for_posts' ) === (int) $post_id ) {
		return false;
	}
	return $supported;
}

add_action( 'admin_notices', 'strategro_posts_page_notice' );
function strategro_posts_page_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'page' !== $screen->id || ! isset( $_GET['post'] ) || (int) $_GET['post'] !== (int) get_option( 'page_for_posts' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'This is your Insights (blog) page. It lists your posts automatically in the Strategro design. Change its headline and intro in', 'strategro' ),
		esc_url( admin_url( 'customize.php?autofocus[section]=strategro_blog' ) ),
		esc_html__( 'Customize > Strategro Blog', 'strategro' )
	);
}
