<?php
/**
 * Strategro Motion widgets for Elementor. Each one prints the markup the
 * motion engine (assets/js/motion.js) already knows how to animate, so the
 * effects are identical to the ones on the built pages.
 *
 * @package Strategro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

function strategro_widget_classes() {
	return array(
		'Strategro_Headline_Widget',
		'Strategro_Button_Widget',
		'Strategro_Stat_Widget',
		'Strategro_Ticker_Widget',
		'Strategro_Feed_Widget',
		'Strategro_Card_Widget',
		'Strategro_Showcase_Widget',
		'Strategro_Steps_Widget',
	);
}

abstract class Strategro_Widget extends Widget_Base {

	public function get_categories() {
		return array( 'strategro' );
	}

	public function get_keywords() {
		return array( 'strategro', 'motion', 'animation', 'scroll' );
	}

	protected function start_content( $label = '' ) {
		$this->start_controls_section( 'content', array( 'label' => $label ? $label : __( 'Content', 'strategro' ) ) );
	}

	protected function start_style( $label = '' ) {
		$this->start_controls_section(
			'style',
			array(
				'label' => $label ? $label : __( 'Style', 'strategro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
	}

	protected function text( $name, $label, $default = '', $type = Controls_Manager::TEXT, $extra = array() ) {
		$this->add_control(
			$name,
			array_merge(
				array(
					'label'       => $label,
					'type'        => $type,
					'default'     => $default,
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
				),
				$extra
			)
		);
	}

	protected function select( $name, $label, $options, $default, $extra = array() ) {
		$this->add_control(
			$name,
			array_merge(
				array(
					'label'   => $label,
					'type'    => Controls_Manager::SELECT,
					'options' => $options,
					'default' => $default,
				),
				$extra
			)
		);
	}

	protected function switcher( $name, $label, $default = 'yes', $extra = array() ) {
		$this->add_control(
			$name,
			array_merge(
				array(
					'label'   => $label,
					'type'    => Controls_Manager::SWITCHER,
					'default' => $default,
				),
				$extra
			)
		);
	}

	protected function color( $name, $label, $selectors ) {
		$this->add_control(
			$name,
			array(
				'label'     => $label,
				'type'      => Controls_Manager::COLOR,
				'selectors' => $selectors,
			)
		);
	}

	protected function align( $selector, $default = '' ) {
		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'strategro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'strategro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'strategro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'strategro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => $default,
				'selectors' => array( '{{WRAPPER}} ' . $selector => 'text-align: {{VALUE}};' ),
			)
		);
	}

	/** The fields a product/feature card takes, for a widget or a repeater. */
	protected function card_fields( $target ) {
		$target->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'strategro' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-robot',
					'library' => 'fa-solid',
				),
			)
		);
		$fields = array(
			'badge'  => array( __( 'Badge', 'strategro' ), 'Live', Controls_Manager::TEXT ),
			'kicker' => array( __( 'Category', 'strategro' ), 'AI receptionist', Controls_Manager::TEXT ),
			'name'   => array( __( 'Name', 'strategro' ), 'Clara', Controls_Manager::TEXT ),
			'text'   => array( __( 'Description', 'strategro' ), 'Answers enquiries day and night, qualifies leads and books calls straight into your calendar.', Controls_Manager::TEXTAREA ),
			'points' => array( __( 'Highlights (one per line)', 'strategro' ), '', Controls_Manager::TEXTAREA ),
		);
		foreach ( $fields as $name => $field ) {
			$target->add_control(
				$name,
				array(
					'label'       => $field[0],
					'type'        => $field[2],
					'default'     => $field[1],
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
				)
			);
		}
		$target->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'strategro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => 'https://clara.strategro.co.uk' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$target->add_control(
			'link_label',
			array(
				'label'       => __( 'Link text', 'strategro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Leave empty to show the web address.', 'strategro' ),
				'label_block' => true,
			)
		);
	}

	/** Prints one card. $card holds the card_fields() values. */
	protected function render_card( $card, $index, $classes, $key ) {
		$url  = ! empty( $card['link']['url'] ) ? $card['link']['url'] : '';
		$tag  = $url ? 'a' : 'div';
		$this->add_render_attribute( $key, 'class', array_merge( array( 'sg-pcard' ), $classes ) );
		if ( $url ) {
			$this->add_link_attributes( $key, $card['link'] );
		}
		$label = ! empty( $card['link_label'] ) ? $card['link_label'] : preg_replace( '#^https?://(www\.)?#', '', rtrim( $url, '/' ) );
		$points = array_filter( array_map( 'trim', explode( "\n", (string) $card['points'] ) ) );
		?>
		<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( $key ); ?>>
			<span class="sg-pcard__top">
				<?php if ( ! empty( $card['icon']['value'] ) ) : ?>
					<span class="sg-pcard__icon"><?php Icons_Manager::render_icon( $card['icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $index ) : ?>
					<span class="sg-pcard__index" aria-hidden="true"><?php echo esc_html( $index ); ?></span>
				<?php endif; ?>
			</span>
			<?php if ( $card['badge'] ) : ?>
				<span class="sg-pcard__badge"><?php echo esc_html( $card['badge'] ); ?></span>
			<?php endif; ?>
			<?php if ( $card['kicker'] ) : ?>
				<span class="sg-pcard__kicker"><?php echo esc_html( $card['kicker'] ); ?></span>
			<?php endif; ?>
			<span class="sg-pcard__name"><?php echo esc_html( $card['name'] ); ?></span>
			<?php if ( $card['text'] ) : ?>
				<span class="sg-pcard__text"><?php echo esc_html( $card['text'] ); ?></span>
			<?php endif; ?>
			<?php if ( $points ) : ?>
				<span class="sg-pcard__points">
					<?php foreach ( $points as $point ) : ?>
						<span><?php echo esc_html( $point ); ?></span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
			<?php if ( $label ) : ?>
				<span class="sg-pcard__link"><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}

	protected function card_style_controls() {
		$this->select(
			'card_theme',
			__( 'Card colours', 'strategro' ),
			array(
				'dark'  => __( 'Dark', 'strategro' ),
				'light' => __( 'Light', 'strategro' ),
			),
			'dark'
		);
		$this->switcher( 'card_tilt', __( '3D tilt with glare', 'strategro' ) );
		$this->switcher( 'card_glow', __( 'Gold glow on hover', 'strategro' ) );
		$this->switcher( 'card_orbit', __( 'Orbiting light border', 'strategro' ), '' );
		$this->color( 'card_accent', __( 'Accent colour', 'strategro' ), array( '{{WRAPPER}} .sg-pcard' => '--sg-pcard-accent: {{VALUE}};' ) );
	}

	protected function card_classes( $settings ) {
		$classes = array( 'sg-pcard--' . $settings['card_theme'] );
		if ( 'yes' === $settings['card_tilt'] ) {
			$classes[] = 'sg-tilt';
		}
		if ( 'yes' === $settings['card_glow'] ) {
			$classes[] = 'sg-glow';
		}
		if ( 'yes' === $settings['card_orbit'] ) {
			$classes[] = 'sg-orbit';
		}
		return $classes;
	}
}

/* ---------------------------------------------------------------------------
 * Split Headline
 * ------------------------------------------------------------------------ */
class Strategro_Headline_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-headline';
	}

	public function get_title() {
		return __( 'Split Headline', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-animated-headline';
	}

	protected function register_controls() {
		$this->start_content();
		$this->text( 'eyebrow', __( 'Label above', 'strategro' ), 'Signal to System' );
		$this->text( 'before', __( 'Headline', 'strategro' ), 'Turn operational drag into', Controls_Manager::TEXTAREA, array( 'rows' => 2 ) );
		$this->text( 'highlight', __( 'Shimmering gold words', 'strategro' ), 'intelligent systems.' );
		$this->text( 'after', __( 'Words after the gold', 'strategro' ), '' );
		$this->select(
			'tag',
			__( 'HTML tag', 'strategro' ),
			array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'p'  => 'p',
			),
			'h2'
		);
		$this->select(
			'size',
			__( 'Size', 'strategro' ),
			array(
				'display' => __( 'Hero', 'strategro' ),
				'title'   => __( 'Section', 'strategro' ),
				'h3'      => __( 'Card', 'strategro' ),
			),
			'title'
		);
		$this->select(
			'motion',
			__( 'Entrance', 'strategro' ),
			array(
				'split'  => __( 'Words slide up one by one', 'strategro' ),
				'reveal' => __( 'Fade up from blur', 'strategro' ),
				'none'   => __( 'None', 'strategro' ),
			),
			'split'
		);
		$this->align( '.sg-headline' );
		$this->end_controls_section();

		$this->start_style();
		$this->color( 'title_color', __( 'Headline colour', 'strategro' ), array( '{{WRAPPER}} .sg-headline__title' => 'color: {{VALUE}};' ) );
		$this->color( 'eyebrow_color', __( 'Label colour', 'strategro' ), array( '{{WRAPPER}} .sg-headline__eyebrow' => 'color: {{VALUE}};' ) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_type',
				'label'    => __( 'Headline font', 'strategro' ),
				'selector' => '{{WRAPPER}} .sg-headline__title',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$tag     = in_array( $s['tag'], array( 'h1', 'h2', 'h3', 'h4', 'p' ), true ) ? $s['tag'] : 'h2';
		$classes = 'elementor-heading-title sg-headline__title sg-headline--' . sanitize_html_class( $s['size'] );
		if ( 'none' !== $s['motion'] ) {
			$classes .= ' sg-' . sanitize_html_class( $s['motion'] );
		}
		?>
		<div class="sg-headline">
			<?php if ( $s['eyebrow'] ) : ?>
				<p class="sg-headline__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
			<?php endif; ?>
			<<?php echo esc_html( $tag ); ?> class="<?php echo esc_attr( $classes ); ?>">
				<?php echo esc_html( $s['before'] ); ?>
				<?php if ( $s['highlight'] ) : ?>
					<span class="sg-gradient-text"><?php echo esc_html( $s['highlight'] ); ?></span>
				<?php endif; ?>
				<?php echo esc_html( $s['after'] ); ?>
			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}
}

