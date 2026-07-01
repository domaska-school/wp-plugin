(function($) {
	// Обработка 
	$(document).ready(function() {
		var container = $("#domcad-gallery-order")[0],
			sortable = new Sortable(container, {
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
				.map(Number),
				// Фрейм wp.media
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
			// Событие открытия
			frame.on('open', function() {
				var selection = frame.state().get('selection');
				selectedIds.forEach(function(id) {
					var attachment = wp.media.attachment(id);
					if (attachment && attachment.get('id')) {
					    selection.add([attachment]);
					}
				});
			});
			// Событие выбора
			frame.on('select', function() {
				var selection = frame.state().get('selection').map(function(attachment) {
					return attachment.toJSON();
				});
				var ids = selection.map(function(img) {
					return img.id;
				}).join(',');
				$('#domcad_slider_home').val(ids);
				// Обновление просмотра
				updatePreview(selection);
			});
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
			current = current.filter( x => x !== id );
			$('#domcad_slider_home').val(current.join(','));
			$tag.remove();
		});
		function updatePreview(attachments) {
			var $container = $('#domcad-gallery-order');
			$container.empty();
			attachments.forEach(function(att) {
				var $span = $('<span>')
					.addClass('domcad-img-tag')
					.attr('data-id', att.id);
				console.log(att);
				// Получаем миниатюры
				if (att.sizes) {
					let url = att.sizes.gallery_image ? att.sizes.gallery_image.url : att.sizes.full.url;
					$span.append(
						$('<img>')
							.attr('src', url)
							.attr('alt', att.alt ? att.alt : (att.title ? att.title : at.id))
							.attr('title', att.title ? att.title : at.id)
					);
				} else {
					// запасной вариант, если миниатюры нет
					$span.text(att.id);
				}

				$span.append(
					$('<span>').addClass('domcad-remove-img').html('&times;')
				);

				$container.append($span);
			});
		}
	});
})(jQuery);