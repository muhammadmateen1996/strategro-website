<?php
/**
 * Builds the Strategro pages as real Elementor data: page designs below,
 * and strategro_install_site() which writes them (plus posts, menus and
 * Elementor's global colours/fonts) into the site.
 *
 * Used by Appearance > Strategro Setup (one click, no import tools needed)
 * and by wordpress/build/build-site.php (WP-CLI).
 *
 * Only free Elementor widgets are used (Heading, Text Editor, Button, Icon,
 * HTML, Shortcode + Containers), so it works with or without Elementor Pro.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------------
 * Elementor element helpers
 * ------------------------------------------------------------------------ */

function strategro_b_id() {
	return substr( bin2hex( random_bytes( 4 ) ), 0, 7 );
}

function strategro_b_pad( $top, $right = null, $bottom = null, $left = null ) {
	$right  = null === $right ? $top : $right;
	$bottom = null === $bottom ? $top : $bottom;
	$left   = null === $left ? $right : $left;
	return array(
		'unit'     => 'px',
		'top'      => (string) $top,
		'right'    => (string) $right,
		'bottom'   => (string) $bottom,
		'left'     => (string) $left,
		'isLinked' => false,
	);
}

function strategro_b_gap( $size ) {
	return array(
		'unit'     => 'px',
		'row'      => (string) $size,
		'column'   => (string) $size,
		'isLinked' => true,
	);
}

/** Container. $opts are raw Elementor settings merged over sensible defaults. */
function strategro_b_con( $children, $opts = array(), $inner = true ) {
	$settings = array_merge(
		array(
			'content_width'  => $inner ? 'full' : 'boxed',
			'flex_direction' => 'column',
			'flex_gap'       => strategro_b_gap( 0 ),
			'padding'        => strategro_b_pad( 0 ),
		),
		$opts
	);
	return array(
		'id'       => strategro_b_id(),
		'elType'   => 'container',
		'settings' => array_filter( $settings, fn( $v ) => null !== $v ),
		'elements' => array_values( array_filter( $children ) ),
		'isInner'  => $inner,
	);
}

/** Top-level page section: full-bleed background, boxed content. */
function strategro_b_section( $classes, $children, $opts = array() ) {
	return strategro_b_con(
		$children,
		array_merge(
			array(
				'css_classes'    => $classes,
				'flex_gap'       => strategro_b_gap( 28 ),
				'padding'        => strategro_b_pad( 120, 24, 120, 24 ),
				'padding_mobile' => strategro_b_pad( 80, 20, 80, 20 ),
			),
			$opts
		),
		false
	);
}

function strategro_b_row( $children, $opts = array() ) {
	return strategro_b_con(
		$children,
		array_merge(
			array(
				'flex_direction'        => 'row',
				'flex_direction_mobile' => 'column',
				'flex_gap'              => strategro_b_gap( 48 ),
				'flex_align_items'      => 'center',
			),
			$opts
		)
	);
}

function strategro_b_col( $children, $width = 50, $opts = array() ) {
	return strategro_b_con(
		$children,
		array_merge(
			array(
				'width'        => array( 'unit' => '%', 'size' => $width ),
				'width_tablet' => array( 'unit' => '%', 'size' => 100 ),
				'width_mobile' => array( 'unit' => '%', 'size' => 100 ),
				'flex_gap'     => strategro_b_gap( 22 ),
			),
			$opts
		)
	);
}

function strategro_b_grid( $cols, $children, $opts = array() ) {
	return strategro_b_con(
		$children,
		array_merge(
			array(
				'container_type'           => 'grid',
				'grid_columns_grid'        => array( 'unit' => 'fr', 'size' => $cols ),
				'grid_columns_grid_tablet' => array( 'unit' => 'fr', 'size' => min( 2, $cols ) ),
				'grid_columns_grid_mobile' => array( 'unit' => 'fr', 'size' => 1 ),
				'grid_rows_grid'           => array( 'unit' => 'custom', 'size' => 'auto' ),
				'grid_gaps'                => strategro_b_gap( 24 ),
			),
			$opts
		)
	);
}

