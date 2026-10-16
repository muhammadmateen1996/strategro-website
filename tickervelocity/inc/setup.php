<?php
/**
 * TickerVelocity > Setup: one click creates the sections, trust and legal
 * pages, menus and reading settings. Optional sample articles preview the
 * design and can be removed in one click.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tv_setup_menu' );
function tv_setup_menu() {
	add_menu_page( 'TickerVelocity', 'TickerVelocity', 'manage_options', 'tickervelocity', 'tv_setup_page', 'dashicons-chart-line', 3 );
	add_submenu_page( 'tickervelocity', __( 'Setup', 'tickervelocity' ), __( 'Setup', 'tickervelocity' ), 'manage_options', 'tickervelocity', 'tv_setup_page' );
}

add_action( 'after_switch_theme', 'tv_after_switch' );
function tv_after_switch() {
	tv_news_sitemap_rewrite();
	flush_rewrite_rules( false );
}

add_action( 'admin_notices', 'tv_setup_notice' );
function tv_setup_notice() {
	$screen = get_current_screen();
	if ( get_option( 'tv_setup_done' ) || ! current_user_can( 'manage_options' ) || ( $screen && false !== strpos( $screen->id, 'tickervelocity' ) ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>%s</strong> %s <a class="button button-primary" href="%s">%s</a></p></div>',
		esc_html__( 'TickerVelocity is active.', 'tickervelocity' ),
		esc_html__( 'One click creates your sections, pages and menus.', 'tickervelocity' ),
		esc_url( admin_url( 'admin.php?page=tickervelocity' ) ),
		esc_html__( 'Open Setup', 'tickervelocity' )
	);
}

function tv_sections() {
	return array(
		'markets'     => array( 'Markets', 'Stocks, indices and the moves behind them.', array( 'stocks' => 'Stocks', 'earnings' => 'Earnings', 'ipos' => 'IPOs' ) ),
		'crypto'      => array( 'Crypto', 'Bitcoin, Ethereum and the wider crypto market, without the hype.', array( 'bitcoin' => 'Bitcoin', 'ethereum' => 'Ethereum', 'altcoins' => 'Altcoins' ) ),
		'macro'       => array( 'Macro', 'Central banks, inflation, jobs and growth: the forces behind every market.', array( 'central-banks' => 'Central banks', 'inflation' => 'Inflation', 'jobs-growth' => 'Jobs and growth' ) ),
		'commodities' => array( 'Commodities', 'Oil, gas, gold and metals, and what moves them.', array( 'oil-energy' => 'Oil and energy', 'gold-metals' => 'Gold and metals' ) ),
		'biotech'     => array( 'Biotech', 'FDA decisions, trial readouts and the deals that move biotech stocks.', array( 'fda' => 'FDA decisions', 'clinical-trials' => 'Clinical trials' ) ),
		'regulation'  => array( 'Regulation', 'The rules, rulings and regulators reshaping finance and crypto.', array( 'sec-us' => 'SEC and US', 'uk-eu' => 'UK and EU', 'crypto-rules' => 'Crypto rules' ) ),
		'layoffs'     => array( 'Layoffs', 'Job cuts at major companies and what they signal.', array( 'tech-layoffs' => 'Tech', 'finance-layoffs' => 'Finance' ) ),
		'daily-brief' => array( 'Daily Brief', 'The five things that matter before the opening bell, every weekday.', array() ),
	);
}

function tv_ensure_category( $slug, $name, $description = '', $parent = 0 ) {
	$term = get_category_by_slug( $slug );
	if ( $term ) {
		return $term->term_id;
	}
	$result = wp_insert_term(
		$name,
		'category',
		array(
			'slug'        => $slug,
			'description' => $description,
			'parent'      => $parent,
		)
	);
	return is_wp_error( $result ) ? 0 : $result['term_id'];
}

function tv_ensure_page( $slug, $title, $content ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return $page->ID;
	}
	return wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		)
	);
}

function tv_ensure_menu( $name, $location, $items ) {
	$menu = wp_get_nav_menu_object( $name );
	$id   = $menu ? $menu->term_id : wp_create_nav_menu( $name );
	if ( is_wp_error( $id ) ) {
		return;
	}
	foreach ( wp_get_nav_menu_items( $id ) ? wp_get_nav_menu_items( $id ) : array() as $item ) {
		wp_delete_post( $item->ID, true );
	}
	foreach ( $items as $i => $item ) {
		$args = array(
			'menu-item-title'    => $item[0],
			'menu-item-status'   => 'publish',
			'menu-item-position' => $i + 1,
		);
		if ( 'category' === $item[1] ) {
			$args['menu-item-type']      = 'taxonomy';
			$args['menu-item-object']    = 'category';
			$args['menu-item-object-id'] = $item[2];
		} else {
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $item[2];
		}
		wp_update_nav_menu_item( $id, 0, $args );
	}
	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/** Runs the setup. Returns lines describing what happened. */
