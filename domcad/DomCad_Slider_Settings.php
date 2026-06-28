<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Прямое обращение к файлу запрещено
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
		add_action( 'after_setup_theme', function() {
			add_image_size( 'gallery_image', 1200, 372, true );
		} );
		add_filter( 'image_size_names_choose', array( $this, 'add_image_sizes') );
		// Запуск
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Регистрация вывода в настройки
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
			'general', // группа настроек: страница «Общие»
			'domcad_slider_home', // имя опции
			array(
				'sanitize_callback' => array( $this, 'sanitize_slider_option' ),
				//'type'              => 'string',
			)
		);

		add_settings_field(
			'domcad_slider_home_field', // ID поля
			__( 'Слайдер на главной странице', 'domcad' ), // Заголовок поля
			array( $this, 'render_slider_field' ), // Callback для вывода HTML
			'general', // Страница настроек (options-general.php)
			'default', // Секция (на странице «Общие» это секция 'default')
			array()
		);
	}

	/**
	 * Валидация значения
	 */
	public function sanitize_slider_option( $input ) {
		if ( empty( $input ) ) return '';
		$ids = preg_split( '/\s*,\s*/', trim( $input ), -1, PREG_SPLIT_NO_EMPTY );
		$clean = array();
		foreach ( $ids as $id ) {
			if ( is_numeric( $id ) && (int)$id > 0 ) {
				$clean[] = (int)$id;
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
				$thumb = wp_get_attachment_image_src( $at, 'domcad-size' );
				$images[] = array(
					'id'   => $at,
					'url'  => $thumb ? $thumb[0] : '',
					'alt'  => get_post_meta( $at, '_wp_attachment_image_alt', true ) ?: $at->post_title,
				);
			}
		}
		?>
		<div class="domcad-gallery-field-wrapper">
			<input type="hidden" id="domcad_slider_home" name="domcad_slider_home" value="<?php echo esc_attr( $ids ); ?>">

			<div id="domcad-gallery-order" class="domcad-gallery-preview">
				<?php foreach ( $images as $img ): ?>
					<span class="domcad-img-tag" data-id="<?php echo (int)$img['id']; ?>">
						<?php if ( $img['url'] ): ?>
							<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" title="<?php echo esc_attr( $img['id'] ); ?>">
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

		<style>
			.domcad-gallery-field-wrapper .domcad-img-tag {
				display: inline-block; margin: 4px; border: 1px solid #ccc; padding: 2px; position: relative;
			}
			.domcad-gallery-field-wrapper .domcad-img-tag img { width: 400px; height: 124px; display: block; object-fit: cover; object-position: 50% 50%;}
			.domcad-gallery-field-wrapper .domcad-remove-img {
				position: absolute; top: -8px; right: -8px; background: #d93232; color: #fff;
				width: 16px; height: 16px; line-height: 16px; text-align: center; border-radius: 50%; cursor: pointer;
			}
		</style>
		<script>
			(function($) {
				$(document).ready(function() {
					var frame = null,
						container = $("#domcad-gallery-order")[0];
					var sortable = new Sortable(container, {
						animation: 150,
				        onEnd: function(evt) {
				            var order = [];
				            var items = container.querySelectorAll('.domcad-img-tag');
				            items.forEach(function(el) {
				                var id = parseInt(el.getAttribute('data-id'), 10);
				                if (!isNaN(id)) order.push(id);
				            });
				            $('#domcad_slider_home').val(order.join(','));
				        }
				    });

					$('#domcad-gallery_btn').on('click', function(e) {
						e.preventDefault();
						var selectedIds = $('#domcad_slider_home')
							.val()
							.split(',')
							.filter(w => w.length > 0)
							.map(Number)
						if (!frame) {
							frame = wp.media({
								title: "<?= __('Выберите изображения для галереи', 'domcad'); ?>",
								button: { text: "<?= __('Использовать выбранные', 'domcad'); ?>" },
								multiple: true
							});
							frame.on('open', function() {
								var selection = frame.state().get('selection');
								selectedIds.forEach(function(id) {
									var attachment = wp.media.attachment(id);
									attachment.fetch(); // загружаем данные вложения
									selection.add(attachment ? [attachment] : []);
								});
							});
							frame.on('select', function() {
								var selection = frame.state().get('selection').map(function(a) { return a; });
								var current = $('#domcad_slider_home')
									.val()
									.split(',')
									.filter(w => w.length > 0)
									.map(Number);
								var objs = [];
								current = [];
								// добавляем новые, не дублируя
								selection.forEach(function(obj) {
									if (current.indexOf(obj.id) === -1) {
										current.push(obj.id);
									}
									objs.push(obj);
								});
								$('#domcad_slider_home').val(current.join(','));
								updatePreview(objs);
							});
						}

						frame.open();
					});

					// удаление отдельного изображения
					$(document).on('click', '.domcad-remove-img', function() {
						var $tag = $(this).closest('.domcad-img-tag');
						var id = parseInt($tag.data('id'), 10);
						var current = $('#domcad_slider_home')
							.val()
							.split(',')
							.filter(w => w.length > 0)
							.map(Number);
						current = current.filter( (x) => { x !== id });
						$('#domcad_slider_home').val(current.join(','));
						$tag.remove();
					});

					function updatePreview(objs) {
						console.log(objs);
						// можно сделать AJAX‑запрос к admin-ajax.php для получения HTML превью,
						// либо просто перезагрузить страницу после сохранения.
						//
					}
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Подключение скриптов/стилей (если понадобятся в будущем)
	 */
	public function enqueue_assets( $hook ) {
		// На странице «Общие настройки»
		if ( 'options-general.php' !== $hook ) {
			return;
		}
		
		// Подключение скриптов для работы с медиа
		wp_enqueue_media();
		// Скрипт сортировки
		wp_enqueue_script(
			'domcad-file-sortable',
			plugin_dir_url(__FILE__) . 'js/sortable.js',
			array(),
			filemtime(plugin_dir_path(__FILE__). 'js/sortable.js'),
			array()
		);
		// Свой скрипт для ajax запросов
		wp_enqueue_script(
			'domcad-file-ajax',
			plugin_dir_url(__FILE__) . 'js/file-ajax.js',
			array('jquery'),
			filemtime(plugin_dir_path(__FILE__). 'js/file-ajax.js'),
			array(
				'in_footer' => true,
			)
		);
		// Для ajax запросов
		wp_localize_script( 
			'domcad-file-ajax',
			'domcadFileAjax', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'domcad-files-data-nonce' )
		] );
		
		// wp_enqueue_style( 'domcad-slider-admin', ... );
		// wp_enqueue_script( 'domcad-slider-admin', ... );
		// wp_enqueue_script( 'domcad-slider-admin', array($this, 'enqueue_files_ajax' ) );
	}
}

// Инициализация
DomCad_Slider_Settings::get_instance();
