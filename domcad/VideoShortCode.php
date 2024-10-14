<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Класс вывода галлерей video
class VideoShortCode {
	static function init(){
		add_shortcode( 'video', array(__CLASS__, 'ps_video_function') );
	}

	static function ps_video_function($atts){
		global $post;
		if($atts["links"]):
			$arrs = explode(',', $atts["links"]);
			if(count($arrs)):
				foreach($arrs as $link):
					$video = new Video($link);
					$videoInfo = $video->videoInfo;
					if($videoInfo['video']):
						$out .= $videoInfo['video'];
					endif;
				endforeach;
			endif;
		endif;
		return $out;
	}
}

VideoShortCode::init();