function tv_run_setup() {
	$log = array();

	$cats = array();
	foreach ( tv_sections() as $slug => $section ) {
		$cats[ $slug ] = tv_ensure_category( $slug, $section[0], $section[1] );
		foreach ( $section[2] as $child_slug => $child_name ) {
			tv_ensure_category( $child_slug, $child_name, '', $cats[ $slug ] );
		}
	}
	$log[] = __( 'Sections and sub-sections created (existing ones were kept).', 'tickervelocity' );

	$pages = array();
	foreach ( tv_page_templates() as $slug => $page ) {
		$pages[ $slug ] = tv_ensure_page( $slug, $page[0], $page[1] );
	}
	$log[] = __( 'Pages created: Home, Latest, Live prices, About, Editorial standards, Disclaimer, Affiliate disclosure, Privacy policy, Terms, Contact and Newsletter. Pages that already existed were left as they were.', 'tickervelocity' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pages['home'] );
	update_option( 'page_for_posts', $pages['latest'] );
	update_option( 'wp_page_for_privacy_policy', $pages['privacy-policy'] );
	update_option( 'default_comment_status', 'closed' );
	if ( (int) get_option( 'posts_per_page' ) === 10 ) {
		update_option( 'posts_per_page', 15 );
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = __( 'Article addresses set to /article-title/ (short and stable).', 'tickervelocity' );
	}
	if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site' ), true ) ) {
		update_option( 'blogdescription', tv_mod( 'tv_tagline' ) );
	}
	$log[] = __( 'Home is the front page and Latest lists every article. Comments are off for new articles.', 'tickervelocity' );

	$primary = array();
	foreach ( array( 'markets', 'crypto', 'macro', 'commodities', 'biotech', 'regulation', 'layoffs', 'daily-brief' ) as $slug ) {
		$primary[] = array( tv_sections()[ $slug ][0], 'category', $cats[ $slug ] );
	}
	tv_ensure_menu( 'TickerVelocity sections', 'primary', $primary );
	tv_ensure_menu(
		'TickerVelocity company',
		'footer',
		array(
			array( 'About', 'page', $pages['about'] ),
			array( 'Editorial standards', 'page', $pages['editorial-standards'] ),
			array( 'Newsletter', 'page', $pages['newsletter'] ),
			array( 'Live prices', 'page', $pages['markets'] ),
			array( 'Latest news', 'page', $pages['latest'] ),
			array( 'Contact', 'page', $pages['contact'] ),
		)
	);
	tv_ensure_menu(
		'TickerVelocity legal',
		'legal',
		array(
			array( 'Disclaimer', 'page', $pages['disclaimer'] ),
			array( 'Affiliate disclosure', 'page', $pages['affiliate-disclosure'] ),
			array( 'Privacy policy', 'page', $pages['privacy-policy'] ),
			array( 'Terms of use', 'page', $pages['terms'] ),
		)
	);
	$log[] = __( 'Menus created: sections (header and footer), company and legal.', 'tickervelocity' );

	// WordPress's own placeholder content.
	foreach ( array( array( 'hello-world', 'post' ), array( 'sample-page', 'page' ) ) as $placeholder ) {
		$item = get_page_by_path( $placeholder[0], OBJECT, $placeholder[1] );
		if ( $item && $item->post_date === $item->post_modified ) {
			wp_trash_post( $item->ID );
		}
	}

	tv_after_switch();
	update_option( 'tv_setup_done', TV_VERSION );
	return $log;
}

