(function ($, Drupal) {
  Drupal.behaviors.propertySearch = {
    attach: function (context, settings) {
      $('.property-search-link').once('search-form-btn').click(function (e) {
        e.preventDefault();
        console.log('sample');
        $('.search-form-btn').mousedown();
      });
    }
  };
})(jQuery, Drupal);
