<?php
/**
 * Appearance > Customize > TickerVelocity: brand, homepage, newsletter,
 * market strip and disclaimer settings.
 *
 * @package TickerVelocity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tv_defaults() {
	return array(
		'tv_tagline'           => 'Markets, decoded. Fast.',
		'tv_contact_email'     => 'editor@tickervelocity.com',
		'tv_x_url'             => '',
		'tv_linkedin_url'      => '',
		'tv_home_sections'     => 'markets, crypto, macro, commodities, biotech, regulation, layoffs',
		'tv_brief_category'    => 'daily-brief',
		'tv_newsletter_name'   => 'The Morning Brief',
		'tv_newsletter_pitch'  => 'The five market moves that matter, in your inbox before the opening bell. Free, every weekday. Unsubscribe in one click.',
		'tv_subscribe_label'   => 'Subscribe',
		'tv_newsletter_via'    => 'wordpress',
		'tv_newsletter_key'    => '',
		'tv_newsletter_list'   => '',
		'tv_newsletter_hook'   => '',
		'tv_crypto_assets'     => 'BTC:bitcoin:Bitcoin, ETH:ethereum:Ethereum, SOL:solana:Solana, XRP:ripple:XRP, BNB:binancecoin:BNB, DOGE:dogecoin:Dogecoin',
		'tv_stock_assets'      => 'SPY:S&P 500 (SPY), QQQ:Nasdaq 100 (QQQ), DIA:Dow Jones (DIA), NVDA:Nvidia, AAPL:Apple, MSFT:Microsoft, TSLA:Tesla, AMZN:Amazon',
		'tv_commodity_assets'  => 'GLD:Gold (GLD), SLV:Silver (SLV), USO:Oil (USO), UNG:Natural gas (UNG)',
		'tv_finnhub_key'       => '',
		'tv_coingecko_key'     => '',
		'tv_ref_domains'       => 'mexc.com, binance.com, bybit.com, okx.com, kucoin.com, bitget.com, gate.io, gate.com, htx.com, coinbase.com, kraken.com, crypto.com, bingx.com, etoro.com',
		'tv_ref_geo'           => true,
		'tv_ref_blocked'       => 'GB, US',
		'tv_ref_unknown'       => 'hide',
		'tv_autosort'          => true,
		'tv_disclaimer'        => 'TickerVelocity publishes news and analysis for information only. Nothing on this site is financial, investment or tax advice, or a recommendation to buy or sell any asset. Prices move fast and can fall as well as rise. Do your own research, or speak to a regulated adviser, before making any decision.',
		'tv_crypto_risk'       => 'Don\'t invest unless you\'re prepared to lose all the money you invest. Cryptoassets are high-risk, and you are unlikely to be protected if something goes wrong.',
		'tv_ai_note'           => 'Our newsroom uses AI tools to monitor markets and draft first versions. Every article is checked against its sources before publishing, and named editors are accountable for what we publish.',
		'tv_comments'          => false,
	);
}

function tv_mod( $key ) {
	$defaults = tv_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

add_action( 'customize_register', 'tv_customize_register' );
function tv_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'tickervelocity',
		array(
			'title'    => __( 'TickerVelocity', 'tickervelocity' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'tv_brand'      => __( 'Brand and contact', 'tickervelocity' ),
		'tv_home'       => __( 'Homepage', 'tickervelocity' ),
		'tv_newsletter' => __( 'Newsletter', 'tickervelocity' ),
		'tv_market'     => __( 'Live prices', 'tickervelocity' ),
		'tv_refs'       => __( 'Referral links', 'tickervelocity' ),
		'tv_legal'      => __( 'Disclaimers', 'tickervelocity' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title' => $title,
				'panel' => 'tickervelocity',
			)
		);
	}

	$fields = array(
		'tv_tagline'          => array( 'tv_brand', __( 'Tagline (header and footer)', 'tickervelocity' ), 'text', '' ),
		'tv_contact_email'    => array( 'tv_brand', __( 'Editorial email (contact and corrections)', 'tickervelocity' ), 'email', '' ),
		'tv_x_url'            => array( 'tv_brand', __( 'X (Twitter) profile URL', 'tickervelocity' ), 'url', '' ),
		'tv_linkedin_url'     => array( 'tv_brand', __( 'LinkedIn page URL', 'tickervelocity' ), 'url', '' ),
		'tv_home_sections'    => array( 'tv_home', __( 'Category rows on the homepage', 'tickervelocity' ), 'text', __( 'Category slugs, in order, separated by commas.', 'tickervelocity' ) ),
		'tv_brief_category'   => array( 'tv_home', __( 'Daily brief category', 'tickervelocity' ), 'text', __( 'Slug of the category your morning brief is posted in. Its newest post is featured on the homepage.', 'tickervelocity' ) ),
		'tv_newsletter_name'  => array( 'tv_newsletter', __( 'Newsletter name', 'tickervelocity' ), 'text', '' ),
		'tv_newsletter_pitch' => array( 'tv_newsletter', __( 'Newsletter pitch', 'tickervelocity' ), 'textarea', '' ),
		'tv_subscribe_label'  => array( 'tv_newsletter', __( 'Header button text', 'tickervelocity' ), 'text', '' ),
		'tv_newsletter_key'   => array( 'tv_newsletter', __( 'Email tool API key', 'tickervelocity' ), 'text', __( 'MailerLite: Integrations > API. Beehiiv: Settings > API (if your plan includes it).', 'tickervelocity' ) ),
		'tv_newsletter_list'  => array( 'tv_newsletter', __( 'MailerLite group ID or Beehiiv publication ID', 'tickervelocity' ), 'text', __( 'Optional for MailerLite (adds signups to that group). Required for Beehiiv (starts with pub_).', 'tickervelocity' ) ),
		'tv_newsletter_hook'  => array( 'tv_newsletter', __( 'Webhook URL', 'tickervelocity' ), 'url', __( 'Used when "Send new subscribers to" is set to a webhook: each signup is POSTed here as JSON (email, source, time).', 'tickervelocity' ) ),
		'tv_crypto_assets'    => array( 'tv_market', __( 'Crypto', 'tickervelocity' ), 'textarea', __( 'SYMBOL:coingecko-id:Name, separated by commas. Find the id in the address of the coin on coingecko.com. Free, no key needed.', 'tickervelocity' ) ),
		'tv_stock_assets'     => array( 'tv_market', __( 'Stocks and index funds', 'tickervelocity' ), 'textarea', __( 'SYMBOL:Name, separated by commas (US-listed). Index funds such as SPY track the S&P 500. Needs the Finnhub key below.', 'tickervelocity' ) ),
		'tv_commodity_assets' => array( 'tv_market', __( 'Commodities', 'tickervelocity' ), 'textarea', __( 'SYMBOL:Name of funds that track a commodity, for example GLD for gold. Needs the Finnhub key.', 'tickervelocity' ) ),
		'tv_finnhub_key'      => array( 'tv_market', __( 'Finnhub API key (free)', 'tickervelocity' ), 'text', __( 'Free from finnhub.io: sign up, copy the key from the dashboard. Without it only crypto prices show.', 'tickervelocity' ) ),
		'tv_coingecko_key'    => array( 'tv_market', __( 'CoinGecko demo API key (optional)', 'tickervelocity' ), 'text', __( 'Free from coingecko.com. Makes crypto prices more reliable.', 'tickervelocity' ) ),
		'tv_ref_domains'      => array( 'tv_refs', __( 'Platforms whose sign-up links are referral links', 'tickervelocity' ), 'textarea', __( 'Sign-up, invite and join links to these sites are treated as referral links, as is any link with a referral code (?ref=, ?inviteCode=...) or marked rel="sponsored". Ordinary links to them (news, announcements) are left alone.', 'tickervelocity' ) ),
		'tv_ref_blocked'      => array( 'tv_refs', __( 'Countries where referral links are not clickable', 'tickervelocity' ), 'text', __( 'Two-letter codes separated by commas. Visitors there see the words without the link.', 'tickervelocity' ) ),
		'tv_disclaimer'       => array( 'tv_legal', __( 'Article disclaimer', 'tickervelocity' ), 'textarea', '' ),
		'tv_crypto_risk'      => array( 'tv_legal', __( 'Crypto risk warning (shown above articles with referral links)', 'tickervelocity' ), 'textarea', '' ),
		'tv_ai_note'          => array( 'tv_legal', __( 'How we use AI (shown under every article)', 'tickervelocity' ), 'textarea', '' ),
	);
	$sanitize = array(
		'select'   => 'sanitize_key',
		'text'     => 'sanitize_text_field',
		'textarea' => 'sanitize_textarea_field',
		'email'    => 'sanitize_email',
		'url'      => 'esc_url_raw',
	);
	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => tv_mod( $id ),
				'sanitize_callback' => $sanitize[ $field[2] ],
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $field[1],
				'description' => $field[3],
				'type'        => $field[2],
				'section'     => $field[0],
			)
		);
	}

	$choices = array(
		'tv_newsletter_via' => array(
			'tv_newsletter',
			__( 'Send new subscribers to', 'tickervelocity' ),
			array(
				'wordpress'  => __( 'Keep them in WordPress only (Subscribers, CSV export)', 'tickervelocity' ),
				'mailerlite' => 'MailerLite',
				'beehiiv'    => 'Beehiiv',
				'webhook'    => __( 'A webhook', 'tickervelocity' ),
			),
			__( 'Subscribers are always kept in WordPress as well.', 'tickervelocity' ),
		),
		'tv_ref_unknown'    => array(
			'tv_refs',
			__( 'When a visitor\'s country can\'t be detected', 'tickervelocity' ),
			array(
				'hide' => __( 'Don\'t make referral links clickable (safer)', 'tickervelocity' ),
				'show' => __( 'Make them clickable', 'tickervelocity' ),
			),
			__( 'Country detection needs Cloudflare (free) with IP Geolocation on, or a host that provides it.', 'tickervelocity' ),
		),
	);
	foreach ( $choices as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => tv_mod( $id ),
				'sanitize_callback' => 'sanitize_key',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $field[1],
				'description' => $field[3],
				'type'        => 'select',
				'choices'     => $field[2],
				'section'     => $field[0],
			)
		);
	}

	$checks = array(
		'tv_ref_geo'  => array( 'tv_refs', __( 'Check the visitor\'s country before showing referral links', 'tickervelocity' ), __( 'Recommended. Off: referral links are always clickable (still labelled "Ad" and marked sponsored).', 'tickervelocity' ), true ),
		'tv_autosort' => array( 'tv_home', __( 'Sort new articles into sections automatically', 'tickervelocity' ), __( 'For articles that arrive without a section (left in Uncategorized). A section chosen by the writer is always kept.', 'tickervelocity' ), true ),
	);
	foreach ( $checks as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $field[3],
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $field[1],
				'description' => $field[2],
				'type'        => 'checkbox',
				'section'     => $field[0],
			)
		);
	}

	$wp_customize->add_setting(
		'tv_comments',
		array(
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'tv_comments',
		array(
			'label'   => __( 'Allow comments on articles', 'tickervelocity' ),
			'type'    => 'checkbox',
			'section' => 'tv_home',
		)
	);
}
