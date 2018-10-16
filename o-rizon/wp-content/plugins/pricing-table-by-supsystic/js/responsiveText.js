(function( $ ){

  $.fn.responsiveText = function( options ) {

    // Setup options
    var settings = $.extend({
          'minFontSize' : Number.NEGATIVE_INFINITY,
          'maxFontSize' : Number.POSITIVE_INFINITY
        }, options);

    return this.each(function(){

      // Store the object
      var $this = $(this),
          text = $this.get(0);

      if (!text) return;

      $this.data('original-font-size', parseFloat(
        window.getComputedStyle(text).fontSize
      ), 10);

      // Resizer() resizes items based on the object width divided by the compressor * 10
      var resizer = function () {
        var ratio = null,
            originalWidth = window.screen.availWidth,
            currentWidth = window.innerWidth,
            size = $this.data('original-font-size');

        if (originalWidth == currentWidth || !size) return;

        if (currentWidth != originalWidth)
          ratio = originalWidth / currentWidth;

        if (currentWidth > originalWidth)
          size *= ratio;
        else
          size /= ratio;

        size = Math.max(
          Math.min(
            size, 
            settings.maxFontSize
          ), 
          settings.minFontSize
        );

        $this.css('font-size', size + 'px');
      };

      resizer();

      // Call on resize. Opera debounces their resize by default.
      $(window).on('resize.responsiveText orientationchange.responsiveText', resizer);

    });

  };

})( jQuery );