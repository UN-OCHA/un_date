<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Common functions.
 */
class UnDateTestBase extends FieldKernelTestBase {

  /**
   * Inline dataproviders.
   *
   * @var bool
   *
   * @see https://www.drupal.org/project/drupal/issues/1411074
   */
  protected $inlineDataProvider = TRUE;

  /**
   * A field storage to use in this test class.
   *
   * @var \Drupal\field\Entity\FieldStorageConfig
   */
  protected $fieldStorage;

  /**
   * The field used in this test class.
   *
   * @var \Drupal\field\Entity\FieldConfig
   */
  protected $field;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The locale storage.
   *
   * @var \Drupal\locale\StringStorageInterface
   */
  protected $localeStorage;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationManager
   */
  protected $stringTranslator;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'locale',
    'language',
    'system',
  ];

  /**
   * Test configuration.
   *
   * @var array
   */
  protected $testConfig = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->stringTranslator = $this->container->get('string_translation');

    $this->localeStorage = $this->container->get('locale.storage');
    $this->installSchema('locale', [
      'locales_location',
      'locales_source',
      'locales_target',
      'locale_file',
    ]);

    // Ensure we are building a new Language object for each test.
    $this->installConfig(['language']);
    $this->languageManager = $this->container->get('language_manager');
    $this->languageManager->reset();

    foreach ($this->getLanguages() as $lang_code) {
      if (!ConfigurableLanguage::load($lang_code)) {
        ConfigurableLanguage::createFromLangcode($lang_code)->save();
      }
    }
    $this->installConfig('locale');

    // Set an explicit site timezone.
    $this->config('system.date')
      ->set('timezone.user.configurable', 0)
      ->set('timezone.default', 'UTC')
      ->save();

    // Add a datetime range field.
    $this->fieldStorage = FieldStorageConfig::create([
      'field_name' => mb_strtolower($this->randomMachineName()),
      'entity_type' => 'entity_test',
      'type' => $this->testConfig['storage']['type'],
      'settings' => $this->testConfig['storage']['settings'],
    ]);
    $this->fieldStorage->save();

    $this->field = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'bundle' => 'entity_test',
      'required' => TRUE,
    ]);
    $this->field->save();

    $display_options = [
      'type' => $this->testConfig['display']['type'],
      'label' => 'hidden',
      'settings' => $this->testConfig['display']['settings'],
    ];
    EntityViewDisplay::create([
      'targetEntityType' => $this->field->getTargetEntityTypeId(),
      'bundle' => $this->field->getTargetBundle(),
      'mode' => 'default',
      'status' => TRUE,
    ])->setComponent($this->fieldStorage->getName(), $display_options)
      ->save();
  }

  /**
   * Supported languages.
   */
  protected function getLanguages() {
    return array_keys($this->languageManager->getUnitedNationsLanguageList());
  }

  /**
   * Import translations.
   */
  protected function importTranslations() {
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
    $module_handler = $this->container->get('module_handler');
    $module_handler->loadInclude('locale', 'fetch.inc');

    // Use default options.
    $translationOptions = _locale_translation_default_update_options();

    // But only use local files, since we do not have to download all projects
    // enabled to check our own functionality.
    $translationOptions['use_remote'] = FALSE;
    $batch = locale_translation_batch_update_build([], $this->getLanguages(), $translationOptions);
    batch_set($batch);
    $batch = & batch_get();
    $batch['progressive'] = FALSE;
    batch_process();
  }

  /**
   * Set language.
   */
  protected function setLanguage(string $lang_code) {
    $this->stringTranslator->setDefaultLangcode($lang_code);

    $language = \Drupal::languageManager()->getLanguage($lang_code);
    $language_manager = \Drupal::languageManager();
    $language_manager->setConfigOverrideLanguage($language);

    // Invalidate the container.
    $this->config('system.site')->set('default_langcode', $lang_code)->save();
    $this->container->get('kernel')->rebuildContainer();
  }

  /**
   * {@inheritdoc}
   */
  protected function doTimezoneConversion(string $value, string $timezone) : string {
    $datetime_type = $this->fieldStorage->getSetting('datetime_type');
    if ($datetime_type === DateRangeItem::DATETIME_TYPE_DATE) {
      $storage_format = DateTimeItemInterface::DATE_STORAGE_FORMAT;
    }
    else {
      $storage_format = DateTimeItemInterface::DATETIME_STORAGE_FORMAT;
    }

    $date = new DrupalDateTime($value, $timezone);
    $storage_timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
    $user_timezone = new \DateTimeZone(date_default_timezone_get());

    if ($datetime_type === DateRangeItem::DATETIME_TYPE_ALLDAY) {
      $date->setTimeZone($user_timezone)->setTime(0, 0, 0);
    }

    return $date->setTimezone($storage_timezone)->format($storage_format);
  }

  /**
   * Render entity.
   */
  protected function renderIt($entity_type, $entity, $lang_code = 'en') {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    $build = $view_builder->view($entity, 'full', $lang_code);
    $output = \Drupal::service('renderer')->renderRoot($build);

    $output = strip_tags($output->__toString());
    $output = preg_replace('/\s+/', ' ', $output);

    return $output;
  }

}
