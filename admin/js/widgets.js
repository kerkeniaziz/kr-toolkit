/**
 * KR Toolkit Widgets JavaScript
 * 
 * @since 1.2.8
 */

(function($) {
	'use strict';

	// Counter widget animation
	$('.kr-counter-number').waypoint(function() {
		var $this = $(this);
		var number = parseInt($this.text().replace(/[^0-9]/g, ''));
		var suffix = $this.text().replace(/[0-9]/g, '');
		
		$({value: 0}).animate({value: number}, {
			duration: 2000,
			step: function() {
				$this.text(Math.floor(this.value) + suffix);
			}
		});
		
		this.destroy();
	}, { offset: '80%' });

	// Icon box hover effect
	$('.kr-icon-box').on('mouseenter', function() {
		$(this).css('transform', 'translateY(-4px)');
	}).on('mouseleave', function() {
		$(this).css('transform', 'translateY(0)');
	});

	// Social links smooth transition
	$('.kr-social-link').on('mouseenter', function() {
		$(this).stop(true, true).animate({
			backgroundColor: '#a855f7'
		}, 200);
	}).on('mouseleave', function() {
		$(this).stop(true, true).animate({
			backgroundColor: '#667eea'
		}, 200);
	});

})(jQuery);