/* ---------------------------------------------------------------------------
 * Sample articles
 * ------------------------------------------------------------------------ */

function tv_add_samples() {
	// Written the way an AI writing tool usually formats articles, so the
	// preview shows the automatic takeaways, sources and price cards at work.
	$body = function ( $topic, $takeaways, $extra ) {
		$list = '<ul><li>' . implode( '</li><li>', explode( "\n", $takeaways ) ) . '</li></ul>';
		return '<p><strong>This is a sample article to preview the TickerVelocity design. Remove it from TickerVelocity &gt; Setup before you launch.</strong></p>'
			. '<h2>Key takeaways</h2>' . $list
			. '<p>' . $topic . ' ' . $extra . '</p>'
			. '<h2>What happened</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, according to an <a href="https://example.com/official-statement">official statement</a>. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.</p><p>Maecenas ligula massa, varius a, semper congue, euismod non, mi, as <a href="https://example.org/data-release">the latest data release</a> showed. Proin porttitor, orci nec nonummy molestie.</p>'
			. '<h2>Why it matters for markets</h2><p>Duis semper. Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim. Pellentesque congue. Ut in risus volutpat libero pharetra tempor.</p><p>Cras vestibulum bibendum augue. Praesent egestas leo in pede. Praesent blandit odio eu enim. Pellentesque sed dui ut augue blandit sodales.</p>'
			. '<h3>The numbers</h3><table><thead><tr><th>Measure</th><th>Expected</th><th>Actual</th></tr></thead><tbody><tr><td>Example figure</td><td>2.1%</td><td>2.4%</td></tr><tr><td>Previous</td><td>2.0%</td><td>2.2%</td></tr></tbody></table>'
			. '<h2>What to watch next</h2><p>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aliquam nibh. Mauris ac mauris sed pede pellentesque fermentum.</p><p>Maecenas adipiscing ante non diam sodales hendrerit. Ut velit mauris, egestas sed, gravida nec, ornare ut, mi.</p>';
	};
	$extras = array(
		'The S&P 500 rose and gold firmed as Treasury yields slipped.',
		'Bitcoin led the move and Ethereum followed. Traders comparing fees can <a href="https://www.mexc.com/register?inviteCode=SAMPLE">open an account on MEXC</a> (a sample referral link).',
		'Nvidia (NVDA) led the Nasdaq higher after the report.',
		'Brent crude and US oil futures both fell.',
		'Biotech shares were mixed after the decision.',
		'Bitcoin was little changed after the announcement.',
		'The stock rose in early trading as investors welcomed the cost cuts.',
		'Stocks, oil, gold and Bitcoin: what moved overnight and what to watch today.',
	);
	$samples = array(
		array( 'Sample: Central bank holds rates, signals cuts later this year', 'central-banks', 'Policymakers kept rates on hold but changed their language on the path ahead.', "Rates unchanged, as expected\nGuidance now points to cuts later in the year\nBond yields fell after the decision", "US10Y:down:Yields fell on softer guidance\nUSD:down:Dollar eased against majors\nGOLD:up:Lower yields support gold", 'mixed', 'This quarter', true ),
		array( 'Sample: Bitcoin climbs as ETF inflows pick up again', 'bitcoin', 'Flows into spot funds turned positive for a fifth straight session.', "Spot ETF inflows positive five days running\nOn-chain activity steady\nVolatility remains high", "BTC:up:ETF demand\nETH:up:Followed bitcoin higher\nCOIN:up:Trading volumes rose", 'bullish', 'This week', false ),
		array( 'Sample: Chipmaker beats forecasts on data-centre demand', 'earnings', 'Quarterly revenue topped estimates and guidance was raised.', "Revenue beat estimates\nGuidance raised for next quarter\nShares rose after hours", "NVDA:up:Beat and raise\nAMD:up:Read-across to peers\nSOX:up:Semiconductor index firmer", 'bullish', 'Today', false ),
		array( 'Sample: Oil slips as supply worries ease', 'oil-energy', 'Prices fell as output rose and demand forecasts were trimmed.', "Output increased more than expected\nDemand forecast trimmed\nRefiners steady", "BRENT:down:Supply rising\nXOM:down:Followed crude lower\nAIRLINES:up:Lower fuel costs", 'bearish', 'This week', false ),
		array( 'Sample: Drugmaker wins FDA approval for new treatment', 'fda', 'Regulators approved the drug after a positive late-stage trial.', "FDA approved the treatment\nLaunch planned next quarter\nAnalysts raised sales forecasts", "XBI:up:Biotech index firmer\nPEERS:flat:Limited read-across", 'bullish', 'Long term', false ),
		array( 'Sample: Regulator publishes new rules for crypto platforms', 'crypto-rules', 'The rules set out how exchanges must protect customer funds.', "Exchanges must segregate customer assets\nNew disclosure requirements\nTransition period of 12 months", "BTC:flat:Muted reaction\nCOIN:up:Clarity seen as positive", 'neutral', 'This year', false ),
		array( 'Sample: Tech company to cut 6% of staff in restructuring', 'tech-layoffs', 'The company said the cuts will fund investment in AI.', "About 6% of roles affected\nSavings to be reinvested in AI\nShares rose on cost discipline", "TECH:up:Investors welcomed cost cuts", 'mixed', 'This quarter', false ),
		array( 'Sample: Morning Brief: five things to know before the open', 'daily-brief', 'Rates, chips, oil, bitcoin and the data to watch today.', "Central bank held rates and hinted at cuts\nChipmaker beat forecasts on data-centre demand\nOil slipped as supply worries eased\nBitcoin rose on ETF inflows\nToday: jobs data at 13:30 UK time", '', '', '', false ),
	);
	$count = 0;
	foreach ( $samples as $i => $s ) {
		if ( get_page_by_path( sanitize_title( $s[0] ), OBJECT, 'post' ) ) {
			continue;
		}
		$term = get_category_by_slug( $s[1] );
		$id   = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_title'    => $s[0],
				'post_excerpt'  => $s[2],
				'post_content'  => $body( $s[2], $s[3], $extras[ $i ] ),
				'post_category' => $term ? array( $term->term_id ) : array(),
				'post_date'     => wp_date( 'Y-m-d H:i:s', time() - ( $i * 3 + 1 ) * HOUR_IN_SECONDS ),
			)
		);
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_tv_sample', 1 );
		update_post_meta( $id, 'tv_tickers', $s[4] );
		update_post_meta( $id, 'tv_sentiment', $s[5] );
		update_post_meta( $id, 'tv_horizon', $s[6] );
		update_post_meta( $id, 'tv_breaking', (bool) $s[7] );
		update_post_meta( $id, 'tv_reviewed_by', 'Sample Editor' );
		++$count;
	}
	return $count;
}

