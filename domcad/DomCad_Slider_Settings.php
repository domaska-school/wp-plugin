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
		// Регистрация настроек
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		// Загрузка стилей и скриптов
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		// Регистрация страницы в Внешнем виде
		add_action( 'admin_menu', array( $this, 'setup_theme_admin_menus' ) );
		add_filter( 'image_resize_dimensions', array( $this, 'image_resize_dimensions' ), 10, 6 );
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
		$sizes['gallery_image'] = __( 'Slider', 'domcad' );
		return $sizes;
	}

	/**
	 * 
	 */
	public function image_resize_dimensions($output, $orig_w, $orig_h, $dest_w, $dest_h, $crop) {
		return null;
	}

	/**
	 * Регистрация страницы в меню «Внешний вид»
	 */
	public function setup_theme_admin_menus() {
		add_submenu_page(
			// уникальный идентификатор для меню верхнего уровня, к которому будет добавлено подменю.
			'themes.php',
			// Тайтл страницы
			__( "Slider on the main page", "domcad" ),
			// заголовок, отображаемый в меню (зачастую короткая версия $page_title)
			__( "Slider", "domcad" ),
			// минимальные права доступа, требуемые пользователю, чтобы получить доступ к данному меню.
			'manage_options',
			// уникальный идентификатор для создаваемого меню.
			'domcad_gallery_elements',
			// название функции, которая вызывается для обработки (и представления) данной страницы меню.
			array( $this, 'theme_front_page_settings' ),
			// Позиция меню
			7
		);
	}

	/**
	 * Ресайз изображения
	 */
	public function resize_image( $path = "" ) {
		if($path) {
			$image = wp_get_image_editor( $path );
			$size = $image->get_size();
			$width = $size["width"];
			$height = $size["height"];
			$path_info = pathinfo($path);
			$filename = $path_info["filename"] . "-1200x372" . "." . $path_info["extension"];
			$tmp = $path_info["dirname"] . "/" . $filename;
			$url = site_url(str_replace(array($_SERVER['DOCUMENT_ROOT']), '', $tmp));
			if($width == 1200 && $height == 372) {
				$url = site_url(str_replace(array($_SERVER['DOCUMENT_ROOT']), '', $path));
				return array(
					$url,
					1200,
					372,
					true
				);
			} else {
				//file_put_contents(__DIR__ . "/test.txt", print_r($url, true) . "\n\n", FILE_APPEND);
				if(!is_file($tmp)) {
					$result = $image->resize(1200, 372, array(
						"x_crop_position" => "center",
						"y_crop_position" => "center"
					));
					$image->save($tmp);
					return array(
						$url,
						1200,
						372,
						true
					);
				} else {
					return array(
						$url,
						1200,
						372,
						true
					);
				}
			}
		} else {
			wp_admin_notice(
				// Сообщение Переменная \$path обязательна
				__("The \$path variable is required", "domcad"),
				array(
					// Тип сообщения
					"type" => "error",
					// Можно ли закрыть уведомление
					"dismissible" => true,
					// id контейнера
					"id" => "",
					// Массив CSS классов для html контейнера заметки. Данные классы будут добавлены к классам из списка
					"additional_classes" => array(
						"notice",
						"notice-{type}",
						"notice-alt",
						"is-dismissible"
					),
					// Оборачивать ли в параграф
					"paragraph_wrap" => true,
				)
			);
		}
	}

	/**
	 * Отображение отдельной страницы настроек
	 */
	public function theme_front_page_settings() {
		$this->enqueue_assets( 'options-general.php' );
		// проверяем, что пользователь может обновлять настройки
		if (!current_user_can('manage_options')) {
			wp_die( __( "You do not have sufficient permissions to access this page." , "domcad" ) );
		}
		if(isset($_POST['update_domcad_gallery'])) {
			$val = intval(esc_attr($_POST['update_domcad_gallery']));
			if($val) {
				// Сохранение
				$ids = isset($_POST['domcad_slider_home']) ? esc_attr($_POST['domcad_slider_home']) : '';
				$speed = intval(isset($_POST['domcad_slider_autoplay_speed'])  ? esc_attr($_POST['domcad_slider_autoplay_speed']) : '5');
				$speed = $speed > 0 ? $speed : 5;
				update_option("domcad_slider_home", $ids);
				update_option("domcad_slider_autoplay_speed", $speed);
				wp_admin_notice(
					// Сообщение
					__("The settings have been saved successfully!", "domcad"),
					array(
						// Тип сообщения
						"type" => "success",
						// Можно ли закрыть уведомление
						"dismissible" => true,
						// id контейнера
						"id" => "",
						// Массив CSS классов для html контейнера заметки. Данные классы будут добавлены к классам из списка
						"additional_classes" => array(
							"notice",
							"notice-{type}",
							"notice-alt",
							"is-dismissible"
						),
						// Оборачивать ли в параграф
						"paragraph_wrap" => true,
					)
				);
			}
		}
		?>
		<h1><?= __("Slider settings on the main page", "domcad" );?></h1>
		<form method="POST" action="">
			<input type="hidden" name="update_domcad_gallery" value="1" />
		<?php
		$this->render_slider_field(true);
		$this->render_slider_autoplay_speed_field(true);
		?>
			<p>
				<input type="submit" name="" value="<?= __("Save settings", "domcad");?>" class="button-primary"/>
			</p>
			<p><?= __('Instructions on how to use', 'domcad');?>: <a href="https://github.com/domaska-school/wp-plugin#плагин-для-сайта" target="_blank">https://github.com/domaska-school/wp-plugin</a></p>
		</form>
		<?php
	}

	/**
	 * Регистрация опции
	 * domcad_slider_home - изображения слайдера
	 * 
	 * Регистрация опции
	 * domcad_slider_autoplay_speed - время между слайдерами
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
			__( 'Slider on the main page', 'domcad' ),
			array( $this, 'render_slider_field' ),
			'general',
			'default',
			array()
		);

		register_setting(
			'general',
			'domcad_slider_autoplay_speed',
			array(
				'sanitize_callback' => array( $this, 'sanitize_slider_option_autoplay_speed' ),
			)
		);

		add_settings_field(
			'domcad_slider_autoplay_speed_field',
			__( 'Time between slider animations', 'domcad' ),
			array( $this, 'render_slider_autoplay_speed_field' ),
			'general',
			'default',
			array()
		);
	}

	/**
	 * Валидация значения domcad_slider_home
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
	 * Валидация значения domcad_slider_autoplay_speed
	 */
	public function sanitize_slider_option_autoplay_speed( $input ) {
		if ( empty( $input ) ) {
			return '';
		}
		$value = intval( trim( $input ), 10 );
		if(!$value){
			$value = 1;
		}
		return $value;
	}

	/**
	 * Вывод поля domcad_slider_home
	 */
	public function render_slider_field($type) {
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
				//$thumb = wp_get_attachment_image_src( $at_id, 'gallery_image' );
				
				if (has_image_size('gallery_image')) {
					$thumb = image_get_intermediate_size( $at_id, 'gallery_image' );
					if(!$thumb) {
						/**
						 * Вот здесь нужно сделать принудительный ресайз
						 */
						$path = wp_get_original_image_path($at_id);
						$thumb = $this->resize_image($path);
					} else {
						$thumb[0] = $thumb['url'];
					}
				} else {
					/**
					 * Вот здесь нужно сделать принудительный ресайз
					 */
					$thumb = $this->resize_image($path);
				}

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
		if($type) {
		?>
		<h3><?= __( "Select Images", "domcad" ); ?></h3>
		<?php
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
				<button type="button" id="domcad-gallery_btn" class="button button-primary">
					<?= __( 'Select images for the Slider', 'domcad'); ?>
				</button>
			</p>
			<?php
			if(!$type) {
			?>
			<p><?= __('Instructions on how to use', 'domcad');?>: <a href="https://github.com/domaska-school/wp-plugin#плагин-для-сайта" target="_blank">https://github.com/domaska-school/wp-plugin</a></p>
			<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Вывод поля domcad_slider_autoplay_speed
	 */
	public function render_slider_autoplay_speed_field ($type) {
		$value = get_option( 'domcad_slider_autoplay_speed', '1' );
		$value = $this->sanitize_slider_option_autoplay_speed($value);
		if($type) {
		?>
		<h3><?= __( "Time between slider animations", "domcad" );?></h3>
		<?php
		}
		?>
		<div class="domcad-gallery-field-autoplay-speed">
			<input type="number" id="domcad_slider_autoplay_speed" name="domcad_slider_autoplay_speed" value="<?php echo esc_attr( $value ); ?>" min="1" max="100" step="1"> <?= __('Seconds', 'domcad');?>
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
			plugin_dir_url( __FILE__ ) . 'js/sortable.min.js',
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . 'js/sortable.min.js' ),
			true
		);

		// Опции админки
		wp_enqueue_script(
			'domcad-options',
			plugin_dir_url( __FILE__ ) . 'js/options.min.js',
			array( 'jquery' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'js/options.min.js' ),
			true
		);

		// AJAX
		wp_enqueue_script(
			'domcad-file-ajax',
			plugin_dir_url( __FILE__ ) . 'js/file-ajax.min.js',
			array( 'jquery' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'js/file-ajax.min.js' ),
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
			'title'   => __( 'Select images for the Slider', 'domcad' ),
			'insert'  => __( 'Use the selected ones', 'domcad' ),
			'noFiles' => __( 'There are no selected files', 'domcad' ),
		);

		wp_localize_script( 'domcad-options', 'domcadTranslations', $translation_array );
	}
}

DomCad_Slider_Settings::get_instance();
?>