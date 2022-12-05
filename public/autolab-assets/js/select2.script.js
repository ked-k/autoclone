(function ($) {
    "use strict";
   $('select').each(function () {
    $(this).select2({
      theme: 'bootstrap-5',
      width: 'style',
      placeholder: $(this).attr('placeholder'),
      allowClear: Boolean($(this).data('allow-clear')),
    });
  });

})(jQuery);
