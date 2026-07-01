<?php
class DomCad_Slider_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'domcad_slider_home_widget',       // ID виджета (уникальный)
			__('Slider: Home Page', 'domcad'), // Название в админке
			array(
				'description' => 'Вывод слайдера галереи на главной странице.',
				'customize_selective_refresh' => true,
			)
		);
	}

	// Форма настроек виджета в админке
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'      => '',
		) );

		$title      = esc_attr( $instance['title'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Заголовок (необязательно):</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>">
		</p>
		<?php
	}

	// Сохранение настроек виджета
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}

	// Вывод виджета на фронтенде
	public function widget( $args, $instance ) {
		// Проверить на главной ли странице
		$request_uri = $_SERVER['REQUEST_URI'] ?? '';
		$tempParam = ($request_uri === '/');
		if(!$tempParam):
			extract( $args );

			$title       = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$gallery_ids = get_option( 'domcad_slider_home', '' );

			echo $before_widget;

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			// Если ID не заданы — ничего не показываем
			if ( empty( $gallery_ids ) ) {
				echo $after_widget;
				return;
			}

			$ids = array_filter( explode( ',', $gallery_ids ), function( $str ) {
				return !!trim( $str );
			} );

			if ( empty( $ids ) ) {
				echo $after_widget;
				return;
			}

			// Тут вызываем ту же логику вывода слайдера, что у тебя уже есть
			// Можно либо продублировать HTML, либо вынести в отдельный метод и переиспользовать
			echo DomCad_Slider_Settings::get_instance()->renderHTML(false);

			echo $after_widget;
		else:
			echo '';
		endif;
	}
}
