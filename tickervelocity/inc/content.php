<?php
/**
 * Makes articles from any writing tool (the Strategro SEO app, the editor,
 * anything using the REST API) look and behave right without extra fields:
 *
 *  - a "Key takeaways" (or "TL;DR", "Summary"...) list in the text becomes
 *    the takeaways box at the top;
 *  - outbound reference links become a Sources list if the article has none;
 *  - referral links are labelled, marked rel="sponsored", disclosed, and
 *    only clickable in countries you allow;
 *  - new articles left in "Uncategorized" are sorted into a section.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------------
 * Key takeaways from the text
 * ------------------------------------------------------------------------ */

/**
 * Finds a takeaways heading followed by a list, removes both from $html and
 * returns the list items.
 */
function tv_extract_takeaways( &$html ) {
	$pattern = '#<h[2-4][^>]*>\s*(?:<strong>)?\s*(?:key\s+takeaways?|takeaways|tl;?\s*dr|summary|in\s+brief|at\s+a\s+glance|key\s+points|the\s+bottom\s+line)\s*:?\s*(?:</strong>)?\s*</h[2-4]>\s*(<(ul|ol)\b[^>]*>(.*?)</\2>)#is';
	if ( ! preg_match( $pattern, $html, $m ) ) {
		return array();
	}
	preg_match_all( '#<li\b[^>]*>(.*?)</li>#is', $m[3], $items );
	$points = array_values( array_filter( array_map( 'trim', array_map( 'wp_strip_all_tags', $items[1] ) ) ) );
	if ( count( $points ) < 2 ) {
		return array();
	}
	$html = str_replace( $m[0], '', $html );
	return array_slice( $points, 0, 6 );
}

/* ---------------------------------------------------------------------------
 * Sources from the article's own reference links
 * ------------------------------------------------------------------------ */

function tv_has_sources_section( $html ) {
	return (bool) preg_match( '#<h[2-4][^>]*>\s*(?:sources?|references?|further\s+reading|citations?)\s*:?\s*</h[2-4]>#i', $html );
}

/** External, non-referral links in the text: array( title, url ). */
function tv_auto_sources( $html ) {
	$home    = wp_parse_url( home_url(), PHP_URL_HOST );
	$skip    = array( 'twitter.com', 'x.com', 'facebook.com', 'linkedin.com', 't.me', 'wa.me', 'instagram.com', 'youtube.com', 'tiktok.com' );
	$sources = array();
	preg_match_all( '#<a\b[^>]*href=["\'](https?://[^"\']+)["\'][^>]*>(.*?)</a>#is', $html, $links, PREG_SET_ORDER );
	foreach ( $links as $link ) {
		$url  = html_entity_decode( $link[1] );
		$host = preg_replace( '/^www\./', '', (string) wp_parse_url( $url, PHP_URL_HOST ) );
		if ( ! $host || $host === preg_replace( '/^www\./', '', (string) $home ) || in_array( $host, $skip, true ) || tv_is_referral_url( $url, $link[0] ) ) {
			continue;
		}
		$key = strtok( $url, '#' );
		if ( isset( $sources[ $key ] ) ) {
			continue;
		}
		$text            = trim( wp_strip_all_tags( $link[2] ) );
		$sources[ $key ] = array(
			'title' => strlen( $text ) > 3 && ! preg_match( '#^https?://#', $text ) ? $text : $host,
			'url'   => $url,
		);
		if ( count( $sources ) >= 8 ) {
			break;
		}
	}
	return array_values( $sources );
}

/* ---------------------------------------------------------------------------
 * Referral links
 * ------------------------------------------------------------------------ */

function tv_referral_domains() {
	return array_filter( array_map( 'trim', explode( ',', strtolower( (string) tv_mod( 'tv_ref_domains' ) ) ) ) );
}

/**
 * Whether a link is a referral link: a sign-up/invite link on one of the
 * listed platforms, any link carrying a referral code, or a link your writing
 * tool marked rel="sponsored". Plain links to a platform (for example its
 * announcement, cited as a source) stay ordinary links.
 */
function tv_is_referral_url( $url, $attrs = '' ) {
	if ( $attrs && preg_match( '/rel=["\'][^"\']*sponsored|class=["\'][^"\']*affiliate|data-affiliate/i', $attrs ) ) {
		return true;
	}
	if ( preg_match( '/[?&](ref|referral|ref_code|refcode|referralcode|invite|invitecode|invite_code|inviter|aff|aff_id|affiliate|affiliate_id|partner_id|clickid)=/i', $url ) ) {
		return true;
	}
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	$path = strtolower( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	foreach ( tv_referral_domains() as $domain ) {
		if ( $host === $domain || substr( $host, -strlen( '.' . $domain ) ) === '.' . $domain ) {
			return (bool) preg_match( '#/(register|invite|join|signup|sign-up|referral|ref|r)(/|$)|/activity/referral#', $path );
		}
	}
	return false;
}

/** Country of the visitor from the CDN or host, or '' if unknown. */
function tv_visitor_country() {
	foreach ( array( 'HTTP_CF_IPCOUNTRY', 'HTTP_CLOUDFRONT_VIEWER_COUNTRY', 'HTTP_X_VERCEL_IP_COUNTRY', 'HTTP_X_COUNTRY_CODE', 'GEOIP_COUNTRY_CODE', 'HTTP_GEOIP_COUNTRY_CODE' ) as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$code = strtoupper( substr( sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ), 0, 2 ) );
			if ( preg_match( '/^[A-Z]{2}$/', $code ) && ! in_array( $code, array( 'XX', 'T1', 'A1', 'A2' ), true ) ) {
				return $code;
			}
		}
	}
	return '';
}

