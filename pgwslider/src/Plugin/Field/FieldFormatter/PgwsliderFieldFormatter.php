<?php

/**
 * @file
 * Contains \Drupal\pgwslider\Plugin\Field\FieldFormatter\PgwsliderFormatter.
 */

namespace Drupal\pgwslider\Plugin\Field\FieldFormatter;


use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
/**
 * Plugin implementation of the 'pgwslider' formatter.
 *
 * @FieldFormatter(
 *   id = "pgwslider_formatter",
 *   label = @Translation("Pgwslider Gallery"),
 *   field_types = {
 *     "image",
 *   },
 * )
 */
class PgwsliderFieldFormatter extends ImageFormatterBase implements ContainerFactoryPluginInterface {

   /*
   * The image style entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The link generator.
   *
   * @var \Drupal\Core\Utility\LinkGeneratorInterface
   */
  protected $linkGenerator;

  /**
   * Constructs a ResponsiveImageFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityStorageInterface $image_style_storage
   *   The image style storage.
   * @param \Drupal\Core\Utility\LinkGeneratorInterface $link_generator
   *   The link generator service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityStorageInterface $image_style_storage, LinkGeneratorInterface $link_generator, AccountInterface $current_user) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->imageStyleStorage = $image_style_storage;
    $this->linkGenerator = $link_generator;
    $this->currentUser = $current_user;
  }
  
 /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity.manager')->getStorage('image_style'),
      $container->get('link_generator'),
      $container->get('current_user')
    );
  } 
  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $pgw_imageoptions = array();
    // getting the existing image styles
    $image_styles = $this->imageStyleStorage->loadMultiple();
    if ($image_styles && !empty($image_styles)) {
      foreach ($image_styles as $machine_name => $image_style) {
          $pgw_imageoptions[$machine_name] = $image_style->label();
      }
    }

    $element['pgw_autoplay'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable auto play.'),
      '#default_value' => $this->getSetting('pgw_autoplay'),
    );

    $element['pgw_arrownavigator'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable arrow navigator'),
      '#default_value' => $this->getSetting('pgw_arrownavigator'),
    );

    $element['pgw_displaylist'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable display list(small thumbnails)'),
      '#default_value' => $this->getSetting('pgw_displaylist'),
     );

    $element['pgw_caption'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable caption'),
      '#default_value' => $this->getSetting('pgw_caption'),
     );

    $element['image_style'] = array(
      '#title' => t('Select Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#required' => TRUE,
      '#options' => $pgw_imageoptions,
    );
    
    $element['pgw_autoplayinterval'] = array(
      '#type' => 'number',
      '#title' => $this->t('Autoplay interval'),
      '#attributes' => array(
        'min' => 0,
        'step' => 1,
        'value' => $this->getSetting('pgw_autoplayinterval'),
      ),
      '#description' => t('Interval (in milliseconds) to go for next.'),
    );
     
    return $element;
  }
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    // Check if image not exist.
    if ($items->isEmpty()) {
      return array();
    }
    $entity = $items->getEntity();
    $field_instance = $items->getFieldDefinition();
    $entity_type_id = $entity->getEntityTypeId();
    $entity_id = $entity->id();
    $field_name = $field_instance->getName();
    $display_name = $this->viewMode;
    
   
    $files = $this->getEntitiesToView($items, $langcode);
    $settings = $this->getSettings();

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }
    
    $url = NULL;
    $image_link_setting = $this->getSetting('image_link');
    // Check if the formatter involves a link.
    if ($image_link_setting == 'content') {
      $entity = $items->getEntity();
      if (!$entity->isNew()) {
        $url = $entity->urlInfo();
      }
    }
    elseif ($image_link_setting == 'file') {
      $link_file = TRUE;
    }

    $image_style_setting = $this->getSetting('image_style');

    // Collect cache tags to be added for each item in the field.
    $cache_tags = array();
    if (!empty($image_style_setting)) {
      $image_style = $this->imageStyleStorage->load($image_style_setting);
      $cache_tags = $image_style->getCacheTags();
    }
    
    foreach ($files as $delta => $file) {
      if (isset($link_file)) {
        $image_uri = $file->getFileUri();
        $url = Url::fromUri(file_create_url($image_uri));
      }
      $cache_tags = Cache::mergeTags($cache_tags, $file->getCacheTags());

      // Extract field item attributes for the theme function, and unset them
      // from the $item so that the field template does not re-render them.
      $item = $file->_referringItem;
      $item_attributes = $item->_attributes;
      unset($item->_attributes);

      $elements[$delta] = array(
        '#theme' => 'image_pgwslider_formatter',
        '#item' => $item,
        '#item_attributes' => $item_attributes,
        '#image_style' => $image_style_setting,
        '#pgw_caption' => $this->getSetting('pgw_caption'),
        '#url' => $url,
        '#settings' => $settings,
        '#cache' => array(
          'tags' => $cache_tags,
        ),
      );

    }
    

    $container = array(
      '#theme' => 'images_pgwslider_formatter',
      '#children' => $elements,
      '#settings' => $settings,
      '#attributes' => array(
        'class' => array('pwslider_container'),
        'id' => array('pwslider-dom-id'),
      ),
    );
    
    // Attach library.
    $container['#attached']['library'][] = 'pgwslider/jquery.pgwslider.slider';
    
    $settings = [];

    // Auto Play
    if ($this->getSetting('pgw_autoplay')) {
      $settings['$pgw_autoplay'] = $this->getSetting('pgw_autoplay');
    }  
    // Arrow key navigator
    if ($this->getSetting('pgw_arrownavigator')) {
      $settings['$pgw_arrownavigator'] = $this->getSetting('pgw_arrownavigator');
    }  
     // Display List
    if ($this->getSetting('pgw_displaylist')) {
      $settings['$pgw_displaylist'] = $this->getSetting('pgw_displaylist');
    }  
    //Caption
    if ($this->getSetting('pgw_caption')) {
      $settings['$pgw_caption'] = $this->getSetting('pgw_caption');
    } 
    //Auto play interval
    if ($this->getSetting('pgw_autoplayinterval')) {
      $settings['$pgw_autoplayinterval'] = $this->getSetting('pgw_autoplayinterval');
    }  
    
    // Global settings
    $config_settings = \Drupal::config('pgwslider.settings');
    // Show the counter
    if ($config_settings->get('pgwdisplay_count')) {
      $settings['$pgwdisplay_count'] = $config_settings->get('pgwdisplay_count');
    } 
    // Transition effect
    if ($config_settings->get('pgwtransition_effect')) {
      $settings['$pgwtransition_effect'] = $config_settings->get('pgwtransition_effect');
    } 
    
    // Attach settings.
    $container['#attached']['drupalSettings']['PgwsliderView'][] = $settings;
    return $container;
  }
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $settings = $this->getSettings();
    $summary = [];
    
     if ($this->getSetting('pgw_autoplay')) {
      $summary[] = t('Autoplay: @pgw_autoplay', array('@pgw_autoplay' => $settings['pgw_autoplay']));
    }
    
    if ($this->getSetting('pgw_arrownavigator')) {
      $summary[] = t('Arrow navigator: @pgw_arrownavigator', array('@pgw_arrownavigator' => $settings['pgw_arrownavigator']));
    }

    if ($this->getSetting('pgw_displaylist')) {
      $summary[] = t('Display list: @pgw_displaylist', array('@pgw_displaylist' => $settings['pgw_displaylist'])); 
     }

    if ($this->getSetting('pgw_caption')) {
      $summary[] = t('Display caption: @pgw_caption', array('@pgw_caption' => $settings['pgw_caption']));  
    }
    //  image style settings
    $image_style = $this->imageStyleStorage->load($settings['image_style']);
    if ($image_style) {
      $summary[] = t('Image style: @image_style', array('@image_style' => $image_style->label()));
    }
  
    $summary[] = t('Autoplay interval : @pgw_autoplayinterval', array('@pgw_autoplayinterval' => $settings['pgw_autoplayinterval']));

    return $summary;
  }
   
   /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'image_style' => '',
      'pgw_autoplay' => TRUE,
      'pgw_autoplayinterval' => 3000,
      'pgw_arrownavigator' => TRUE,
      'pgw_displaylist' => TRUE,
      'pgw_caption' => true,
    ) + parent::defaultSettings();
  }
}