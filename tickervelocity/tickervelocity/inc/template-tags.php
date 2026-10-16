<?php
/**
 * Small template helpers.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** The TickerVelocity mark: rising bars with a velocity line. */
function tv_mark() {
	?>
	<svg class="tv-mark" width="30" height="30" viewBox="0 0 32 32" fill="none" aria-hidden="true">
		<rect x="3" y="19" width="5.5" height="10" rx="1.6" fill="currentColor" fill-opacity=".38"/>
		<rect x="11" y="13" width="5.5" height="16" rx="1.6" fill="currentColor" fill-opacity=".68"/>
		<rect x="19" y="7" width="5.5" height="22" rx="1.6" fill="url(#tv-mark-g)"/>
		<path d="M3.5 13.5 11 8.2l5 2.4L28.5 3" stroke="url(#tv-mark-g)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		<defs><linearGradient id="tv-mark-g" x1="3" y1="30" x2="29" y2="3" gradientUnits="userSpaceOnUse"><stop stop-color="#6d5ef8"/><stop offset="1" stop-color="#22d3ee"/></linearGradient></defs>
	</svg>
	<?php
}

/** Uploaded logo, or the mark and wordmark. */
function tv_brand() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf( '<a class="tv-brand" href="%s" rel="home" aria-label="%s">', esc_url( home_url( '/' ) ), esc_attr( get_bloginfo( 'name' ) . ' home' ) );
	tv_mark();
	echo '<span class="tv-brand__name">Ticker<b>Velocity</b></span></a>';
}

/** "5 min ago", "3 h ago", then a date. */
function tv_time_ago( $post = null ) {
	$then = get_post_time( 'U', true, $post );
	$diff = time() - $then;
	if ( $diff < 0 ) {
		$label = get_the_date( 'j M Y', $post );
	} elseif ( $diff < HOUR_IN_SECONDS ) {
		/* translators: %d: minutes */
		$label = sprintf( __( '%d min ago', 'tickervelocity' ), max( 1, (int) floor( $diff / MINUTE_IN_SECONDS ) ) );
	} elseif ( $diff < DAY_IN_SECONDS ) {
		/* translators: %d: hours */
		$label = sprintf( __( '%d h ago', 'tickervelocity' ), (int) floor( $diff / HOUR_IN_SECONDS ) );
	} else {
		$label = get_the_date( 'j M Y', $post );
	}
	return sprintf( '<time datetime="%s">%s</time>', esc_attr( get_the_date( 'c', $post ) ), esc_html( $label ) );
}

function tv_reading_time( $post = null ) {
	$words = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post ) ) ) ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 230 ) );
}

/** Main category (SEO plugin's primary pick if set). Never "Uncategorized". */
function tv_primary_category( $post = null ) {
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
	$terms = get_the_category( $post->ID );
	// Prefer a subcategory (more specific), then any real category.
	usort(
		$terms,
		function ( $a, $b ) {
			return (int) ( 0 === $a->parent ) - (int) ( 0 === $b->parent );
		}
	);
	foreach ( $terms as $term ) {
		if ( 'uncategorized' !== $term->slug ) {
			return $term;
		}
	}
	return null;
}

/** Top-level category of a post (for colour coding and offers). */
function tv_top_category( $post = null ) {
	$term = tv_primary_category( $post );
	while ( $term && $term->parent ) {
		$term = get_term( $term->parent, 'category' );
	}
	return $term && ! is_wp_error( $term ) ? $term : null;
}

function tv_category_chip( $post = null, $class = 'tv-chip' ) {
	$term = tv_primary_category( $post );
	if ( ! $term ) {
		return;
	}
	$top = tv_top_category( $post );
	printf( '<span class="%1$s" data-cat="%2$s">%3$s</span>', esc_attr( $class ), esc_attr( $top ? $top->slug : '' ), esc_html( $term->name ) );
}

/** Featured image, or generated cover art so cards never have an empty box. */
function tv_post_media( $size = 'tv-card', $post = null, $attrs = array() ) {
	$post = get_post( $post );
	if ( has_post_thumbnail( $post ) ) {
		echo get_the_post_thumbnail( $post, $size, array_merge( array( 'loading' => 'lazy' ), $attrs ) );
		return;
	}
	$top  = tv_top_category( $post );
	$term = tv_primary_category( $post );
	?>
	<span class="tv-art" data-cat="<?php echo esc_attr( $top ? $top->slug : '' ); ?>" aria-hidden="true">
		<svg class="tv-art__chart" viewBox="0 0 400 225" preserveAspectRatio="none">
			<?php
			// A deterministic little price line per post.
			$seed   = crc32( (string) $post->ID );
			$points = array();
			$y      = 150;
			for ( $x = 0; $x <= 400; $x += 25 ) {
				$seed     = ( $seed * 1103515245 + 12345 ) & 0x7fffffff;
				$y        = max( 40, min( 200, $y + ( $seed % 60 ) - 32 ) );
				$points[] = $x . ',' . $y;
			}
			?>
			<polyline points="<?php echo esc_attr( implode( ' ', $points ) ); ?>" fill="none" stroke="currentColor" stroke-width="2.5" vector-effect="non-scaling-stroke"/>
			<polygon points="<?php echo esc_attr( '0,225 ' . implode( ' ', $points ) . ' 400,225' ); ?>" fill="currentColor" fill-opacity=".12"/>
		</svg>
		<span class="tv-art__label"><?php echo esc_html( $term ? $term->name : __( 'Markets', 'tickervelocity' ) ); ?></span>
	</span>
	<?php
}