function strategro_b_widget( $type, $settings ) {
	return array(
		'id'         => strategro_b_id(),
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

function strategro_b_heading( $text, $tag = 'h2', $classes = '', $opts = array() ) {
	return strategro_b_widget( 'heading', array_merge( array( 'title' => $text, 'header_size' => $tag, '_css_classes' => $classes ), $opts ) );
}

function strategro_b_text( $html, $classes = '', $opts = array() ) {
	return strategro_b_widget( 'text-editor', array_merge( array( 'editor' => $html, '_css_classes' => $classes ), $opts ) );
}

function strategro_b_link( $url ) {
	$external = 0 === strpos( $url, 'http' );
	return array(
		'url'               => $url,
		'is_external'       => $external ? 'on' : '',
		'nofollow'          => '',
		'custom_attributes' => '',
	);
}

function strategro_b_button( $label, $url, $classes = 'sg-btn sg-magnetic sg-arrow' ) {
	return strategro_b_widget(
		'button',
		array(
			'text'         => $label,
			'link'         => strategro_b_link( $url ),
			'_css_classes' => $classes,
		)
	);
}

function strategro_b_html( $html, $classes = '' ) {
	return strategro_b_widget( 'html', array( 'html' => $html, '_css_classes' => $classes ) );
}

function strategro_b_icon( $fa ) {
	return strategro_b_widget(
		'icon',
		array(
			'selected_icon' => array( 'value' => 'fas fa-' . $fa, 'library' => 'fa-solid' ),
			'align'         => 'left',
		)
	);
}

function strategro_b_buttons( $buttons ) {
	return strategro_b_con(
		$buttons,
		array(
			'flex_direction'        => 'row',
			'flex_direction_mobile' => 'row',
			'flex_wrap'             => 'wrap',
			'flex_gap'              => strategro_b_gap( 14 ),
			'flex_align_items'      => 'center',
		)
	);
}

/** Eyebrow + title (+ optional lead) block used at the top of most sections. */
function strategro_b_intro( $eyebrow, $title, $lead = '', $center = false ) {
	$align = $center ? array( 'align' => 'center' ) : array();
	return strategro_b_con(
		array(
			strategro_b_heading( $eyebrow, 'p', 'sg-eyebrow sg-reveal', $align ),
			strategro_b_heading( $title, 'h2', 'sg-title sg-split', $align ),
			$lead ? strategro_b_text( '<p>' . $lead . '</p>', 'sg-lead sg-reveal', $center ? array( 'align' => 'center' ) : array() ) : null,
		),
		array(
			'flex_gap'          => strategro_b_gap( 18 ),
			'width'             => array( 'unit' => '%', 'size' => $center ? 70 : 62 ),
			'width_tablet'      => array( 'unit' => '%', 'size' => 100 ),
			'width_mobile'      => array( 'unit' => '%', 'size' => 100 ),
			'_flex_align_self' => $center ? 'center' : 'flex-start',
		)
	);
}

function strategro_b_hero( $chip, $title, $lead, $extra = array(), $classes = 'sg-dark sg-network sg-spot sg-aurora' ) {
	return strategro_b_section(
		$classes,
		array_merge(
			array(
				strategro_b_heading( $chip, 'p', 'sg-chip sg-reveal' ),
				strategro_b_heading( $title, 'h1', 'sg-display sg-split', array( '_element_width' => 'initial', '_element_custom_width' => array( 'unit' => '%', 'size' => 85 ), '_element_custom_width_mobile' => array( 'unit' => '%', 'size' => 100 ) ) ),
				strategro_b_text( '<p>' . $lead . '</p>', 'sg-lead sg-reveal', array( '_element_width' => 'initial', '_element_custom_width' => array( 'unit' => '%', 'size' => 58 ), '_element_custom_width_mobile' => array( 'unit' => '%', 'size' => 100 ) ) ),
			),
			$extra
		),
		array(
			'padding'        => strategro_b_pad( 150, 24, 130, 24 ),
			'padding_mobile' => strategro_b_pad( 100, 20, 90, 20 ),
			'flex_gap'       => strategro_b_gap( 26 ),
			'min_height'     => array( 'unit' => 'vh', 'size' => 72 ),
			'flex_justify_content' => 'center',
		)
	);
}

function strategro_b_cta( $title, $lead, $label = 'Book an AI Systems Audit', $url = '/contact/' ) {
	return strategro_b_section(
		'sg-gold sg-spot',
		array(
			strategro_b_heading( $title, 'h2', 'sg-title sg-split', array( 'align' => 'center' ) ),
			$lead ? strategro_b_text( '<p>' . $lead . '</p>', 'sg-lead sg-reveal', array( 'align' => 'center' ) ) : null,
			strategro_b_con(
				array( strategro_b_button( $label, $url, 'sg-btn-dark sg-magnetic sg-arrow sg-reveal' ) ),
				array( 'flex_align_items' => 'center', 'padding' => strategro_b_pad( 12, 0, 0, 0 ) )
			),
		),
		array(
			'flex_align_items' => 'center',
			'flex_gap'         => strategro_b_gap( 18 ),
			'padding'          => strategro_b_pad( 110, 24, 110, 24 ),
		)
	);
}

function strategro_b_steps() {
	return array(
		array( 'Discover', 'We map how work actually moves today, including the manual steps nobody thinks to mention.' ),
		array( 'Design', 'You see exactly what will change and where automation fits before any development starts.' ),
		array( 'Build', 'We connect your tools and test against real scenarios with your team before going live.' ),
		array( 'Optimise', 'Systems are reviewed against real usage, so accuracy improves as your business changes.' ),
	);
}

function strategro_b_steps_section( $steps, $title ) {
	$cards = array();
	foreach ( $steps as $i => $step ) {
		$cards[] = strategro_b_con(
			array(
				strategro_b_heading( sprintf( '%02d', $i + 1 ), 'p', 'sg-eyebrow' ),
				strategro_b_heading( $step[0], 'h3', 'sg-h3' ),
				strategro_b_text( '<p>' . $step[1] . '</p>', 'sg-small' ),
			),
			array(
				'css_classes' => 'sg-glass sg-step sg-glow',
				'padding'     => strategro_b_pad( 30 ),
				'flex_gap'    => strategro_b_gap( 12 ),
			)
		);
	}
	return strategro_b_section(
		'sg-dark sg-aurora sg-steps',
		array(
			strategro_b_intro( 'How we build', $title ),
			strategro_b_con( array(), array( 'css_classes' => 'sg-line', 'margin' => array( 'unit' => 'px', 'top' => '24', 'right' => '0', 'bottom' => '8', 'left' => '0', 'isLinked' => false ) ) ),
			strategro_b_grid( 4, $cards ),
		)
	);
}

function strategro_b_icons() {
	return array(
		'clara'    => 'robot',
		'lodway'   => 'shipping-fast',
		'seo'      => 'chart-line',
		'proposal' => 'file-signature',
		'shopops'  => 'shopping-bag',
	);
}

function strategro_b_domain( $url ) {
	return preg_replace( '#^https?://(www\.)?#', '', rtrim( $url, '/' ) );
}

/* ---------------------------------------------------------------------------
 * Pages
 * ------------------------------------------------------------------------ */

function strategro_b_home( $content, $steps, $icons ) {
	$feed_items = array(
		array( 'CL', 'Clara answered an after-hours enquiry and booked a call' ),
		array( 'LW', 'Lodway dispatched a same-day job to the nearest driver' ),
		array( 'SEO', 'Strategro SEO queued a new article for review' ),
		array( 'PR', 'Strategro Proposals drafted a proposal from a new lead' ),
		array( 'SO', 'ShopOps flagged a listing draft ready to approve' ),
		array( 'LW', 'Lodway captured proof of delivery with a signature' ),
		array( 'CL', 'Clara qualified a website visitor and sent the details on' ),
	);
	$feed  = '<div class="sg-feed" data-visible="4"><div class="sg-feed__head"><span class="sg-feed__dot"></span>System activity<span class="sg-feed__tag">Example</span></div><ul class="sg-feed__list">';
	foreach ( $feed_items as $item ) {
		$feed .= '<li><b>' . $item[0] . '</b><span>' . $item[1] . '</span><time>now</time></li>';
	}
	$feed .= '</ul></div>';

	$hero = strategro_b_section(
		'sg-dark sg-hero sg-network sg-spot sg-aurora',
		array(
			strategro_b_row(
				array(
					strategro_b_col(
						array(
							strategro_b_heading( 'Signal to System', 'p', 'sg-chip sg-reveal' ),
							strategro_b_heading( 'Turn operational drag into <span class="sg-gradient-text">intelligent systems.</span>', 'h1', 'sg-display sg-split' ),
							strategro_b_text( '<p>Strategro builds AI products for the work that slips through the cracks: enquiries, courier jobs, search content, proposals and online orders. Use one of ours, or have us build one around how you work.</p>', 'sg-lead sg-reveal' ),
							strategro_b_buttons(
								array(
									strategro_b_button( 'Explore our products', '#products', 'sg-btn sg-magnetic sg-arrow sg-reveal' ),
									strategro_b_button( 'Book a demo', '/contact/', 'sg-btn-ghost sg-magnetic sg-reveal' ),
								)
							),
						),
						56,
						array( 'flex_gap' => strategro_b_gap( 26 ) )
					),
					strategro_b_col( array( strategro_b_html( $feed, 'sg-reveal sg-tilt sg-parallax' ) ), 44 ),
				),
				array( 'flex_gap' => strategro_b_gap( 56 ) )
			),
			strategro_b_html( '<span class="sg-scroll-cue"><i></i>Scroll</span>', 'sg-reveal' ),
		),
		array(
			'padding'              => strategro_b_pad( 110, 24, 70, 24 ),
			'padding_mobile'       => strategro_b_pad( 70, 20, 60, 20 ),
			'flex_justify_content' => 'space-between',
			'flex_gap'             => strategro_b_gap( 60 ),
		)
	);

	$marquee_items = array();
	foreach ( $content['products'] as $product ) {
		$marquee_items[] = strategro_b_heading( $product['name'], 'span' );
	}
	$marquee = strategro_b_section(
		'sg-dark sg-marquee',
		$marquee_items,
		array(
			'content_width'  => 'full',
			'flex_direction' => 'row',
			'flex_gap'       => strategro_b_gap( 0 ),
			'padding'        => strategro_b_pad( 34, 0, 34, 0 ),
			'border_border'  => 'solid',
			'border_width'   => array( 'unit' => 'px', 'top' => '1', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ),
			'border_color'   => 'rgba(250,248,242,0.08)',
		)
	);

	$cards = array();
	foreach ( $content['products'] as $i => $product ) {
		$cards[] = strategro_b_con(
			array(
				strategro_b_con(
					array(
						strategro_b_con(
							array(
								strategro_b_icon( $icons[ $product['slug'] ] ),
								strategro_b_heading( sprintf( '%02d', $i + 1 ), 'span', 'sg-index' ),
							),
							array(
								'flex_direction'        => 'row',
								'flex_direction_mobile' => 'row',
								'flex_justify_content'  => 'space-between',
								'flex_align_items'      => 'flex-start',
							)
						),
						strategro_b_heading( $product['tagline'], 'p', 'sg-eyebrow' ),
						strategro_b_heading( $product['name'], 'h3', 'sg-h3 sg-product-name' ),
						strategro_b_text( '<p>' . $product['description'] . '</p>' ),
					),
					array( 'flex_gap' => strategro_b_gap( 16 ) )
				),
				strategro_b_con(
					array(
						strategro_b_heading( $product['audience'], 'p', 'sg-meta' ),
						strategro_b_heading( strategro_b_domain( $product['url'] ), 'span', 'sg-domain' ),
					),
					array(
						'css_classes'           => 'sg-divided',
						'flex_direction'        => 'row',
						'flex_direction_mobile' => 'row',
						'flex_wrap'             => 'wrap',
						'flex_justify_content'  => 'space-between',
						'flex_align_items'      => 'center',
						'flex_gap'              => strategro_b_gap( 12 ),
						'padding'               => strategro_b_pad( 20, 0, 0, 0 ),
					)
				),
			),
			array(
				'css_classes'          => 'sg-product sg-orbit sg-tilt sg-glow',
				'html_tag'             => 'a',
				'link'                 => strategro_b_link( $product['url'] ),
				'width'                => array( 'unit' => 'px', 'size' => 440 ),
				'width_tablet'         => array( 'unit' => '%', 'size' => 100 ),
				'width_mobile'         => array( 'unit' => '%', 'size' => 100 ),
				'padding'              => strategro_b_pad( 34 ),
				'padding_mobile'       => strategro_b_pad( 26 ),
				'flex_justify_content' => 'space-between',
				'flex_gap'             => strategro_b_gap( 28 ),
			)
		);
	}

	$products = strategro_b_section(
		'sg-dark sg-grid-bg sg-hscroll',
		array(
			strategro_b_con(
				array(
					strategro_b_row(
						array(
							strategro_b_intro( 'Our products', 'Five products. Each one takes a job off someone’s plate.', 'Every Strategro product started as a system we built for a real operational problem. Now any business can use it.' ),
							strategro_b_heading( 'Scroll to explore &rarr;', 'p', 'sg-meta sg-reveal', array( 'hide_mobile' => 'hidden-mobile', 'hide_tablet' => 'hidden-tablet' ) ),
						),
						array( 'flex_justify_content' => 'space-between', 'flex_align_items' => 'flex-end' )
					),
				),
				array( 'content_width' => 'boxed', 'padding' => strategro_b_pad( 0, 24, 0, 24 ) )
			),
			strategro_b_con(
				$cards,
				array(
					'css_classes'           => 'sg-hscroll-track',
					'flex_direction'        => 'row',
					'flex_direction_tablet' => 'column',
					'flex_direction_mobile' => 'column',
					'flex_gap'              => strategro_b_gap( 24 ),
					'flex_align_items'      => 'stretch',
					// No desktop padding: the theme lines the cards up with the page content.
					'padding'               => null,
					'padding_tablet'        => strategro_b_pad( 0, 24, 0, 24 ),
					'padding_mobile'        => strategro_b_pad( 0, 20, 0, 20 ),
				)
			),
		),
		array(
			'_element_id'    => 'products',
			'content_width'  => 'full',
			'flex_gap'       => strategro_b_gap( 56 ),
			'padding'        => strategro_b_pad( 120, 0, 120, 0 ),
			'padding_mobile' => strategro_b_pad( 80, 0, 80, 0 ),
		)
	);

	$stat_data = array(
		array( '5', 'Live products, built and run by Strategro', true ),
		array( '60–95%', 'Typical reduction in manual workflow time across delivered projects', true ),
		array( '95%', 'Faster freight quotes for Peerz Ltd, from 5–10 minutes to seconds', true ),
		array( '24/7', 'Coverage from our deployed chatbot and voice systems', false ),
	);
	$stats = array();
	foreach ( $stat_data as $stat ) {
		$stats[] = strategro_b_con(
			array(
				strategro_b_heading( $stat[0], 'p', 'sg-stat' . ( $stat[2] ? ' sg-counter' : '' ) ),
				strategro_b_text( '<p>' . $stat[1] . '</p>', 'sg-small' ),
			),
			array( 'flex_gap' => strategro_b_gap( 10 ), 'padding' => strategro_b_pad( 24, 0, 0, 0 ), 'css_classes' => 'sg-divided' )
		);
	}
	$stats_section = strategro_b_section(
		'sg-paper',
		array(
			strategro_b_intro( 'Track record', 'Numbers from real deployments.' ),
			strategro_b_grid( 4, $stats, array( 'css_classes' => 'sg-stagger', 'grid_gaps' => strategro_b_gap( 32 ), 'grid_columns_grid_mobile' => array( 'unit' => 'fr', 'size' => 2 ) ) ),
		)
	);

	$peerz   = $content['useCases'][0];
	$flow    = '<ul><li>' . implode( '</li><li>', $peerz['systemFlow'] ) . '</li></ul>';
	$results = '<ul><li>' . implode( '</li><li>', $peerz['results'] ) . '</li></ul>';
	$case    = strategro_b_section(
		'sg-light sg-grid-bg',
		array(
			strategro_b_row(
				array(
					strategro_b_col(
						array(
							strategro_b_heading( 'Real project · ' . $peerz['client'], 'p', 'sg-eyebrow sg-reveal' ),
							strategro_b_heading( $peerz['title'], 'h2', 'sg-title sg-split' ),
							strategro_b_text( '<p>' . $peerz['scenario'] . '</p>', 'sg-lead sg-reveal' ),
							strategro_b_text( $flow, 'sg-checks sg-reveal' ),
						),
						52
					),
					strategro_b_col(
						array(
							strategro_b_heading( 'Results', 'p', 'sg-eyebrow' ),
							strategro_b_text( $results, 'sg-checks' ),
						),
						48,
						array(
							'css_classes' => 'sg-dark sg-orbit sg-tilt sg-reveal',
							'padding'     => strategro_b_pad( 40 ),
							'flex_gap'    => strategro_b_gap( 20 ),
						)
					),
				),
				array( 'flex_gap' => strategro_b_gap( 64 ) )
			),
		)
	);

	$pills = array( strategro_b_heading( 'Built on', 'p', 'sg-eyebrow' ) );
	foreach ( array( 'n8n', 'OpenAI', 'WhatsApp Business API', 'Google Workspace' ) as $tool ) {
		$pills[] = strategro_b_heading( $tool, 'span', 'sg-pill' );
	}
	$built_on = strategro_b_section(
		'sg-dark',
		array(
			strategro_b_con(
				$pills,
				array(
					'css_classes'           => 'sg-stagger',
					'flex_direction'        => 'row',
					'flex_direction_mobile' => 'row',
					'flex_wrap'             => 'wrap',
					'flex_align_items'      => 'center',
					'flex_gap'              => strategro_b_gap( 12 ),
				)
			),
		),
		array( 'padding' => strategro_b_pad( 44, 24, 44, 24 ) )
	);

	$service_cards = array();
	foreach ( $content['services'] as $service ) {
		$service_cards[] = strategro_b_con(
			array(
				strategro_b_heading( $service['name'], 'h3', 'sg-h3' ),
				strategro_b_text( '<p>' . $service['outcome'] . '</p>', 'sg-small' ),
			),
			array(
				'css_classes' => 'sg-card sg-glow',
				'html_tag'    => 'a',
				'link'        => strategro_b_link( '/custom-builds/#' . $service['slug'] ),
				'padding'     => strategro_b_pad( 24 ),
				'flex_gap'    => strategro_b_gap( 8 ),
			)
		);
	}
	$custom = strategro_b_section(
		'sg-light',
		array(
			strategro_b_row(
				array(
					strategro_b_col(
						array(
							strategro_b_heading( 'Custom builds', 'p', 'sg-eyebrow sg-reveal' ),
							strategro_b_heading( 'Need a system that doesn’t exist yet?', 'h2', 'sg-title sg-split' ),
							strategro_b_text( '<p>Our products began as custom builds, and we still build them: workflow automation, chatbots, voice receptionists, knowledge assistants and lead systems, shaped around how your team already works.</p>', 'sg-lead sg-reveal' ),
							strategro_b_button( 'See custom builds', '/custom-builds/', 'sg-btn sg-magnetic sg-arrow sg-reveal' ),
						),
						42
					),
					strategro_b_col( array( strategro_b_grid( 2, $service_cards, array( 'css_classes' => 'sg-stagger', 'grid_gaps' => strategro_b_gap( 16 ) ) ) ), 58 ),
				),
				array( 'flex_gap' => strategro_b_gap( 64 ) )
			),
		)
	);

	$insights = strategro_b_section(
		'sg-paper',
		array(
			strategro_b_row(
				array(
					strategro_b_intro( 'Insights', 'Practical thinking on AI automation.' ),
					strategro_b_button( 'All insights', '/insights/', 'sg-btn-ghost sg-magnetic sg-arrow sg-reveal' ),
				),
				array( 'flex_justify_content' => 'space-between', 'flex_align_items' => 'flex-end' )
			),
			strategro_b_widget( 'shortcode', array( 'shortcode' => '[strategro_latest_posts count="3"]', '_css_classes' => 'sg-reveal' ) ),
		),
		array( 'flex_gap' => strategro_b_gap( 40 ) )
	);

	return array(
		$hero,
		$marquee,
		$products,
		$stats_section,
		$case,
		strategro_b_steps_section( $steps, 'Every product goes through the same four stages.' ),
		$built_on,
		$custom,
		$insights,
		strategro_b_cta( 'See what your business could automate first.', 'A 30-minute AI Systems Audit shows where automation would save the most time. No obligation, no jargon.' ),
	);
}

function strategro_b_products_page( $content, $icons ) {
	$sections = array(
		strategro_b_hero(
			'Products',
			'Software for the jobs that <span class="sg-gradient-text">slip through the cracks.</span>',
			'Five products, each built to take one operational job off your team: answering enquiries, running courier operations, publishing content, writing proposals and running an online store.'
		),
	);

	foreach ( $content['products'] as $i => $product ) {
		$visual = strategro_b_col(
			array(
				strategro_b_icon( $icons[ $product['slug'] ] ),
				strategro_b_heading( $product['name'], 'p', 'sg-outline-name' ),
				strategro_b_heading( strategro_b_domain( $product['url'] ), 'span', 'sg-domain' ),
			),
			46,
			array(
				'css_classes'          => 'sg-product sg-orbit sg-tilt sg-reveal',
				'min_height'           => array( 'unit' => 'px', 'size' => 380 ),
				'padding'              => strategro_b_pad( 40 ),
				'flex_justify_content' => 'space-between',
				'html_tag'             => 'a',
				'link'                 => strategro_b_link( $product['url'] ),
			)
		);
		$text = strategro_b_col(
			array(
				strategro_b_heading( sprintf( '%02d · %s', $i + 1, $product['tagline'] ), 'p', 'sg-eyebrow sg-reveal' ),
				strategro_b_heading( $product['name'], 'h2', 'sg-title sg-split' ),
				strategro_b_text( '<p>' . $product['description'] . '</p>', 'sg-lead sg-reveal' ),
				strategro_b_heading( 'Built for: ' . $product['audience'], 'p', 'sg-meta sg-reveal' ),
				strategro_b_button( 'Visit ' . strategro_b_domain( $product['url'] ), $product['url'], 'sg-btn sg-magnetic sg-arrow sg-reveal' ),
			),
			54
		);
		$sections[] = strategro_b_section(
			0 === $i % 2 ? 'sg-dark sg-grid-bg' : 'sg-dark sg-aurora',
			array(
				strategro_b_row(
					0 === $i % 2 ? array( $text, $visual ) : array( $visual, $text ),
					array( 'flex_gap' => strategro_b_gap( 64 ) )
				),
			),
			array( '_element_id' => $product['slug'] )
		);
	}

	$sections[] = strategro_b_cta( 'Not sure which product fits?', 'Tell us what is slowing your team down. We will point you to the right product, or build something new.', 'Talk to us' );
	return $sections;
}

function strategro_b_custom_builds_page( $content, $steps ) {
	$cards = array();
	foreach ( $content['services'] as $service ) {
		$includes = array();
		foreach ( $service['whatItIncludes'] as $item ) {
			$includes[] = $item['title'];
		}
		$cards[] = strategro_b_con(
			array(
				strategro_b_heading( $service['outcome'], 'p', 'sg-eyebrow' ),
				strategro_b_heading( $service['name'], 'h3', 'sg-h3' ),
				strategro_b_text( '<p>' . $service['description'] . '</p>', 'sg-small' ),
				strategro_b_text( '<ul><li>' . implode( '</li><li>', $includes ) . '</li></ul>', 'sg-checks sg-small' ),
			),
			array(
				'_element_id' => $service['slug'],
				'css_classes' => 'sg-card sg-glow sg-tilt',
				'padding'     => strategro_b_pad( 34 ),
				'flex_gap'    => strategro_b_gap( 16 ),
			)
		);
	}

	return array(
		strategro_b_hero(
			'Custom builds',
			'When the system you need doesn’t exist yet, <span class="sg-gradient-text">we build it.</span>',
			'Every Strategro product started this way. We design, build and run AI systems around one specific operational outcome, connected to the tools you already use.'
		),
		strategro_b_section(
			'sg-light',
			array(
				strategro_b_intro( 'What we build', 'Five kinds of system, each solving one bottleneck cleanly.' ),
				strategro_b_grid( 2, $cards, array( 'css_classes' => 'sg-stagger', 'grid_gaps' => strategro_b_gap( 24 ) ) ),
			)
		),
		strategro_b_steps_section( $steps, 'A system built in four deliberate stages.' ),
		strategro_b_cta( 'Not sure which system fits first?', 'An AI Systems Audit identifies your highest-impact automation opportunity in one call.' ),
	);
}

function strategro_b_about_page() {
	$stats = array();
	foreach ( array(
		array( '60–95%', 'Typical reduction in manual workflow time across delivered projects', true ),
		array( '24/7', 'Automated coverage delivered via deployed chatbot and voice infrastructure', false ),
		array( '2024', 'Strategro Ltd founded and registered in London, United Kingdom', false ),
	) as $stat ) {
		$stats[] = strategro_b_con(
			array(
				strategro_b_heading( $stat[0], 'p', 'sg-stat' . ( $stat[2] ? ' sg-counter' : '' ) ),
				strategro_b_text( '<p>' . $stat[1] . '</p>', 'sg-small' ),
			),
			array( 'flex_gap' => strategro_b_gap( 10 ), 'css_classes' => 'sg-divided', 'padding' => strategro_b_pad( 24, 0, 0, 0 ) )
		);
	}

	$principles = array();
	foreach ( array(
		array( 'Practical over hyped', 'We build systems that solve a defined operational problem. If AI isn’t the right tool for something, we say so.' ),
		array( 'Grounded in your operations', 'Every system starts with how your business actually works today, not a generic template applied regardless of context.' ),
		array( 'Built to be maintained', 'Workflows and assistants are documented clearly, so your team understands and can evolve what’s been built.' ),
	) as $principle ) {
		$principles[] = strategro_b_con(
			array(
				strategro_b_heading( $principle[0], 'h3', 'sg-h3' ),
				strategro_b_text( '<p>' . $principle[1] . '</p>', 'sg-small' ),
			),
			array( 'css_classes' => 'sg-card sg-glow', 'padding' => strategro_b_pad( 30 ), 'flex_gap' => strategro_b_gap( 12 ) )
		);
	}

	return array(
		strategro_b_hero(
			'About Strategro',
			'AI products and automation, built around <span class="sg-gradient-text">how businesses actually work.</span>',
			'Strategro Ltd is a UK-registered technology company building AI products and business process automation for SMEs, particularly in logistics, real estate, law and professional services, where response speed and information flow directly affect revenue.',
			array( strategro_b_grid( 3, $stats, array( 'css_classes' => 'sg-stagger', 'grid_gaps' => strategro_b_gap( 32 ), 'margin' => array( 'unit' => 'px', 'top' => '36', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ) ) ) )
		),
		strategro_b_section(
			'sg-light',
			array(
				strategro_b_row(
					array(
						strategro_b_col(
							array(
								strategro_b_heading( 'Leadership', 'p', 'sg-eyebrow sg-reveal' ),
								strategro_b_heading( 'Muhammad Mateen Qazi', 'h2', 'sg-title sg-split' ),
								strategro_b_heading( 'Founder &amp; Director, Strategro Ltd', 'p', 'sg-meta sg-reveal' ),
							),
							40
						),
						strategro_b_col(
							array(
								strategro_b_text( '<p>We founded Strategro to bring practical, production-grade AI automation to SMEs, built on n8n, OpenAI and retrieval-augmented generation rather than off-the-shelf chatbot widgets.</p><p>Before founding Strategro, our team built and deployed multi-client RAG chatbot infrastructure, automated SEO content pipelines, and lead-to-document automation for real estate and logistics businesses. That work now underpins every product we run and every system we build for clients.</p>', 'sg-lead sg-reveal' ),
							),
							60
						),
					),
					array( 'flex_align_items' => 'flex-start', 'flex_gap' => strategro_b_gap( 64 ) )
				),
			)
		),
		strategro_b_section(
			'sg-paper',
			array(
				strategro_b_intro( 'How we think', 'Systems, not software for its own sake.', 'AI is a tool for solving a specific operational problem, not a feature to bolt on. We start with what is actually slowing your business down, and design backwards from there.' ),
				strategro_b_grid( 3, $principles, array( 'css_classes' => 'sg-stagger' ) ),
			)
		),
		strategro_b_section(
			'sg-light',
			array(
				strategro_b_intro( 'Where we work', 'UK and UAE businesses, particularly in time-sensitive sectors.', 'Logistics, real estate, law firms and professional service businesses share a common pattern: response speed and information flow have a direct, measurable effect on revenue. That is where Strategro’s systems have the clearest impact.' ),
			)
		),
		strategro_b_cta( 'Talk to Strategro about your operations.', '' ),
	);
}

function strategro_b_contact_page() {
	$booking = '<div class="sg-embed"><div class="sg-embed__head"><h3>Book a call</h3><p>Pick a slot that works for you.</p></div><iframe src="https://clara.strategro.co.uk/book/strategro-ltd-uk" height="720" title="Book an AI Systems Audit" loading="lazy"></iframe></div>';
	$form    = '<div class="sg-embed"><div class="sg-embed__head"><h3>Prefer to send a message?</h3><p>Tell us a bit about what you’d like to automate and we’ll get back to you.</p></div><iframe src="https://clara.strategro.co.uk/f/strategro-ltd-uk" height="640" title="Contact form" loading="lazy"></iframe></div>';

	return array(
		strategro_b_hero(
			'Contact',
			'Book an <span class="sg-gradient-text">AI Systems Audit.</span>',
			'A 30-minute conversation to find where automation, or one of our products, would save your business the most time. No jargon, no pressure.'
		),
		strategro_b_section(
			'sg-light',
			array(
				strategro_b_con(
					array(
						strategro_b_html( $booking, 'sg-reveal' ),
						strategro_b_html( $form, 'sg-reveal' ),
						strategro_b_text( '<p style="text-align:center">Trouble with the forms above? Email <a href="mailto:mateen@strategro.co.uk">mateen@strategro.co.uk</a> directly.</p>', 'sg-small sg-reveal' ),
					),
					array(
						'width'           => array( 'unit' => 'px', 'size' => 820 ),
						'width_mobile'    => array( 'unit' => '%', 'size' => 100 ),
						'flex_gap'        => strategro_b_gap( 40 ),
						'_flex_align_self' => 'center',
					)
				),
			),
			array( 'flex_align_items' => 'center' )
		),
	);
}

/* ---------------------------------------------------------------------------
 * Installer
 * ------------------------------------------------------------------------ */

/**
 * Creates or updates one page. A page the installer made before is updated
 * in place. Any other page already at that address is kept as a draft
 * (address "<slug>-old", title prefixed "Previous:") so nothing is lost.
 */
function strategro_b_upsert_page( $slug, $title, $elements, $html, $order, &$log ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	$target   = 0;

	if ( $existing ) {
		if ( get_post_meta( $existing->ID, '_strategro_installed', true ) ) {
			$target = $existing->ID;
		} else {
			wp_update_post(
				array(
					'ID'          => $existing->ID,
					'post_name'   => $slug . '-old',
					'post_status' => 'draft',
					'post_title'  => 'Previous: ' . $existing->post_title,
				)
			);
			/* translators: 1: page title, 2: page address */
			$log[] = sprintf( __( 'Kept your existing "%1$s" page as a draft (now at /%2$s-old/).', 'strategro' ), $existing->post_title, $slug );
		}
	}

	$post_id = wp_insert_post(
		array(
			'ID'           => $target,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $html,
			'menu_order'   => $order,
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		throw new RuntimeException( $post_id->get_error_message() );
	}
	update_post_meta( $post_id, '_strategro_installed', 1 );

	if ( null !== $elements ) {
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
		update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );
		update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
		// Saving through Elementor also builds the page's CSS and a plain-text copy for search.
		$document = \Elementor\Plugin::$instance->documents->get( $post_id, false );
		if ( $document ) {
			$document->save( array( 'elements' => $elements ) );
		}
	} else {
		delete_post_meta( $post_id, '_elementor_edit_mode' );
		delete_post_meta( $post_id, '_elementor_data' );
	}

	return $post_id;
}

/** Creates (or rebuilds) one of the installer's own menus and assigns it to a theme slot. */
function strategro_b_menu( $name, $location, $items ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		wp_delete_nav_menu( $menu->term_id );
	}
	$menu_id = wp_create_nav_menu( $name );
	foreach ( $items as $item ) {
		$parent = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $item[0],
				// Relative paths keep links working on any domain.
				'menu-item-url'    => $item[1],
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			)
		);
		foreach ( isset( $item[2] ) ? $item[2] : array() as $child ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $child[0],
					'menu-item-url'       => $child[1],
					'menu-item-status'    => 'publish',
					'menu-item-type'      => 'custom',
					'menu-item-parent-id' => $parent,
				)
			);
		}
	}
	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/** Elementor's global colours and fonts (Elementor > Site Settings). */
