!(function($){
	RegExp.escape = function(string) {
		return string.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')
	};
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
	/**
	 * Клик на ссылках документов
	 * pdf, xlsx
	 */
	$(document).on('click', "a[href$='.pdf'], a[href$='.xlsx']", e => {
		let target = e.target.nodeName == "A" ? $(e.target) : $(e.target).closest('a'),
			base = window.location.origin + '/',
			reg = new RegExp("^" + base),
			href, arr, ext, options, url;
		/**
		 * Если существует
		 */
		if (target[0]) {
			href = target[0].href;
			/**
			 * Если проходит регулярку
			 */
			if(reg.test(href)){
				arr = href.split('.');
				ext = arr.at(-1).toLowerCase();
				url = window.location.origin + `/viewer/${ext}_viewer/?file=` + encodeURI(href);
				options = {
					src: url,
					toolbar: true,
					smallBtn: false,
					buttons: [
						"close"
					],
					opts : {
						afterShow : function( instance, current ) {
							$('.fancybox-content').addClass(ext == "xlsx" ? 'xlsx_viewer' : 'docx_viewer')
						},
						afterLoad : function( instance, current ) {
							$('.fancybox-content').addClass(ext == "xlsx" ? 'xlsx_viewer' : 'docx_viewer')
						},
					}
				};
				console.log(options.src);
				/**
				 * Открываем fancybox
				 */
				e.preventDefault();
				$.fancybox.open(options);
				return !1;
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