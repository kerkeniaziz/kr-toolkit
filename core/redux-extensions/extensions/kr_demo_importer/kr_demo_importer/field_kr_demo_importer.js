// KR Demo Importer JavaScript

(function($) {
	$(document).ready(function() {
		kr_init_demo_importer();
	});

	function kr_init_demo_importer() {
		$(document).on('click', '.import-demo-data', function(e) {
			e.preventDefault();

			var $button = $(this);
			var $wrapper = $button.closest('.wrap-importer');
			var demoId = $wrapper.data('demo-id');
			var nonce = $wrapper.data('nonce');

			if ($wrapper.hasClass('imported') && !$button.attr('id') || $button.attr('id') === '') {
				return;
			}

			if (!confirm('Are you sure you want to import this demo? This may replace your current content.')) {
				return;
			}

			$wrapper.find('.spinner').addClass('is-active');
			$button.prop('disabled', true);

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'redux_kr_demo_importer',
					type: 'import-demo-content',
					demo_import_id: demoId,
					nonce: nonce
				},
				success: function(response) {
					$wrapper.find('.spinner').removeClass('is-active');

					if (response.status) {
						// Update UI to show imported state
						$wrapper.addClass('imported active');
						$wrapper.removeClass('not-imported');
						$wrapper.find('.more-details').text('Demo Imported').attr('style', 'background: #10b981 !important;');
						$wrapper.find('.import-demo-data:not(#kr-importer-reimport)').replaceWith('<span class="button-secondary kr-importer-buttons">Imported</span>');

						// Show re-import button if not visible
						if ($wrapper.find('#kr-importer-reimport').length === 0) {
							$wrapper.find('.theme-actions').append('<div id="kr-importer-reimport" class="kr-importer-buttons button-primary import-demo-data importer-button">Re-Import</div>');
						}

						alert('Demo imported successfully! Page will reload now.');
						location.reload();
					} else {
						alert('Import failed: ' + (response.message || 'Unknown error'));
						$button.prop('disabled', false);
					}
				},
				error: function(xhr, status, error) {
					$wrapper.find('.spinner').removeClass('is-active');
					$button.prop('disabled', false);
					alert('Error: ' + error);
				}
			});
		});
	}
})(jQuery);