function strategro_b_kit_settings() {
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id ) {
		return false;
	}
	$kit_data = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$kit_data = is_array( $kit_data ) ? $kit_data : array();
	$palette  = array(
		'sgink950'   => array( 'Ink 950', '#060F0D' ),
		'sgink900'   => array( 'Ink 900', '#0A1815' ),
		'sgink800'   => array( 'Ink 800', '#0F221D' ),
		'sgink700'   => array( 'Ink 700', '#163229' ),
		'sgink500'   => array( 'Ink 500', '#2A5747' ),
		'sggold300'  => array( 'Gold 300', '#ECD8A3' ),
		'sggold400'  => array( 'Gold 400', '#DDBB6F' ),
		'sggold500'  => array( 'Gold 500', '#C99A44' ),
		'sggold600'  => array( 'Gold 600', '#A97E33' ),
		'sgpaper50'  => array( 'Paper 50', '#FAF8F2' ),
		'sgpaper100' => array( 'Paper 100', '#F3EFE2' ),
		'sgpaper200' => array( 'Paper 200', '#E8E1CD' ),
	);
	$custom_colors = array();
	foreach ( $palette as $id => $color ) {
		$custom_colors[] = array( '_id' => $id, 'title' => $color[0], 'color' => $color[1] );
	}
	$kit_data = array_merge(
		$kit_data,
		array(
			'system_colors'               => array(
				array( '_id' => 'primary', 'title' => 'Primary', 'color' => '#060F0D' ),
				array( '_id' => 'secondary', 'title' => 'Secondary', 'color' => '#163229' ),
				array( '_id' => 'text', 'title' => 'Text', 'color' => '#163229' ),
				array( '_id' => 'accent', 'title' => 'Accent', 'color' => '#C99A44' ),
			),
			'custom_colors'               => $custom_colors,
			'system_typography'           => array(
				array( '_id' => 'primary', 'title' => 'Primary', 'typography_typography' => 'custom', 'typography_font_family' => 'Fraunces', 'typography_font_weight' => '500' ),
				array( '_id' => 'secondary', 'title' => 'Secondary', 'typography_typography' => 'custom', 'typography_font_family' => 'Fraunces', 'typography_font_weight' => '400' ),
				array( '_id' => 'text', 'title' => 'Text', 'typography_typography' => 'custom', 'typography_font_family' => 'Inter', 'typography_font_weight' => '400' ),
				array( '_id' => 'accent', 'title' => 'Accent', 'typography_typography' => 'custom', 'typography_font_family' => 'Inter', 'typography_font_weight' => '600' ),
			),
			'body_typography_typography'  => 'custom',
			'body_typography_font_family' => 'Inter',
			'body_color'                  => '#163229',
			'body_background_background'  => 'classic',
			'body_background_color'       => '#FAF8F2',
			'container_width'             => array( 'unit' => 'px', 'size' => 1280 ),
			'container_padding'           => strategro_b_pad( 0 ),
		)
	);
	foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $tag ) {
		$kit_data[ $tag . '_typography_typography' ]  = 'custom';
		$kit_data[ $tag . '_typography_font_family' ] = 'Fraunces';
		$kit_data[ $tag . '_typography_font_weight' ] = '500';
	}
	update_post_meta( $kit_id, '_elementor_page_settings', $kit_data );
	return true;
}

