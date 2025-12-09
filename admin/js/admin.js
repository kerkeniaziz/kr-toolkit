/**
 * KR Toolkit Admin JavaScript
 * Following Astra Sites' modern patterns for demo import
 *
 * @package KR_Toolkit
 * @since 1.3.0
 */
(function($) {
	'use strict';

	const KRToolkit = {
		importProcess: null,
		currentStep: 0,
		totalSteps: 0,

		/**
		 * Initialize
		 */
		init: function() {
			this.bindEvents();
			this.checkSystemRequirements();
		},

		/**
		 * Bind events
		 */
		bindEvents: function() {
			// Demo import
			$(document).on('click', '.kr-import-demo', this.showImportModal.bind(this));
			$(document).on('click', '.kr-import-confirm', this.importDemo.bind(this));
			$(document).on('click', '.kr-import-cancel', this.hideImportModal.bind(this));
			
			// Preview demo
			$(document).on('click', '.kr-preview-demo', this.previewDemo.bind(this));
			
			// Child theme creation
			$(document).on('click', '.kr-create-child-theme', this.createChildTheme.bind(this));
			
			// License management
			$(document).on('click', '.kr-activate-license', this.activateLicense.bind(this));
			$(document).on('click', '.kr-deactivate-license', this.deactivateLicense.bind(this));
			
			// Tab navigation
			$(document).on('click', '.kr-toolkit-nav-tab a', this.switchTab.bind(this));
			
			// Close modal on outside click
			$(document).on('click', '.kr-import-process', function(e) {
				if ($(e.target).hasClass('kr-import-process')) {
					KRToolkit.hideImportModal();
				}
			});
		},

		/**
		 * Check system requirements
		 */
		checkSystemRequirements: function() {
			if ($('.kr-system-check').length === 0) {
				return;
			}

			$.ajax({
				url: krToolkitAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'kr_check_system',
					nonce: krToolkitAdmin.nonce
				},
				success: function(response) {
					if (response.success && response.data) {
						KRToolkit.displaySystemStatus(response.data);
					}
				}
			});
		},

		/**
		 * Display system status
		 */
		displaySystemStatus: function(status) {
			const $container = $('.kr-system-check');
			if (!$container.length) return;

			let html = '<div class="kr-system-status">';
			
			$.each(status, function(key, item) {
				const statusClass = item.status ? 'success' : (item.required ? 'error' : 'warning');
				const icon = item.status ? '✓' : '✗';
				
				html += `
					<div class="kr-status-item ${statusClass}">
						<span class="kr-status-icon">${icon}</span>
						<div class="kr-status-content">
							<strong>${item.label}</strong>: ${item.value}
							${item.message ? `<p>${item.message}</p>` : ''}
						</div>
					</div>
				`;
			});
			
			html += '</div>';
			$container.html(html);
		},

		/**
		 * Show import modal
		 */
		showImportModal: function(e) {
			e.preventDefault();
			
			const $button = $(e.currentTarget);
			const demoSlug = $button.data('demo-slug');
			const demoName = $button.data('demo-name');
			const isPro = $button.data('is-pro');
			
			// Check if pro demo and license is needed
			if (isPro && !krToolkitAdmin.hasLicense) {
				alert(krToolkitAdmin.strings.requiresLicense);
				return;
			}

			// Create modal HTML
			const modalHtml = `
				<div class="kr-import-process active">
					<div class="kr-import-modal">
						<div class="kr-import-header">
							<h2 class="kr-import-title">${krToolkitAdmin.strings.importDemo}: ${demoName}</h2>
							<p class="kr-import-description">${krToolkitAdmin.strings.importDescription}</p>
						</div>
						<div class="kr-import-body">
							<div class="kr-import-options">
								<label class="kr-import-option">
									<input type="checkbox" name="import_content" checked>
									<span>${krToolkitAdmin.strings.importContent}</span>
								</label>
								<label class="kr-import-option">
									<input type="checkbox" name="import_widgets" checked>
									<span>${krToolkitAdmin.strings.importWidgets}</span>
								</label>
								<label class="kr-import-option">
									<input type="checkbox" name="import_customizer" checked>
									<span>${krToolkitAdmin.strings.importCustomizer}</span>
								</label>
							</div>
							<div class="kr-import-warning">
								<p><strong>${krToolkitAdmin.strings.importWarning}</strong></p>
								<p>${krToolkitAdmin.strings.importBackup}</p>
							</div>
						</div>
						<div class="kr-import-footer">
							<button class="kr-btn kr-btn-secondary kr-import-cancel">${krToolkitAdmin.strings.cancel}</button>
							<button class="kr-btn kr-import-confirm" data-demo-slug="${demoSlug}">${krToolkitAdmin.strings.startImport}</button>
						</div>
						<div class="kr-import-progress" style="display: none;">
							<ul class="kr-import-steps"></ul>
							<div class="kr-progress">
								<div class="kr-progress-bar" style="width: 0%"></div>
							</div>
							<p class="kr-import-status">${krToolkitAdmin.strings.preparing}</p>
						</div>
					</div>
				</div>
			`;

			// Remove existing modal and add new one
			$('.kr-import-process').remove();
			$('body').append(modalHtml);
		},

		/**
		 * Hide import modal
		 */
		hideImportModal: function() {
			$('.kr-import-process').removeClass('active');
			setTimeout(function() {
				$('.kr-import-process').remove();
			}, 300);
		},

		/**
		 * Import demo
		 */
		importDemo: function(e) {
			e.preventDefault();
			
			const $button = $(e.currentTarget);
			const demoSlug = $button.data('demo-slug');
			const $modal = $button.closest('.kr-import-modal');
			
			// Get import options
			const options = {
				content: $modal.find('input[name="import_content"]').is(':checked'),
				widgets: $modal.find('input[name="import_widgets"]').is(':checked'),
				customizer: $modal.find('input[name="import_customizer"]').is(':checked')
			};

			// Hide options and show progress
			$modal.find('.kr-import-body, .kr-import-footer').hide();
			$modal.find('.kr-import-progress').show();

			// Define import steps
			const steps = [];
			if (options.content) steps.push('content');
			if (options.widgets) steps.push('widgets');
			if (options.customizer) steps.push('customizer');
			
			this.totalSteps = steps.length;
			this.currentStep = 0;

			// Build steps UI
			let stepsHtml = '';
			steps.forEach((step, index) => {
				stepsHtml += `
					<li class="kr-import-step" data-step="${step}">
						<span class="kr-step-icon">⏳</span>
						<span class="kr-step-label">${this.getStepLabel(step)}</span>
					</li>
				`;
			});
			$modal.find('.kr-import-steps').html(stepsHtml);

			// Start import process
			this.processImportSteps(demoSlug, steps, 0);
		},

		/**
		 * Process import steps
		 */
		processImportSteps: function(demoSlug, steps, index) {
			if (index >= steps.length) {
				this.completeImport();
				return;
			}

			const step = steps[index];
			this.currentStep = index + 1;
			
			// Update UI
			this.updateImportProgress();
			this.updateStepStatus(step, 'processing');

			// Send AJAX request
			$.ajax({
				url: krToolkitAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'kr_import_demo',
					nonce: krToolkitAdmin.nonce,
					demo_slug: demoSlug,
					step: step
				},
				success: (response) => {
					if (response.success) {
						this.updateStepStatus(step, 'completed');
						// Process next step
						setTimeout(() => {
							this.processImportSteps(demoSlug, steps, index + 1);
						}, 500);
					} else {
						this.updateStepStatus(step, 'error');
						this.showError(response.data.message || krToolkitAdmin.strings.importError);
					}
				},
				error: () => {
					this.updateStepStatus(step, 'error');
					this.showError(krToolkitAdmin.strings.importError);
				}
			});
		},

		/**
		 * Update import progress
		 */
		updateImportProgress: function() {
			const progress = (this.currentStep / this.totalSteps) * 100;
			$('.kr-progress-bar').css('width', progress + '%');
			
			const statusText = krToolkitAdmin.strings.importingStep
				.replace('{current}', this.currentStep)
				.replace('{total}', this.totalSteps);
			$('.kr-import-status').text(statusText);
		},

		/**
		 * Update step status
		 */
		updateStepStatus: function(step, status) {
			const $step = $(`.kr-import-step[data-step="${step}"]`);
			const icons = {
				processing: '⏳',
				completed: '✓',
				error: '✗'
			};
			
			$step.removeClass('processing completed error').addClass(status);
			$step.find('.kr-step-icon').text(icons[status]);
		},

		/**
		 * Complete import
		 */
		completeImport: function() {
			$('.kr-progress-bar').css('width', '100%');
			$('.kr-import-status').text(krToolkitAdmin.strings.importComplete);
			
			setTimeout(() => {
				alert(krToolkitAdmin.strings.importSuccess);
				window.location.href = krToolkitAdmin.homeUrl;
			}, 1000);
		},

		/**
		 * Show error
		 */
		showError: function(message) {
			$('.kr-import-status').html(`<span style="color: #dc3232;">${message}</span>`);
			
			setTimeout(() => {
				if (confirm(krToolkitAdmin.strings.importRetry)) {
					location.reload();
				} else {
					this.hideImportModal();
				}
			}, 2000);
		},

		/**
		 * Get step label
		 */
		getStepLabel: function(step) {
			const labels = {
				content: krToolkitAdmin.strings.importContent || 'Import Content',
				widgets: krToolkitAdmin.strings.importWidgets || 'Import Widgets',
				customizer: krToolkitAdmin.strings.importCustomizer || 'Import Customizer Settings'
			};
			return labels[step] || step;
		},

		/**
		 * Preview demo
		 */
		previewDemo: function(e) {
			e.preventDefault();
			const url = $(e.currentTarget).data('preview-url');
			window.open(url, '_blank');
		},

		/**
		 * Create child theme
		 */
		createChildTheme: function(e) {
			e.preventDefault();
			
			const $button = $(e.currentTarget);
			const $form = $button.closest('form');
			const themeName = $form.find('#child-theme-name').val();
			const themeDescription = $form.find('#child-theme-description').val();
			const themeAuthor = $form.find('#child-theme-author').val();
			
			if (!themeName) {
				alert(krToolkitAdmin.strings.enterThemeName || 'Please enter a theme name.');
				return;
			}
			
			// Show loading
			$button.addClass('kr-loading').prop('disabled', true);
			const originalText = $button.text();
			$button.text(krToolkitAdmin.strings.creating || 'Creating...');
			
			// Send AJAX request
			$.ajax({
				url: krToolkitAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'kr_create_child_theme',
					nonce: krToolkitAdmin.nonce,
					theme_name: themeName,
					theme_description: themeDescription,
					theme_author: themeAuthor
				},
				success: function(response) {
					if (response.success) {
						alert(response.data.message || krToolkitAdmin.strings.createSuccess);
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
					$button.text(originalText);
				}
			});
		},

		/**
		 * Activate license
		 */
		activateLicense: function(e) {
			e.preventDefault();
			
			const $button = $(e.currentTarget);
			const $form = $button.closest('form');
			const licenseKey = $form.find('#license-key').val();
			
			if (!licenseKey) {
				alert(krToolkitAdmin.strings.enterLicense || 'Please enter your license key.');
				return;
			}
			
			// Show loading
			$button.addClass('kr-loading').prop('disabled', true);
			const originalText = $button.text();
			$button.text(krToolkitAdmin.strings.activating || 'Activating...');
			
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
						alert(response.data.message || krToolkitAdmin.strings.activateSuccess);
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
					$button.text(originalText);
				}
			});
		},

		/**
		 * Deactivate license
		 */
		deactivateLicense: function(e) {
			e.preventDefault();
			
			if (!confirm(krToolkitAdmin.strings.confirmDeactivate || 'Are you sure you want to deactivate your license?')) {
				return;
			}
			
			const $button = $(e.currentTarget);
			
			// Show loading
			$button.addClass('kr-loading').prop('disabled', true);
			const originalText = $button.text();
			
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
						alert(response.data.message || krToolkitAdmin.strings.deactivateSuccess);
						location.reload();
					} else {
						alert(response.data.message || krToolkitAdmin.strings.deactivateError);
					}
				},
				error: function() {
					alert(krToolkitAdmin.strings.deactivateError);
				},
				complete: function() {
					$button.removeClass('kr-loading').prop('disabled', false);
					$button.text(originalText);
				}
			});
		},

		/**
		 * Switch tab
		 */
		switchTab: function(e) {
			e.preventDefault();
			
			const $tab = $(e.currentTarget);
			const target = $tab.attr('href');
			
			// Update tab state
			$('.kr-toolkit-nav-tab').removeClass('active');
			$tab.parent().addClass('active');
			
			// Update content
			$('.kr-toolkit-tab-content').hide();
			$(target).show();
			
			// Update URL
			if (history.pushState) {
				history.pushState(null, null, $tab.attr('href'));
			}
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		KRToolkit.init();
	});

})(jQuery);