function tv_referrals_allowed_for( $country ) {
	if ( '' === $country ) {
		return 'show' === tv_mod( 'tv_ref_unknown' );
	}
	$blocked = array_filter( array_map( 'trim', explode( ',', strtoupper( (string) tv_mod( 'tv_ref_blocked' ) ) ) ) );
	return ! in_array( $country, $blocked, true );
}

/**
 * Rewrites referral links in article text. With country checks on (the
 * default) they are printed as plain text and turned into links in the
 * browser only for visitors in allowed countries; with checks off they are
 * printed as labelled sponsored links.
 */
add_filter( 'the_content', 'tv_process_referral_links', 20 );
add_filter( 'the_content_feed', 'tv_process_referral_links', 20 );
function tv_process_referral_links( $html ) {
	if ( is_admin() || ! tv_referral_domains() && false === stripos( $html, 'ref' ) ) {
		return $html;
	}
	$geo  = (bool) tv_mod( 'tv_ref_geo' ) || is_feed();
	$html = preg_replace_callback(
		'#<a\b([^>]*)href=(["\'])(https?://[^"\']+)\2([^>]*)>(.*?)</a>#is',
		function ( $m ) use ( $geo ) {
			$url = html_entity_decode( $m[3] );
			if ( ! tv_is_referral_url( $url, $m[1] . ' ' . $m[4] ) ) {
				return $m[0];
			}
			$GLOBALS['tv_has_referrals'] = true;
			$tag                         = '<span class="tv-ref__tag">' . esc_html__( 'Ad', 'tickervelocity' ) . '</span>';
			if ( $geo ) {
				return '<span class="tv-ref is-locked" data-tv-ref="' . esc_attr( base64_encode( $url ) ) . '">' . $m[5] . '</span>'; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- not obfuscation; keeps the link out of the page until the country check passes.
			}
			return '<a class="tv-ref" href="' . esc_url( $url ) . '" rel="sponsored nofollow noopener" target="_blank">' . $m[5] . $tag . '</a>';
		},
		$html
	);
	return $html;
}

/** Disclosure shown above articles that contain referral links. */
function tv_referral_note() {
	if ( empty( $GLOBALS['tv_has_referrals'] ) ) {
		return;
	}
	$page = get_page_by_path( 'affiliate-disclosure' );
	$geo  = (bool) tv_mod( 'tv_ref_geo' );
	?>
	<aside class="tv-refnote" data-tv-refnote<?php echo $geo ? ' hidden' : ''; ?>>
		<p><strong><?php esc_html_e( 'This article contains affiliate links', 'tickervelocity' ); ?></strong>, <?php esc_html_e( 'marked "Ad". If you sign up through one, we may earn a commission at no cost to you. It never affects our coverage.', 'tickervelocity' ); ?>
		<?php if ( $page ) : ?>
			<a href="<?php echo esc_url( get_permalink( $page ) ); ?>"><?php esc_html_e( 'How this works', 'tickervelocity' ); ?></a>
		<?php endif; ?>
		</p>
		<p class="tv-refnote__risk"><?php echo esc_html( tv_mod( 'tv_crypto_risk' ) ); ?></p>
	</aside>
	<?php
}

add_action( 'rest_api_init', 'tv_geo_route' );
function tv_geo_route() {
	register_rest_route(
		'tv/v1',
		'/geo',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => function () {
				$country  = tv_visitor_country();
				$response = rest_ensure_response(
					array(
						'country' => $country,
						'allowed' => tv_referrals_allowed_for( $country ),
					)
				);
				$response->header( 'Cache-Control', 'no-store, private' );
				return $response;
			},
		)
	);
}

/* ---------------------------------------------------------------------------
 * Sorting new articles into sections
 * ------------------------------------------------------------------------ */

