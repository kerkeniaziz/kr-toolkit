/**
 * KR Toolkit Admin JavaScript
 */
(function($) {
	'use strict';

	const KRToolkit = {
		/**
		 * Initialize
		 */
		init: function() {
			this.bindEvents();
		},

		/**
		 * Bind events
		 */
		bindEvents: function() {
			// Demo import
			$('.kr-import-demo').on('click', this.importDemo);
			
			// Child theme creation
			$('.kr-create-child-theme').on('click', this.createChildTheme);
			
			// License activation
			$('.kr-activate-license').on('click', this.activateLicense);
			
			// License deactivation
			$('.kr-deactivate-license').on('click', this.deactivateLicense);
		},

		/**
		 * Import demo
		 */
		importDemo: function(e) {
			e.preventDefault();
			
			const $button = $(this);
			const demoSlug = $button.data('demo-slug');
			
			// Confirm import
			if (!confirm(krToolkitAdmin.strings.confirmImport)) {
				return;
			}
			
			// Show loading
			$button.addClass('kr-loading').prop('disabled', true);
			$button.text(krToolkitAdmin.strings.importing);
			
			// Send AJAX request
			$.ajax({
				url: krToolkitAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'kr_import_demo',
					nonce: krToolkitAdmin.nonce,
					demo_slug: demoSlug
				},
				success: function(response) {
					if (response.success) {
						alert(krToolkitAdmin.strings.importSuccess);
						location.reload();
					} else {
						alert(response.data.message || krToolkitAdmin.strings.importError);
					}
				},
				error: function() {
					alert(krToolkitAdmin.strings.importError);
				},
				complete: function() {
					$button.removeClass('kr-loading').prop('disabled', false);
					$button.text('Import Demo');
				}
			});
		},

		/**
		 * Create child theme
		 */
		createChildTheme: function(e) {
			e.preventDefault();
			
			const $form = $('.kr-child-theme-form');
			const $button = $(this);
			const themeName = $('#child-theme-name').val();
			
			if (!themeName) {
				alert('Please enter a theme name.');
				return;
			}
			
			// Show loading
			$button.addClass('kr-loading').prop('disabled', true);
			$button.text(krToolkitAdmin.strings.creating);
			
			// Send AJAX request
			$.ajax({
				url: krToolkitAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'kr_create_child_theme',
					nonce: krToolkitAdmin.nonce,
					theme_name: themeName
				},
				success: function(response) {
					if (response.success) {
						alert(krToolkitAdmin.strings.createSuccess);
						location.reload();
					} else {
						alert(response.data.message || krToolkitAdmin.strings.createError);
					}
				},
				error: function() {
					alert(krToolkitAdmin.strings.createError);
				},
				complete: function() {
					$button.removeClass('kr-loading').prop('disabled', false);
					$button.text('Create Child Theme');
				}
			});
		},

		/**
		 * Activate license
		 */
		activateLicense: function(e) {
			e.preventDefault();
			
			const $button = $(this);
			const licenseKey = $('#license-key').val();
			
			if (!licenseKey) {
				alert('Please enter your license key.');
				return;
			}
			
			// Show loading
			$button.addClass('kr-loading').prop('disabled', true);
			$button.text(krToolkitAdmin.strings.activating);
			
			// Send AJAX request
			$.ajax({
				url: krToolkitAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'kr_activate_license',
					nonce: krToolkitAdmin.nonce,
					license_key: licenseKey
				},
				success: function(response) {
					if (response.success) {
						alert(krToolkitAdmin.strings.activateSuccess);
						location.reload();
					} else {
						alert(response.data.message || krToolkitAdmin.strings.activateError);
					}
				},
				error: function() {
					alert(krToolkitAdmin.strings.activateError);
				},
				complete: function() {
					$button.removeClass('kr-loading').prop('disabled', false);
					$button.text('Activate License');
				}
			});
		},

		/**
		 * Deactivate license
		 */
		deactivateLicense: function(e) {
			e.preventDefault();
			
			if (!confirm('Are you sure you want to deactivate your license?')) {
				return;
			}
			
			const $button = $(this);
			
			// Show loading
			$button.addClass('kr-loading').prop('disabled', true);
			
			// Send AJAX request
			$.ajax({
				url: krToolkitAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'kr_deactivate_license',
					nonce: krToolkitAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						alert('License deactivated successfully!');
						location.reload();
					} else {
						alert(response.data.message || 'Deactivation failed.');
					}
				},
				error: function() {
					alert('Deactivation failed.');
				},
				complete: function() {
					$button.removeClass('kr-loading').prop('disabled', false);
				}
			});
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		KRToolkit.init();
	});

})(jQuery);
