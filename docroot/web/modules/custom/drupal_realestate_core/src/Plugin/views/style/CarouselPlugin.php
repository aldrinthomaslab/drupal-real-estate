<?php
namespace Drupal\drupal_realestate_core\Plugin\views\style;

use Drupal\views\Plugin\views\style\DefaultStyle;

/**
 * Style plugin to render a list of years and months
 * in reverse chronological order linked to content.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "carousel",
 *   title = @Translation("Carousel"),
 *   help = @Translation("View style to display items in a carousel format."),
 *   theme = "views_view_carousel",
 *   display_types = { "normal" }
 * )
 */

class CarouselPlugin extends DefaultStyle {

}
