!(function($){
	RegExp.escape = function(string) {
		return string.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')
	};
	$("body").append('<div class="fancybox-app-viewer"></div>');
	/**
	 * Дефолтные настройки Fancybox
	 * Добавляем Русский язык
	**/
	$.fancybox.defaults.i18n.ru = {
		CLOSE: "Закрыть",
		NEXT: "Следующий",
		PREV: "Предыдущий",
		ERROR: "Запрошенный контент не может быть загружен.<br/>Повторите попытку позже.",
		PLAY_START: "Начать слайдшоу",
		PLAY_STOP: "Остановить слайдшоу",
		FULL_SCREEN: "Полный экран",
		THUMBS: "Миниатюры",
		DOWNLOAD: "Скачать",
		SHARE: "Поделиться",
		ZOOM: "Увеличить"
	};
	$.fancybox.defaults.transitionEffect = "circular";
	$.fancybox.defaults.transitionDuration = 500;
	$.fancybox.defaults.lang = "ru";
	$.fancybox.defaults.parentEl = ".fancybox-app-viewer";
	$.fancybox.defaults.beforeShow = function (instance, current) {
		//console.log(instance, current);
		//$(current.)
	}
	/**
	 * Клик на ссылках документов
	 * pdf, xlsx
	 */
	$(document).on('click', "a[href$='.pdf'], a[href$='.xlsx']", e => {
		let target = e.target.nodeName == "A" ? $(e.target) : $(e.target).closest('a'),
			base = window.location.origin + '/',
			reg = new RegExp("^" + base),
			href, arr, ext, url;
		/**
		 * Если существует
		 */
		if (target[0]) {
			href = target[0].href;
			arr = href.split('.');
			ext = arr.at(-1).toLowerCase();
			/**
			 * Если проходит регулярку
			 */
			if(reg.test(href)){
				url = window.location.origin + `/viewer/${ext}_viewer/?file=` + encodeURI(href);
				/**
				 * Открываем fancybox
				 */
				e.preventDefault();
				$.fancybox.open({
					src: url,
					toolbar: true,
					smallBtn: false,
					buttons: [
						"close"
					],
					opts : {
						afterShow : function( instance, current ) {
							$(".fancybox-content").css({
								height: '100% !important',
								overflow: 'hidden'
							}).addClass(`${ext}_viewer`);
						},
						afterLoad : function( instance, current ) {
							$(".fancybox-content").css({
								height: '100% !important',
								overflow: 'hidden'
							}).addClass(`${ext}_viewer`);
						},
						afterClose: function() {
							Cookies.remove('pdfjs.history', { path: '' });
							window.localStorage.removeItem('pdfjs.history');
						}
					}
				});
				return !1;
			}else if(ext=='pdf'){
				/**
				 * 
				 * 
				 * 
				 * 
				 * 
				**/
			}
		}
	})
	/**
	 * Клик на ссылке изображения в контенте
	 */
	.on('click', ".entry-content a[href$='.jpg'], .entry-content a[href$='.jpeg'], .entry-content a[href$='.png'], .entry-content a[href$='.gif'], .entry-content a[href$='.webp']", e => {
		let target = e.target.nodeName == "A" ? $(e.target) : $(e.target).closest('a');
		// Если существует
		// и нет атрибута data-fancybox
		if (target[0] && (typeof target.data("fancybox") !== "string")) {
			let base = window.location.origin,
				reg = new RegExp("^" + base),
				href = target[0].href;
			// Если проходит регулярку
			if(reg.test(href)){
				// Открываем fancybox
				e.preventDefault();
				$.fancybox.open({
					src: href
				});
				return !1;
			}
		}
	});
	/*
	$("#content article").each((index, element, array) => {
		let id = $(element).attr("id");
		$(".wp-block-image img", element).each((i, e, a) => {
			let lnk = $(e).closest("a");
			if(lnk.length){
				lnk.each((li, le, la) => {
					le.setAttribute("data-fancybox", id);
				});
			}else{
				lnk = $("<a></a>");
				lnk.attr({
					"href": $(e).attr("data-src"),
					"data-fancybox": id
				});
				$(e).wrap(lnk);
			}
		});
	});
	*/
}(jQuery));