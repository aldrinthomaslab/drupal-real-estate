(function ($, Drupal) {
  Drupal.behaviors.propertySearch = {
    attach: function (context, settings) {
      $('.property-search-link').once('search-form-btn').click(function (e) {
        e.preventDefault();

        $('.search-form-btn').click();
      });
    }
  };
})(jQuery, Drupal);
