<?php
// Прямое обращение к файлу запрещено
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DomCad_Slider_Settings {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Регистрация своего размера
		add_action( 'after_setup_theme', array( $this, 'register_custom_image_size' ) );

		add_filter( 'image_size_names_choose', array( $this, 'add_image_sizes' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Регистрация кастомного размера изображения
	 */
	public function register_custom_image_size() {
		add_image_size( 'gallery_image', 1200, 372, true );
	}

	/**
	 * Добавление размера в выпадающий список выбора размера
	 */
	public function add_image_sizes( $sizes ) {
		$sizes['gallery_image'] = __( 'Слайдер', 'domcad' );
		return $sizes;
	}

	/**
	 * Регистрация опции domcad_slider_home
	 */
	public function register_setting() {
		register_setting(
			'general',
			'domcad_slider_home',
			array(
				'sanitize_callback' => array( $this, 'sanitize_slider_option' ),
			)
		);

		add_settings_field(
			'domcad_slider_home_field',
			__( 'Слайдер на главной странице', 'domcad' ),
			array( $this, 'render_slider_field' ),
			'general',
			'default',
			array()
		);
	}

	/**
	 * Валидация значения
	 */
	public function sanitize_slider_option( $input ) {
		if ( empty( $input ) ) {
			return '';
		}

		$ids = preg_split( '/\s*,\s*/', trim( $input ), -1, PREG_SPLIT_NO_EMPTY );
		$clean = array();

		foreach ( $ids as $id ) {
			if ( is_numeric( $id ) && (int) $id > 0 ) {
				$clean[] = (int) $id;
			}
		}

		return implode( ',', $clean );
	}

	/**
	 * Вывод поля на странице настроек
	 */
	public function render_slider_field() {
		$ids      = get_option( 'domcad_slider_home', '' );
		$images   = array();

		if ( ! empty( $ids ) ) {
			$args = explode( ',', $ids );
			foreach ( $args as $at ) {
				$at_id = (int) $at;
				if ( $at_id <= 0 ) {
					continue;
				}

				// Используем правильный размер: 'gallery_image'
				$thumb = wp_get_attachment_image_src( $at_id, 'gallery_image' );

				$post = get_post( $at_id );
				$title = $post && ! empty( $post->post_title ) ? $post->post_title : $at_id;

				$alt = get_post_meta( $at_id, '_wp_attachment_image_alt', true );
				if ( empty( $alt ) ) {
					$alt = $title;
				}

				$images[] = array(
					'id'  => $at_id,
					'url' => $thumb ? $thumb[0] : '',
					'alt' => esc_attr( $alt ),
				);
			}
		}
		?>
		<div class="domcad-gallery-field-wrapper">
			<input type="hidden" id="domcad_slider_home" name="domcad_slider_home" value="<?php echo esc_attr( $ids ); ?>">
			<div id="domcad-gallery-order" class="domcad-gallery-preview">
				<?php foreach ( $images as $img ): ?>
					<span class="domcad-img-tag" data-id="<?php echo (int) $img['id']; ?>">
						<?php if ( ! empty( $img['url'] ) ): ?>
							<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo $img['alt']; ?>" title="<?php echo esc_attr( $img['id'] ); ?>">
						<?php else: ?>
							<?php echo esc_html( $img['id'] ); ?>
						<?php endif; ?>
						<span class="domcad-remove-img">&times;</span>
					</span>
				<?php endforeach; ?>
			</div>
			<p>
				<button type="button" id="domcad-gallery_btn" class="button button-secondary">
					<?php _e( 'Выбрать изображения для слайдера', 'domcad'); ?>
				</button>
			</p>
		</div>
		<?php
	}

	/**
	 * Подключение скриптов/стилей
	 */
	public function enqueue_assets( $hook ) {
		if ( 'options-general.php' !== $hook ) {
			return;
		}

		wp_enqueue_media();

		// Сортировка
		wp_enqueue_script(
			'domcad-file-sortable',
			plugin_dir_url( __FILE__ ) . 'js/sortable.js',
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . 'js/sortable.js' ),
			true
		);

		// Опции админки
		wp_enqueue_script(
			'domcad-options',
			plugin_dir_url( __FILE__ ) . 'js/options.js',
			array( 'jquery' ), // добавь сюда 'wp-media' или 'wp-i18n', если используешь в JS
			filemtime( plugin_dir_path( __FILE__ ) . 'js/options.js' ),
			true
		);

		// AJAX
		wp_enqueue_script(
			'domcad-file-ajax',
			plugin_dir_url( __FILE__ ) . 'js/file-ajax.js',
			array( 'jquery' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'js/file-ajax.js' ),
			true
		);

		wp_localize_script( 
			'domcad-file-ajax',
			'domcadFileAjax', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'domcad-files-data-nonce' ),
			)
		);

		$translation_array = array(
			'title'   => __( 'Выберите изображение для Слайдера', 'domcad' ),
			'insert'  => __( 'Использовать выбранные', 'domcad' ),
			'noFiles' => __( 'Нет выбранных файлов', 'domcad' ),
		);

		wp_localize_script( 'domcad-options', 'domcadTranslations', $translation_array );
	}
}

DomCad_Slider_Settings::get_instance();
?>