/**
 * Installs the whole Strategro site. Returns a list of plain-English lines
 * describing what changed. Throws RuntimeException if it cannot run.
 */
function strategro_install_site() {
	if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		throw new RuntimeException( __( 'Elementor is not active. Activate the free Elementor plugin, then run setup again.', 'strategro' ) );
	}

	$content = json_decode( file_get_contents( STRATEGRO_DIR . '/data/content.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$legal   = json_decode( file_get_contents( STRATEGRO_DIR . '/data/legal.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$log     = array();

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 );
	}

	// The pages are built from Flexbox Containers, which must be switched on.
	$experiments = \Elementor\Plugin::$instance->experiments;
	if ( $experiments && ! $experiments->is_feature_active( 'container' ) ) {
		update_option( 'elementor_experiment-container', 'active' );
		$log[] = __( 'Switched on Elementor\'s Flexbox Containers (needed by these pages).', 'strategro' );
	}

	if ( strategro_b_kit_settings() ) {
		$log[] = __( 'Set Elementor\'s global colours and fonts to the Strategro brand.', 'strategro' );
	}

	$steps = strategro_b_steps();
	$icons = strategro_b_icons();

	$home_id     = strategro_b_upsert_page( 'home', 'Home', strategro_b_home( $content, $steps, $icons ), '', 0, $log );
	$products_id = strategro_b_upsert_page( 'products', 'Products', strategro_b_products_page( $content, $icons ), '', 1, $log );
	strategro_b_upsert_page( 'custom-builds', 'Custom Builds', strategro_b_custom_builds_page( $content, $steps ), '', 2, $log );
	strategro_b_upsert_page( 'about', 'About', strategro_b_about_page(), '', 3, $log );
	$insights_id = strategro_b_upsert_page( 'insights', 'Insights', null, '', 4, $log );
	strategro_b_upsert_page( 'contact', 'Contact', strategro_b_contact_page(), '', 5, $log );
	$privacy_id = strategro_b_upsert_page( 'privacy-policy', $legal['privacy-policy']['title'], null, $legal['privacy-policy']['html'], 6, $log );
	$terms_id   = strategro_b_upsert_page( 'terms', $legal['terms']['title'], null, $legal['terms']['html'], 7, $log );
	$log[]      = __( 'Built 8 pages: Home, Products, Custom Builds, About, Insights, Contact, Privacy Policy and Terms.', 'strategro' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', $insights_id );
	update_option( 'wp_page_for_privacy_policy', $privacy_id );
	$log[] = __( 'Home is now the homepage and Insights is the blog page.', 'strategro' );

	// Blog posts: only added if you don't already have a post at that address.
	$added = 0;
	foreach ( $content['posts'] as $post ) {
		if ( get_page_by_path( $post['slug'], OBJECT, 'post' ) ) {
			continue;
		}
		$cat_ids = array();
		foreach ( $post['categories'] as $name ) {
			$term = term_exists( $name, 'category' );
			if ( ! $term ) {
				$term = wp_insert_term( $name, 'category' );
			}
			if ( ! is_wp_error( $term ) ) {
				$cat_ids[] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			}
		}
		$id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_title'    => $post['title'],
				'post_name'     => $post['slug'],
				'post_content'  => $post['content'],
				'post_excerpt'  => $post['excerpt'],
				'post_date'     => get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $post['date'] ) ) ),
				'post_category' => $cat_ids,
			),
			true
		);
		if ( ! is_wp_error( $id ) ) {
			++$added;
		}
	}
	if ( $added ) {
		/* translators: %d: number of posts */
		$log[] = sprintf( _n( 'Added %d blog post.', 'Added %d blog posts.', $added, 'strategro' ), $added );
	}

	// Menus (named "Strategro ..." so they never replace menus you already have).
	$product_links = array();
	foreach ( $content['products'] as $product ) {
		$product_links[] = array( $product['name'], $product['url'] );
	}
	strategro_b_menu(
		'Strategro Header',
		'primary',
		array(
			array( 'Products', '/products/', $product_links ),
			array( 'Custom Builds', '/custom-builds/' ),
			array( 'About', '/about/' ),
			array( 'Insights', '/insights/' ),
			array( 'Contact', '/contact/' ),
		)
	);
	strategro_b_menu( 'Strategro Footer Products', 'footer_products', $product_links );
	strategro_b_menu(
		'Strategro Footer Company',
		'footer_company',
		array(
			array( 'About Strategro', '/about/' ),
			array( 'Custom Builds', '/custom-builds/' ),
			array( 'Insights', '/insights/' ),
			array( 'Contact', '/contact/' ),
		)
	);
	strategro_b_menu(
		'Strategro Footer Legal',
		'footer_legal',
		array(
			array( 'Privacy Policy', '/privacy-policy/' ),
			array( 'Terms of Service', '/terms/' ),
		)
	);
	$log[] = __( 'Created the header and footer menus (Appearance > Menus, all named "Strategro ...").', 'strategro' );

	\Elementor\Plugin::$instance->files_manager->clear_cache();
	flush_rewrite_rules();
	update_option( 'strategro_installed_version', STRATEGRO_VERSION );
	update_option( 'strategro_setup_done', 1 );

	return $log;
}