function tv_remove_samples() {
	$ids = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_tv_sample', // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	return count( $ids );
}

/* ---------------------------------------------------------------------------
 * Admin page
 * ------------------------------------------------------------------------ */

add_action( 'admin_post_tv_setup', 'tv_setup_request' );
function tv_setup_request() {
	check_admin_referer( 'tv_setup' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'tickervelocity' ) );
	}
	$task = isset( $_POST['task'] ) ? sanitize_key( wp_unslash( $_POST['task'] ) ) : '';
	if ( 'setup' === $task ) {
		$log = tv_run_setup();
	} elseif ( 'samples' === $task ) {
		/* translators: %d: number of articles */
		$log = array( sprintf( __( '%d sample articles added. Have a look at the site, then remove them before launch.', 'tickervelocity' ), tv_add_samples() ) );
	} elseif ( 'remove_samples' === $task ) {
		/* translators: %d: number of articles */
		$log = array( sprintf( __( '%d sample articles removed.', 'tickervelocity' ), tv_remove_samples() ) );
	} else {
		$log = array();
	}
	set_transient( 'tv_setup_log', $log, 120 );
	wp_safe_redirect( admin_url( 'admin.php?page=tickervelocity' ) );
	exit;
}

function tv_setup_button( $task, $label, $class = 'button' ) {
	printf(
		'<form method="post" action="%1$s" style="display:inline-block;margin-right:8px"><input type="hidden" name="action" value="tv_setup"><input type="hidden" name="task" value="%2$s">%3$s<button class="%4$s">%5$s</button></form>',
		esc_url( admin_url( 'admin-post.php' ) ),
		esc_attr( $task ),
		wp_nonce_field( 'tv_setup', '_wpnonce', true, false ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_attr( $class ),
		esc_html( $label )
	);
}

function tv_setup_page() {
	$log     = get_transient( 'tv_setup_log' );
	$samples = count(
		get_posts(
			array(
				'post_type'      => 'post',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_tv_sample', // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		)
	);
	delete_transient( 'tv_setup_log' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'TickerVelocity setup', 'tickervelocity' ); ?></h1>
		<?php if ( $log ) : ?>
			<div class="notice notice-success"><ul style="list-style:disc;margin-left:20px">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>

		<div class="card" style="max-width:820px">
			<h2><?php esc_html_e( '1. Build the site structure', 'tickervelocity' ); ?></h2>
			<p><?php esc_html_e( 'Creates the sections (Markets, Crypto, Macro, Commodities, Biotech, Regulation, Layoffs, Daily Brief) with sub-sections, the trust and legal pages, the menus, and sets the homepage. Nothing is deleted, and pages you already have are kept.', 'tickervelocity' ); ?></p>
			<?php tv_setup_button( 'setup', get_option( 'tv_setup_done' ) ? __( 'Run setup again', 'tickervelocity' ) : __( 'Set up TickerVelocity', 'tickervelocity' ), 'button button-primary button-hero' ); ?>
		</div>

		<div class="card" style="max-width:820px">
			<h2><?php esc_html_e( '2. Preview with sample articles (optional)', 'tickervelocity' ); ?></h2>
			<p><?php esc_html_e( 'Adds 8 clearly marked sample articles so you can see every layout before your workflow publishes real ones. Remove them before launch.', 'tickervelocity' ); ?></p>
			<?php
			tv_setup_button( 'samples', __( 'Add sample articles', 'tickervelocity' ) );
			if ( $samples ) {
				/* translators: %d: number of sample articles */
				tv_setup_button( 'remove_samples', sprintf( __( 'Remove %d sample articles', 'tickervelocity' ), $samples ) );
			}
			?>
		</div>

		<div class="card" style="max-width:820px">
			<h2><?php esc_html_e( '3. Finish in a few minutes', 'tickervelocity' ); ?></h2>
			<ol>
				<li><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=tv_market' ) ); ?>"><?php esc_html_e( 'Live prices', 'tickervelocity' ); ?></a>: <?php esc_html_e( 'crypto works straight away; add a free Finnhub key for stocks, index funds, gold and oil.', 'tickervelocity' ); ?></li>
				<li><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=tickervelocity' ) ); ?>"><?php esc_html_e( 'Customize > TickerVelocity', 'tickervelocity' ); ?></a>: <?php esc_html_e( 'newsletter (MailerLite or Beehiiv), editorial email, social links, referral link countries, disclaimers.', 'tickervelocity' ); ?></li>
				<li><a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>"><?php esc_html_e( 'Your profile', 'tickervelocity' ); ?></a>: <?php esc_html_e( 'a real name, photo, bio, job title and LinkedIn. Author credibility matters a lot in finance.', 'tickervelocity' ); ?></li>
				<li><a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>"><?php esc_html_e( 'Connect the Strategro SEO app', 'tickervelocity' ); ?></a>: <?php esc_html_e( 'add a user for it (role: Author, or Editor to publish without review), create an Application Password on that user\'s profile, and enter the site address, username and password in the app.', 'tickervelocity' ); ?></li>
				<li><?php esc_html_e( 'Review the legal pages (marked "Review before launch") and replace the placeholders.', 'tickervelocity' ); ?></li>
			</ol>
		</div>
	</div>
	<?php
}

/* ---------------------------------------------------------------------------
 * Page content
 * ------------------------------------------------------------------------ */

function tv_page_templates() {
	$review = '<p><em>Review before launch: this is a starting template, not legal advice. Replace anything in [brackets].</em></p>';
	$email  = tv_mod( 'tv_contact_email' );
	return array(
		'home'                 => array( 'Home', '' ),
		'latest'               => array( 'Latest', '' ),
		'about'                => array(
			'About TickerVelocity',
			'<p>TickerVelocity is a fast, independent markets newsroom. We cover the stories that move prices (central banks, earnings, crypto, commodities, biotech readouts, regulation and corporate job cuts) and explain why they matter, in plain English.</p>'
			. '<h2>What we do</h2><ul><li>Report market-moving news quickly, with the primary source linked in every article.</li><li>Lead every story with the key takeaways and the likely market impact, so you can get the point in thirty seconds.</li><li>Send The Morning Brief: the five things that matter, before the opening bell.</li></ul>'
			. '<h2>What we don\'t do</h2><p>We don\'t give personal financial advice, tell you what to buy, or take payment to cover a company or token. Partner links are always labelled and never influence what we write. See our <a href="/editorial-standards/">editorial standards</a> and <a href="/affiliate-disclosure/">affiliate disclosure</a>.</p>'
			. '<h2>Who we are</h2><p>[Founder name] founded TickerVelocity in [year]. [One or two sentences on relevant experience in markets, finance or technology.] TickerVelocity is published by [legal entity, registered address].</p>',
		),
		'editorial-standards'  => array(
			'Editorial standards and AI policy',
			'<p>Speed matters in markets, but accuracy matters more. These are the rules every TickerVelocity article follows.</p>'
			. '<h2>Sources</h2><p>Every article links to its primary sources: regulatory filings, central bank statements, official data releases, court documents and company announcements. We use secondary reporting only when we credit it.</p>'
			. '<h2>How we use AI</h2><p>We use AI tools to monitor markets, spot trending stories, summarise documents and draft first versions of articles. AI does not decide what is true. Before an article is published, an editor checks its facts, figures and quotes against the sources listed at the end of it, and the editor\'s name appears on the article. AI-generated images are labelled as illustrations.</p>'
			. '<h2>Market impact and sentiment</h2><p>The "Market impact" box is our editorial read of which assets a story is likely to affect and in which direction. It is not a forecast, a price target or investment advice.</p>'
			. '<h2>Independence</h2><p>We are not paid to cover any company, fund or token. Some articles contain affiliate links, always marked "Ad"; we may earn a commission, but partners have no say over our coverage.</p>'
			. '<h2 id="corrections">Corrections</h2><p>If we get something wrong, we fix it quickly and add a note to the article saying what changed. To report an error, email <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a> with the article link.</p>',
		),
		'disclaimer'           => array(
			'Disclaimer',
			$review
			. '<p>TickerVelocity publishes news and general commentary for information only. Nothing on this website, in our newsletter or on our social channels is financial, investment, legal or tax advice, a personal recommendation, or an offer or solicitation to buy or sell any security, cryptoasset or other investment.</p>'
			. '<p>We do not take your personal circumstances into account. Investments can go down as well as up and you may get back less than you invest. Cryptoassets are high-risk and largely unregulated; you could lose all the money you invest. Past performance is not a reliable indicator of future results.</p>'
			. '<p>Market data on this site is delayed and provided for information only. We work hard to be accurate, but we cannot guarantee that information is complete, current or free of errors. Before making any financial decision, do your own research and consider speaking to an independent, regulated financial adviser.</p>'
			. '<p>[Legal entity] is not authorised or regulated by the Financial Conduct Authority or any other financial regulator.</p>',
		),
		'affiliate-disclosure' => array(
			'Affiliate disclosure',
			$review
			. '<p>TickerVelocity is free to read. To fund it, some articles include affiliate (referral) links to products such as trading platforms or tools. If you sign up through one of these links, we may earn a commission at no extra cost to you.</p>'
			. '<ul><li>Affiliate links are marked "Ad", and articles that contain them say so at the top.</li><li>They never influence what we cover or what we say about a company or asset.</li><li>We only make them available where we believe they can lawfully be promoted. In some countries, including the UK and the US, they are shown as plain text.</li><li>An affiliate link is not a recommendation to invest. Check that a provider is authorised in your country and understand the risks first.</li></ul>'
			. '<p>Questions? Email <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>.</p>',
		),
		'privacy-policy'       => array(
			'Privacy policy',
			$review
			. '<p>This policy explains what personal data TickerVelocity ([legal entity], [address]) collects and why.</p>'
			. '<h2>Newsletter</h2><p>If you subscribe, we store your email address, the date you subscribed and the page you subscribed from. We use it only to send the newsletter you asked for. Our email provider is [provider, e.g. Beehiiv or MailerLite], which processes it on our behalf. Every email has a one-click unsubscribe link.</p>'
			. '<h2>Analytics</h2><p>[Describe your analytics tool, e.g. privacy-friendly analytics without cookies, or Google Analytics with consent.]</p>'
			. '<h2>Affiliate links</h2><p>When you click an affiliate link, the partner may set cookies to record that you came from TickerVelocity. Their own privacy policy applies.</p>'
			. '<h2>Your rights</h2><p>You can ask to see, correct or delete your data at any time by emailing <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>. If you are in the UK or EU you can also complain to your data protection authority (in the UK, the ICO).</p>',
		),
		'terms'                => array(
			'Terms of use',
			$review
			. '<p>By using TickerVelocity you agree to these terms.</p><h2>Content</h2><p>All content is for information only and is not advice (see our <a href="/disclaimer/">disclaimer</a>). You may share links and short quotes with credit; please don\'t republish whole articles.</p><h2>Liability</h2><p>To the extent the law allows, we are not liable for any loss arising from your use of this website or reliance on its content.</p><h2>Law</h2><p>These terms are governed by the laws of [England and Wales].</p>',
		),
		'contact'              => array(
			'Contact',
			'<p>News tips, corrections and partnership enquiries: <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>.</p><p>For corrections, please include the article link and what needs fixing. We read every message.</p>',
		),
		'markets'              => array(
			'Live prices',
			'[tv_prices]<p>Prices may be delayed and are for information only, not investment advice. Crypto data by CoinGecko; stocks and funds by Finnhub.</p>',
		),
		'newsletter'           => array(
			'Newsletter',
			'[tv_newsletter]<h2>What you get</h2><p>Every weekday before the opening bell: the five stories that moved markets, what they mean, and the data and events to watch today. Five minutes, no hype.</p>',
		),
	);
}
