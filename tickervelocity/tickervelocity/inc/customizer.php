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
		'tv_newsletter_hook'   => '',
		'tv_market_crypto'     => 'BTC:bitcoin, ETH:ethereum, SOL:solana, XRP:ripple, BNB:binancecoin',
		'tv_coingecko_key'     => '',
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
		'tv_market'     => __( 'Market strip', 'tickervelocity' ),
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
		'tv_newsletter_hook'  => array( 'tv_newsletter', __( 'Send new subscribers to (webhook URL)', 'tickervelocity' ), 'url', __( 'Usually an n8n Webhook node that adds the email to Beehiiv, MailerLite or similar. Subscribers are also kept in Subscribers in the admin.', 'tickervelocity' ) ),
		'tv_market_crypto'    => array( 'tv_market', __( 'Crypto in the market strip', 'tickervelocity' ), 'text', __( 'SYMBOL:coingecko-id pairs, separated by commas. Prices come from CoinGecko every five minutes. Indices, stocks and commodities can be sent by n8n (see the README).', 'tickervelocity' ) ),
		'tv_coingecko_key'    => array( 'tv_market', __( 'CoinGecko demo API key (optional)', 'tickervelocity' ), 'text', __( 'Free from coingecko.com. Makes price updates more reliable.', 'tickervelocity' ) ),
		'tv_disclaimer'       => array( 'tv_legal', __( 'Article disclaimer', 'tickervelocity' ), 'textarea', '' ),
		'tv_crypto_risk'      => array( 'tv_legal', __( 'Crypto risk warning (shown with partner offers)', 'tickervelocity' ), 'textarea', '' ),
		'tv_ai_note'          => array( 'tv_legal', __( 'How we use AI (shown under every article)', 'tickervelocity' ), 'textarea', '' ),
	);
	$sanitize = array(
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
