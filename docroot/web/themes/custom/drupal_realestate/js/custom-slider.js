(function ($) {
  function collision($div1, $div2) {
      var x1 = $div1.offset().left;
      var w1 = 40;
      var r1 = x1 + w1;
      var x2 = $div2.offset().left;
      var w2 = 40;
      var r2 = x2 + w2;

      if (r1 < x2 || x1 > r2)
          return false;
      return true;
  }
  // Fetch Url value
  var getQueryString = function (parameter) {
      var href = window.location.href;
      var reg = new RegExp('[?&]' + parameter + '=([^&#]*)', 'i');
      var string = reg.exec(href);
      return string ? string[1] : null;
  };

  var min_value = parseInt($('.property-search-price-min').val());
  var max_value = parseInt($('.property-search-price-max').val())

  // End url
  // // slider call
  $('#slider').slider({
      range: true,
      min: min_value,
      max: max_value,
      step: 1,
      values: [min_value, max_value],
      slide: function (event, ui) {
        $('.property-search-price-min').val(ui.values[0]);
        $('.property-search-price-max').val(ui.values[1]);

        var new_min_val = abbreviateFormat(ui.values[0]);
        var new_max_val = abbreviateFormat(ui.values[1]);

        $('.ui-slider-handle:eq(0) .price-range-min').html(new_min_val);
        $('.ui-slider-handle:eq(1) .price-range-max').html(new_max_val);

        if (collision($('.price-range-min'), $('.price-range-max')) == true) {
          $('.price-range-min, .price-range-max').css('opacity', '0');
          $('.price-range-both').css('display', 'block');
          $('.price-range-both').html(new_min_val + ' - ' + new_max_val);
        } else {
          $('.price-range-min, .price-range-max').css('opacity', '1');
          $('.price-range-both').css('display', 'none');
        }
      }
  });

  function abbreviateFormat (value) {
    var abbr_value = parseInt(value);
    var abbr_suffix = [ 'K', 'M' ];
    var abbr = '';

    abbr_suffix.forEach(function (element) {
      if (abbr_value < 1000) {
        return transformToFixed(abbr_value) + abbr;
      }

      abbr_value = abbr_value / 1000;
      abbr = element;
    });

    return transformToFixed(abbr_value) + abbr;
  }

  function transformToFixed (value) {
    if ((value % 1) * 10 >= 1) {
      return value.toFixed(1);
    }

    return value.toFixed(0);
  }

  $('.ui-slider-range').append('<span class="price-range-both value"><i>' + $('#slider').slider('values', 0) +
      ' - </i>' + $('#slider').slider('values', 1) + '</span>');

  $('.ui-slider-handle:eq(0)').append('<span class="price-range-min value">' + abbreviateFormat(min_value) +
      '</span>');

  $('.ui-slider-handle:eq(1)').append('<span class="price-range-max value">' + abbreviateFormat(max_value) +
      '</span>');
})(jQuery);
