(function ($) {
	'use strict';

	$.fn.sModal = function(options) {
		var self = this,
			settings = $.extend({
				width: 640,
				height: 480,
				buttons: [],
			}, options),
			$modalOverlay,
			$modalContainer,
			$closeModalButton,
			$modalContent,
			$modalButtons,
			modalOverlayStyle = {
				position: 'fixed',
				top: 0,
				left: 0,
				background: 'rgba(0,0,0,.75)',
				width: '100%',
				height: '100%',
				'z-index': '100000',
				display: 'none',
				'overflow-y': 'auto',
			},
			modalContainerStyle = {
				width: settings.width,
				position: 'absolute',
				top: '50%',
				left: '50%',
				'max-width': 'calc(100% - 1.5em)',
				'margin-right': '-50%',
				'margin-bottom': '20px',
				transform: 'translate(-50%, -50%)',
				background: '#fff',
				padding: '1em',
			},
			modalContentStyle = {
				height: 'calc(100% - 3.6em)',
				'overflow-y': 'auto',
				'overflow-x': 'hidden',
				padding: '1em',
			},
			closeButtonStyle = {
				position: 'absolute',
				right: '10px',
				top: '10px',
				'font-size': '35px',
				color: '#fff',
				cursor: 'pointer',
			},
			modalButtonsStyle = {
				position: 'relative',
				width: '100%',
				'text-align': 'right',
				'margin-top': '1em',
			};


		function closeModal() {
			self.trigger('close.before');
			if ($('.sModal:visible').length < 2) {
				$('body').css('overflow', 'auto');
			}
			$modalOverlay.fadeOut();
			self.trigger('close.after');
		}

		function openModal() {
			$('body').css('overflow', 'hidden');
			$modalOverlay.show(0, function() {
				resizeModal();
				$modalOverlay.hide().fadeIn();
			});
			
			if( parseInt($('.sc-modal-container').css('top')) > $(window).height()){
				var offset = $(window).height() / 2 + 40;
				$('.sc-modal-container').css('top' , offset);
			}
			
		}

		function resizeModal() {
			var offset = '50%';
			if ($modalContainer.height() > $(window).height()) {
				offset = $modalContainer.height() / 2 + 40;
			}
			$modalContainer.css('top', offset);
		}

		function createInstance($modalTemplate) {

			$modalOverlay = $('<div class="sc-modal-overlay" tabindex="0">').css(modalOverlayStyle);
			
			$modalContainer = $('<div class="sc-modal-container">').css(modalContainerStyle);
			
			$closeModalButton = $('<div class="sc-modal-close-button">&times;</div>').css(closeButtonStyle);
			$closeModalButton.appendTo($modalOverlay);

			$modalContent = $('<div class="sc-modal-content">').css(modalContentStyle);
			$modalContent.appendTo($modalContainer);

			$modalTemplate.appendTo($modalContent).show();

			$modalContainer.appendTo($modalOverlay);

			$modalOverlay.appendTo('body');
			$modalOverlay.add($closeModalButton).on('click', function(event) {
				if ($(this).is($(event.target))) {
					closeModal();
				}
			});

			$(document).on('keyup', function(event) {
				if (event.which == 27 && $modalOverlay.is(':visible')) {
					closeModal();
				}
			});

			var resizeTimer;

			$(window).on('resize', function(event) {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function() {
					resizeModal();
				}, 250);
			});

			if (settings.buttons.length > 0) {
				$modalButtons = $('<div class="sc-modal-action-buttons">').css(modalButtonsStyle);

				for (var i = 0; i < settings.buttons.length; i++) {
					$('<button>').attr('class', settings.buttons[i].class || null)
					.html(settings.buttons[i].content || null)
					.on('click', $.proxy(settings.buttons[i].event, $modalTemplate))
					.appendTo($modalButtons);
				}

				self.$buttons = $modalButtons;

				$modalButtons.appendTo($modalContainer);
			}
			
		}

		createInstance(this);

		$.extend(this, {
			open: function() {
				this.trigger('open.before');
				openModal();
				this.trigger('open.after');
				return this;
			},
			close: function() {
				this.trigger('close.before');
				closeModal();
				this.trigger('close.after');
				return this;
			}
		});

		return this;
	};

})(jQuery);