/** Sub-section slug => words that point to it. */
function tv_section_keywords() {
	return array(
		'daily-brief'     => array( 'morning brief', 'daily brief', 'things to know', 'before the open', 'week ahead' ),
		'crypto-rules'    => array( 'crypto regulation', 'crypto rules', 'mica', 'stablecoin bill', 'crypto bill', 'crypto licen', 'crypto ban', 'crypto tax' ),
		'sec-us'          => array( 'sec', 'securities and exchange commission', 'cftc', 'congress', 'senate', 'lawsuit', 'regulator' ),
		'uk-eu'           => array( 'fca', 'financial conduct authority', 'european commission', 'eu rules', 'esma', 'pra' ),
		'bitcoin'         => array( 'bitcoin', 'btc', 'satoshi', 'halving', 'microstrategy', 'strategy inc' ),
		'ethereum'        => array( 'ethereum', 'ether', 'eth', 'vitalik', 'layer 2' ),
		'altcoins'        => array( 'solana', 'xrp', 'ripple', 'cardano', 'dogecoin', 'bnb', 'memecoin', 'meme coin', 'altcoin', 'stablecoin', 'defi', 'token', 'airdrop', 'crypto' ),
		'central-banks'   => array( 'federal reserve', 'the fed', 'fomc', 'powell', 'ecb', 'bank of england', 'bank of japan', 'rate cut', 'rate hike', 'interest rate', 'central bank', 'lagarde', 'bailey' ),
		'inflation'       => array( 'inflation', 'cpi', 'pce', 'ppi', 'consumer prices', 'price index' ),
		'jobs-growth'     => array( 'jobs report', 'nonfarm', 'payrolls', 'unemployment', 'gdp', 'recession', 'jobless claims', 'pmi', 'retail sales' ),
		'earnings'        => array( 'earnings', 'quarterly results', 'revenue', 'eps', 'guidance', 'beat estimates', 'missed estimates', 'profit' ),
		'ipos'            => array( 'ipo', 'initial public offering', 'goes public', 'market debut', 'listing' ),
		'stocks'          => array( 'shares', 'stock', 'nasdaq', 's&p 500', 'dow jones', 'wall street', 'rally', 'sell-off', 'equities' ),
		'oil-energy'      => array( 'oil', 'crude', 'brent', 'wti', 'opec', 'natural gas', 'lng', 'energy prices', 'gasoline' ),
		'gold-metals'     => array( 'gold', 'silver', 'copper', 'platinum', 'lithium', 'metals' ),
		'fda'             => array( 'fda', 'approval', 'approves', 'pdufa', 'regulatory nod' ),
		'clinical-trials' => array( 'trial', 'phase 3', 'phase iii', 'phase 2', 'clinical', 'readout', 'endpoint' ),
		'tech-layoffs'    => array( 'layoff', 'job cuts', 'cut jobs', 'redundanc', 'workforce reduction' ),
		'finance-layoffs' => array( 'layoff', 'job cuts', 'cut jobs', 'redundanc', 'bank' ),
	);
}

/** How often $word appears as a word (short words must match exactly, longer ones as a word start). */
function tv_count_word( $word, $text ) {
	$word = trim( $word );
	$re   = '/(?<![a-z0-9])' . preg_quote( $word, '/' ) . ( strlen( $word ) <= 4 ? '(?![a-z0-9])' : '' ) . '/i';
	return preg_match_all( $re, $text );
}

/** Best sub-section slug for a title and text, or ''. */
function tv_guess_section( $title, $text ) {
	$best   = '';
	$top    = 0;
	$all    = $title . ' ' . $text;
	$layoff = preg_match( '/layoff|job cuts|cut jobs|cutting jobs|redundanc|workforce reduction/i', $all );
	foreach ( tv_section_keywords() as $slug => $words ) {
		if ( in_array( $slug, array( 'tech-layoffs', 'finance-layoffs' ), true ) && ! $layoff ) {
			continue;
		}
		$score = 0;
		foreach ( $words as $word ) {
			$score += 3 * tv_count_word( $word, $title ) + min( 4, tv_count_word( $word, $text ) );
		}
		if ( 'finance-layoffs' === $slug && ! preg_match( '/\b(bank|banks|goldman|jpmorgan|citi|citigroup|hsbc|barclays|morgan stanley|wells fargo|ubs|deutsche bank|blackrock)\b/i', $all ) ) {
			$score = 0;
		}
		if ( $layoff && $score && in_array( $slug, array( 'tech-layoffs', 'finance-layoffs' ), true ) ) {
			$score += 6; // Job cuts are the story, whatever the company makes.
		}
		if ( $score > $top ) {
			$top  = $score;
			$best = $slug;
		}
	}
	return $top >= 3 ? $best : '';
}

add_action( 'wp_after_insert_post', 'tv_autosort_post', 10, 3 );
function tv_autosort_post( $post_id, $post, $update ) {
	if ( 'post' !== $post->post_type || wp_is_post_revision( $post_id ) || ! tv_mod( 'tv_autosort' ) || get_post_meta( $post_id, '_tv_sorted', true ) ) {
		return;
	}
	if ( ! in_array( $post->post_status, array( 'publish', 'future', 'draft', 'pending' ), true ) ) {
		return;
	}
	$cats    = wp_get_post_categories( $post_id );
	$default = (int) get_option( 'default_category' );
	if ( $cats && array_diff( $cats, array( $default ) ) ) {
		update_post_meta( $post_id, '_tv_sorted', 'author' ); // The writer chose a section; respect it.
		return;
	}
	$slug = tv_guess_section( $post->post_title, wp_strip_all_tags( $post->post_content ) );
	$term = get_category_by_slug( $slug ? $slug : 'markets' );
	if ( $term ) {
		wp_set_post_categories( $post_id, array( $term->term_id ) );
		update_post_meta( $post_id, '_tv_sorted', $slug ? $slug : 'fallback' );
	}
}