/* ---------------------------------------------------------------------------
 * Glow Button
 * ------------------------------------------------------------------------ */
class Strategro_Button_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-glow-button';
	}

	public function get_title() {
		return __( 'Glow Button', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	protected function register_controls() {
		$this->start_content();
		$this->text( 'label', __( 'Text', 'strategro' ), 'Book an AI Systems Audit' );
		$this->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'strategro' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '/contact/' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->select(
			'look',
			__( 'Style', 'strategro' ),
			array(
				'gold'  => __( 'Gold', 'strategro' ),
				'ghost' => __( 'Outline', 'strategro' ),
				'dark'  => __( 'Dark', 'strategro' ),
			),
			'gold'
		);
		$this->select(
			'size',
			__( 'Size', 'strategro' ),
			array(
				'sm' => __( 'Small', 'strategro' ),
				'md' => __( 'Medium', 'strategro' ),
				'lg' => __( 'Large', 'strategro' ),
			),
			'md'
		);
		$this->switcher( 'arrow', __( 'Arrow', 'strategro' ) );
		$this->switcher( 'magnetic', __( 'Drifts towards the cursor', 'strategro' ) );
		$this->align( '.sg-button-wrap' );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$classes = array( 'sg-button', 'sg-button--' . $s['look'], 'sg-button--' . $s['size'] );
		if ( 'yes' === $s['arrow'] ) {
			$classes[] = 'sg-arrow';
		}
		if ( 'yes' === $s['magnetic'] ) {
			$classes[] = 'sg-magnetic';
		}
		$this->add_render_attribute( 'button', 'class', $classes );
		if ( ! empty( $s['link']['url'] ) ) {
			$this->add_link_attributes( 'button', $s['link'] );
		}
		?>
		<div class="sg-button-wrap">
			<a <?php $this->print_render_attribute_string( 'button' ); ?>><span><?php echo esc_html( $s['label'] ); ?></span></a>
		</div>
		<?php
	}
}

/* ---------------------------------------------------------------------------
 * Stat Counter
 * ------------------------------------------------------------------------ */
class Strategro_Stat_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-stat-counter';
	}

	public function get_title() {
		return __( 'Stat Counter', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	protected function register_controls() {
		$this->start_content();
		$this->text( 'prefix', __( 'Before the number', 'strategro' ), '' );
		$this->text( 'number', __( 'Number', 'strategro' ), '24' );
		$this->text( 'suffix', __( 'After the number', 'strategro' ), '/7' );
		$this->text( 'label', __( 'Label', 'strategro' ), 'Enquiries answered' );
		$this->text( 'text', __( 'Description', 'strategro' ), '', Controls_Manager::TEXTAREA, array( 'rows' => 3 ) );
		$this->switcher( 'count', __( 'Count up from zero', 'strategro' ) );
		$this->align( '.sg-statw' );
		$this->end_controls_section();

		$this->start_style();
		$this->color( 'label_color', __( 'Label colour', 'strategro' ), array( '{{WRAPPER}} .sg-statw__label' => 'color: {{VALUE}};' ) );
		$this->color( 'text_color', __( 'Description colour', 'strategro' ), array( '{{WRAPPER}} .sg-statw__text' => 'color: {{VALUE}};' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="sg-statw">
			<p class="sg-statw__value">
				<?php echo esc_html( $s['prefix'] ); ?><span class="<?php echo 'yes' === $s['count'] ? 'sg-counter' : ''; ?>"><?php echo esc_html( $s['number'] ); ?></span><?php echo esc_html( $s['suffix'] ); ?>
			</p>
			<?php if ( $s['label'] ) : ?>
				<p class="sg-statw__label"><?php echo esc_html( $s['label'] ); ?></p>
			<?php endif; ?>
			<?php if ( $s['text'] ) : ?>
				<p class="sg-statw__text"><?php echo esc_html( $s['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}

/* ---------------------------------------------------------------------------
 * Ticker (endless sideways strip of words or logos)
 * ------------------------------------------------------------------------ */
class Strategro_Ticker_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-ticker';
	}

	public function get_title() {
		return __( 'Motion Ticker', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	protected function register_controls() {
		$this->start_content( __( 'Items', 'strategro' ) );
		$repeater = new Repeater();
		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'strategro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Clara',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'image',
			array(
				'label'       => __( 'Logo (optional, replaces the text)', 'strategro' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
			)
		);
		$items = array();
		foreach ( array( 'Clara', 'Lodway', 'Strategro SEO', 'Strategro Proposals', 'ShopOps' ) as $name ) {
			$items[] = array( 'text' => $name );
		}
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $items,
				'title_field' => '{{{ text }}}',
			)
		);
		$this->end_controls_section();

		$this->start_style( __( 'Motion and style', 'strategro' ) );
		$this->select(
			'look',
			__( 'Look', 'strategro' ),
			array(
				'gradient' => __( 'Large gradient words', 'strategro' ),
				'outline'  => __( 'Large outlined words', 'strategro' ),
				'pill'     => __( 'Pills', 'strategro' ),
			),
			'gradient'
		);
		$this->text( 'separator', __( 'Separator', 'strategro' ), '✦', Controls_Manager::TEXT, array( 'label_block' => false ) );
		$this->add_control(
			'speed',
			array(
				'label'     => __( 'Seconds per loop', 'strategro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 8,
						'max' => 120,
					),
				),
				'default'   => array( 'size' => 40 ),
				'selectors' => array( '{{WRAPPER}} .sg-ticker' => '--sg-ticker-speed: {{SIZE}}s;' ),
			)
		);
		$this->select(
			'direction',
			__( 'Direction', 'strategro' ),
			array(
				'left'  => __( 'Right to left', 'strategro' ),
				'right' => __( 'Left to right', 'strategro' ),
			),
			'left'
		);
		$this->add_control(
			'repeat',
			array(
				'label'       => __( 'Repeat the list', 'strategro' ),
				'description' => __( 'Raise this if a short list leaves a gap on wide screens.', 'strategro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 6,
				'default'     => 2,
			)
		);
		$this->switcher( 'pause', __( 'Pause on hover', 'strategro' ) );
		$this->switcher( 'fade', __( 'Fade the edges', 'strategro' ) );
		$this->color( 'text_color', __( 'Text colour', 'strategro' ), array( '{{WRAPPER}} .sg-ticker' => '--sg-ticker-color: {{VALUE}};' ) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_type',
				'selector' => '{{WRAPPER}} .sg-ticker__item',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['items'] ) ) {
			return;
		}
		$classes = array( 'sg-ticker', 'sg-ticker--' . $s['look'], 'sg-ticker--' . $s['direction'] );
		if ( 'yes' === $s['pause'] ) {
			$classes[] = 'sg-ticker--pause';
		}
		if ( 'yes' === $s['fade'] ) {
			$classes[] = 'sg-ticker--fade';
		}
		$repeat = max( 1, min( 6, (int) $s['repeat'] ) );
		ob_start();
		for ( $r = 0; $r < $repeat; $r++ ) {
			foreach ( $s['items'] as $item ) {
				echo '<span class="sg-ticker__item">';
				if ( ! empty( $item['image']['url'] ) ) {
					printf( '<img src="%s" alt="%s" loading="lazy">', esc_url( $item['image']['url'] ), esc_attr( $item['text'] ) );
				} else {
					echo esc_html( $item['text'] );
				}
				echo '</span>';
				if ( '' !== $s['separator'] ) {
					echo '<span class="sg-ticker__sep" aria-hidden="true">' . esc_html( $s['separator'] ) . '</span>';
				}
			}
		}
		$group = ob_get_clean();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="sg-ticker__track">
				<div class="sg-ticker__group"><?php echo $group; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></div>
				<div class="sg-ticker__group" aria-hidden="true"><?php echo $group; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			</div>
		</div>
		<?php
	}
}

/* ---------------------------------------------------------------------------
 * Activity Feed
 * ------------------------------------------------------------------------ */
class Strategro_Feed_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-activity-feed';
	}

	public function get_title() {
		return __( 'Live Activity Feed', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	protected function register_controls() {
		$this->start_content();
		$this->text( 'title', __( 'Title', 'strategro' ), 'System activity' );
		$this->text( 'tag', __( 'Tag', 'strategro' ), 'Example' );
		$this->add_control(
			'visible',
			array(
				'label'       => __( 'Rows shown at once', 'strategro' ),
				'description' => __( 'Extra rows slide in one at a time.', 'strategro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 8,
				'default'     => 4,
			)
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'badge',
			array(
				'label'   => __( 'Badge', 'strategro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'CL',
			)
		);
		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Event', 'strategro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Clara booked a call',
				'label_block' => true,
			)
		);
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ badge }}} · {{{ text }}}',
				'default'     => array(
					array(
						'badge' => 'CL',
						'text'  => 'Clara answered an after-hours enquiry and booked a call',
					),
					array(
						'badge' => 'LW',
						'text'  => 'Lodway dispatched a same-day job to the nearest driver',
					),
					array(
						'badge' => 'SEO',
						'text'  => 'Strategro SEO queued a new article for review',
					),
					array(
						'badge' => 'PR',
						'text'  => 'Strategro Proposals drafted a proposal from a new lead',
					),
					array(
						'badge' => 'SO',
						'text'  => 'ShopOps flagged a listing draft ready to approve',
					),
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="sg-feed" data-visible="<?php echo esc_attr( max( 1, (int) $s['visible'] ) ); ?>">
			<div class="sg-feed__head">
				<span class="sg-feed__dot"></span><?php echo esc_html( $s['title'] ); ?>
				<?php if ( $s['tag'] ) : ?>
					<span class="sg-feed__tag"><?php echo esc_html( $s['tag'] ); ?></span>
				<?php endif; ?>
			</div>
			<ul class="sg-feed__list">
				<?php foreach ( (array) $s['items'] as $item ) : ?>
					<li><b><?php echo esc_html( $item['badge'] ); ?></b><span><?php echo esc_html( $item['text'] ); ?></span><time><?php esc_html_e( 'now', 'strategro' ); ?></time></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

/* ---------------------------------------------------------------------------
 * Product Card
 * ------------------------------------------------------------------------ */
class Strategro_Card_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-product-card';
	}

	public function get_title() {
		return __( 'Motion Card', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	protected function register_controls() {
		$this->start_content();
		$this->card_fields( $this );
		$this->text( 'index', __( 'Big number', 'strategro' ), '01', Controls_Manager::TEXT, array( 'label_block' => false ) );
		$this->end_controls_section();

		$this->start_style();
		$this->card_style_controls();
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$this->render_card( $s, $s['index'], $this->card_classes( $s ), 'card' );
	}
}

/* ---------------------------------------------------------------------------
 * Horizontal Showcase (pins and scrolls sideways on desktop)
 * ------------------------------------------------------------------------ */
class Strategro_Showcase_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-hscroll-showcase';
	}

	public function get_title() {
		return __( 'Sideways Scroll Showcase', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	protected function register_controls() {
		$this->start_content( __( 'Cards', 'strategro' ) );
		$repeater = new Repeater();
		$this->card_fields( $repeater );
		$defaults = array(
			array( 'Clara', 'AI receptionist', 'robot', 'https://clara.strategro.co.uk', 'Answers enquiries day and night, qualifies leads and books calls.' ),
			array( 'Lodway', 'Courier operations', 'shipping-fast', 'https://lodway.com', 'Dispatch, live tracking, proof of delivery and invoicing for courier firms.' ),
			array( 'Strategro SEO', 'Search content', 'chart-line', 'https://seo.strategro.co.uk', 'Plans, writes and publishes search content that brings in customers.' ),
			array( 'Strategro Proposals', 'Sales documents', 'file-signature', 'https://proposal.strategro.co.uk', 'Turns a new lead into a polished proposal in minutes.' ),
			array( 'ShopOps', 'Online store', 'shopping-bag', 'https://shopops.strategro.co.uk', 'Finds products, writes listings and keeps an online store running.' ),
		);
		$items = array();
		foreach ( $defaults as $card ) {
			$items[] = array(
				'name'   => $card[0],
				'kicker' => $card[1],
				'icon'   => array(
					'value'   => 'fas fa-' . $card[2],
					'library' => 'fa-solid',
				),
				'link'   => array( 'url' => $card[3] ),
				'text'   => $card[4],
				'badge'  => 'Live',
			);
		}
		$this->add_control(
			'cards',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $items,
				'title_field' => '{{{ name }}}',
			)
		);
		$this->switcher( 'numbers', __( 'Number the cards', 'strategro' ) );
		$this->switcher(
			'pin',
			__( 'Pin and scroll sideways', 'strategro' ),
			'yes',
			array( 'description' => __( 'On desktop the cards hold in place and slide sideways as visitors scroll down. Phones get a swipeable row.', 'strategro' ) )
		);
		$this->end_controls_section();

		$this->start_style();
		$this->add_responsive_control(
			'card_width',
			array(
				'label'     => __( 'Card width', 'strategro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 240,
						'max' => 640,
					),
				),
				'default'   => array(
					'size' => 400,
					'unit' => 'px',
				),
				'selectors' => array( '{{WRAPPER}} .sg-hsw' => '--sg-hsw-card: {{SIZE}}px;' ),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'     => __( 'Space between cards', 'strategro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'   => array(
					'size' => 24,
					'unit' => 'px',
				),
				'selectors' => array( '{{WRAPPER}} .sg-hsw' => '--sg-hsw-gap: {{SIZE}}px;' ),
			)
		);
		$this->card_style_controls();
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$classes = $this->card_classes( $s );
		?>
		<div class="sg-hsw<?php echo 'yes' === $s['pin'] ? ' sg-hscroll' : ''; ?>">
			<div class="sg-hsw__track sg-hscroll-track">
				<?php
				foreach ( (array) $s['cards'] as $i => $card ) {
					$this->render_card( $card, 'yes' === $s['numbers'] ? sprintf( '%02d', $i + 1 ) : '', $classes, 'card-' . $i );
				}
				?>
			</div>
		</div>
		<?php
	}
}

/* ---------------------------------------------------------------------------
 * Scroll Steps
 * ------------------------------------------------------------------------ */
class Strategro_Steps_Widget extends Strategro_Widget {

	public function get_name() {
		return 'sg-scroll-steps';
	}

	public function get_title() {
		return __( 'Scroll Steps', 'strategro' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	protected function register_controls() {
		$this->start_content( __( 'Steps', 'strategro' ) );
		$repeater = new Repeater();
		$repeater->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'strategro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Discover',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'strategro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => 'We map how work actually moves today.',
			)
		);
		$steps = array();
		foreach ( strategro_default_steps() as $step ) {
			$steps[] = array(
				'title' => $step[0],
				'text'  => $step[1],
			);
		}
		$this->add_control(
			'steps',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $steps,
				'title_field' => '{{{ title }}}',
			)
		);
		$this->switcher( 'line', __( 'Gold line draws across', 'strategro' ) );
		$this->end_controls_section();

		$this->start_style();
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'strategro' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'selectors'      => array( '{{WRAPPER}} .sg-stepsw__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
			)
		);
		$this->select(
			'look',
			__( 'Cards', 'strategro' ),
			array(
				'glass' => __( 'Glass (for dark sections)', 'strategro' ),
				'card'  => __( 'White (for light sections)', 'strategro' ),
			),
			'glass'
		);
		$this->switcher( 'glow', __( 'Gold glow on hover', 'strategro' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$step = 'sg-stepsw__step sg-step ' . ( 'card' === $s['look'] ? 'sg-stepsw__step--card' : 'sg-glass' ) . ( 'yes' === $s['glow'] ? ' sg-glow' : '' );
		?>
		<div class="sg-stepsw sg-steps">
			<?php if ( 'yes' === $s['line'] ) : ?>
				<div class="sg-line"></div>
			<?php endif; ?>
			<div class="sg-stepsw__grid">
				<?php foreach ( (array) $s['steps'] as $i => $item ) : ?>
					<div class="<?php echo esc_attr( $step ); ?>">
						<span class="sg-stepsw__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<h3 class="sg-stepsw__title"><?php echo esc_html( $item['title'] ); ?></h3>
						<p class="sg-stepsw__text"><?php echo esc_html( $item['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
