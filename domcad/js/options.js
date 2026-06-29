(function($) {
	// Обработка 
	$(document).ready(function() {
		var frame = null,
			container = $("#domcad-gallery-order")[0];
		var sortable = new Sortable(container, {
			animation: 300,
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
		// Клик по кнопке выбора 
		$('#domcad-gallery_btn').on('click', function(e) {
			e.preventDefault();
			var selectedIds = $('#domcad_slider_home')
				.val()
				.split(',')
				.filter(w => w.length > 0)
				.map(Number);
			// Открытие окна выбора изображений
			if (!frame) {
				frame = wp.media({
					title: domcadTranslations.title,
					button: {
						text: domcadTranslations.insert
					},
					multiple: true,
					library: {
						// В нашей ситуации нужны только изображения
						type: 'image'
					},
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