/**
 * The article body with ids on h2/h3 (for the contents list) and the
 * newsletter box placed after the paragraph about 40% of the way in.
 * Returns array( 'html', 'toc' ).
 */
function tv_article_parts() {
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
			if ( preg_match( '/\sid=["\']([^"\']+)["\']/', $m[2], $found ) ) {
				$id    = $found[1];
				$attrs = $m[2];
			} else {
				$base = sanitize_title( $text ) ? sanitize_title( $text ) : 'section';
				$id   = $base;
				for ( $i = 2; isset( $used[ $id ] ); $i++ ) {
					$id = $base . '-' . $i;
				}
				$attrs = $m[2] . ' id="' . esc_attr( $id ) . '"';
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

	// Newsletter box mid-article (only in articles long enough to have one).
	$count = substr_count( $html, '</p>' );
	if ( $count >= 6 ) {
		$after = (int) ceil( $count * 0.4 );
		$seen  = 0;
		ob_start();
		get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'inline' ) );
		$box  = ob_get_clean();
		$html = preg_replace_callback(
			'#</p>#',
			function ( $m ) use ( &$seen, $after, $box ) {
				return ++$seen === $after ? $m[0] . $box : $m[0];
			},
			$html
		);
	}

	return array(
		'html' => $html,
		'toc'  => count( $toc ) >= 2 ? $toc : array(),
	);
}

function tv_share_buttons() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	$links = array(
		array( 'X', 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title, 'M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.21-6.82-5.97 6.82H1.67l7.73-8.84L1.25 2.25h6.83l4.71 6.23 5.45-6.23zm-1.16 17.52h1.83L7.08 4.13H5.12z' ),
		array( 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url, 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.38-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45z' ),
		array( 'Telegram', 'https://t.me/share/url?url=' . $url . '&text=' . $title, 'M21.94 4.3 18.8 19.1c-.24 1.05-.86 1.31-1.74.81l-4.8-3.54-2.31 2.23c-.26.26-.47.47-.97.47l.35-4.9 8.9-8.05c.39-.34-.08-.53-.6-.19L6.63 12.86l-4.74-1.48c-1.03-.32-1.05-1.03.21-1.52L20.6 2.73c.86-.32 1.6.19 1.34 1.57z' ),
		array( 'WhatsApp', 'https://wa.me/?text=' . $title . '%20' . $url, 'M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91A9.84 9.84 0 0 0 12.04 2zm5.8 14.03c-.24.68-1.43 1.32-1.97 1.37-.5.05-1.13.07-1.83-.12-.42-.13-.96-.31-1.66-.61-2.92-1.26-4.83-4.2-4.98-4.4-.14-.2-1.19-1.58-1.19-3.02 0-1.43.75-2.14 1.02-2.43.27-.3.59-.37.79-.37h.57c.18 0 .43-.07.67.51.25.6.84 2.06.92 2.2.07.15.12.32.02.52-.1.2-.15.32-.29.49-.15.17-.31.39-.44.52-.15.15-.3.31-.13.6.17.3.76 1.25 1.63 2.03 1.12 1 2.06 1.31 2.36 1.46.29.15.46.12.63-.07.17-.2.73-.85.92-1.15.2-.29.39-.24.66-.15.27.1 1.72.81 2.02.96.29.15.49.22.56.34.07.12.07.71-.17 1.39z' ),
	);
	echo '<div class="tv-share">';
	foreach ( $links as $link ) {
		printf(
			'<a class="tv-share__btn" href="%1$s" target="_blank" rel="noopener noreferrer"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="%2$s"/></svg><span class="screen-reader-text">%3$s</span></a>',
			esc_url( $link[1] ),
			esc_attr( $link[2] ),
			/* translators: %s: network */
			esc_html( sprintf( __( 'Share on %s', 'tickervelocity' ), $link[0] ) )
		);
	}
	printf(
		'<button class="tv-share__btn tv-share__copy" type="button" data-tv-copy="%1$s"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/></svg><span>%2$s</span></button>',
		esc_url( get_permalink() ),
		esc_html__( 'Copy link', 'tickervelocity' )
	);
	echo '</div>';
}

function tv_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => '&larr;<span class="screen-reader-text">' . esc_html__( 'Newer', 'tickervelocity' ) . '</span>',
			'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Older', 'tickervelocity' ) . '</span>&rarr;',
			'class'              => 'tv-pagination',
			'screen_reader_text' => __( 'More news', 'tickervelocity' ),
		)
	);
}

/** Menu with a fallback built from the top categories, so the header is never empty. */
function tv_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	if ( 'primary' === $location ) {
		foreach ( tv_home_categories() as $term ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_category_link( $term ) ), esc_html( $term->name ) );
		}
	}
	echo '</ul>';
}

/** The categories listed in Customize > Homepage, as terms that exist. */
function tv_home_categories() {
	$terms = array();
	foreach ( array_filter( array_map( 'trim', explode( ',', tv_mod( 'tv_home_sections' ) ) ) ) as $slug ) {
		$term = get_category_by_slug( $slug );
		if ( $term ) {
			$terms[] = $term;
		}
	}
	return $terms;
}

function tv_latest_url() {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

function tv_toc_list( $toc ) {
	echo '<ol class="tv-toc__list">';
	foreach ( $toc as $item ) {
		printf( '<li class="tv-toc__h%1$d"><a href="#%2$s">%3$s</a></li>', (int) $item['level'], esc_attr( $item['id'] ), esc_html( $item['text'] ) );
	}
	echo '</ol>';
}
