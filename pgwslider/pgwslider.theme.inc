<?php

/**
 * @file
 * Preprocessors and helper functions to make theming easier.
 */

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Render\Element;
use Drupal\Core\Template\Attribute;

/**
 * Prepares variables for image pgwslider formatter templates.
 *
 * Default template: image-pgwslider-formatter.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - item: An ImageItem object.
 *   - item_attributes: An optional associative array of html attributes to be
 *     placed in the img tag.
 *   - image_style: An optional image style.
 *   - url: An optional \Drupal\Core\Url object.
 */
function template_preprocess_image_pgwslider_formatter(&$variables) {
  if ($variables['image_style']) {
    $variables['image'] = array(
      '#theme' => 'image_style',
      '#style_name' => $variables['image_style'],
    );
  }
  else {
    $variables['image'] = array(
      '#theme' => 'image',
    );
  }
  $variables['image']['#attributes'] = $variables['item_attributes'];

  $item = $variables['item'];

  // Do not output an empty 'title' attribute.
  if (Unicode::strlen($item->title) != 0) {
    $variables['image']['#title'] = $item->title;
  }

  if (($entity = $item->entity) && empty($item->uri)) {
    $variables['image']['#uri'] = $entity->getFileUri();
  }
  else {
    $variables['image']['#uri'] = $item->uri;
  }

  foreach (array('width', 'height', 'alt') as $key) {
    $variables['image']["#$key"] = $item->$key;
  }

  $variables['caption_text'] = $item->alt;
}

/**
 * Prepares variables for container templates.
 *
 * Default template: images-pgwslider-formatter.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #attributes, #children.
 */
function template_preprocess_images_pgwslider_formatter(&$variables) {
  $element = $variables['element'];
  // Ensure #attributes is set.
  $element += array('#attributes' => array());

  $settings = $element['#settings'];

  $variables['arrownavigator'] = $settings['arrownavigator'];
  $variables['bulletnavigator'] = $settings['bulletnavigator'];

  $variables['children'] = $element['#children'];
  $variables['attributes'] = $element['#attributes'];
}