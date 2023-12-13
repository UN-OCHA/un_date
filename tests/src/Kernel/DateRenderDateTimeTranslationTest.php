<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRenderDateTimeTranslationTest extends FieldKernelTestBase {

  use UnDateTestTrait;

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
    'datetime',
    'un_date',
  ];

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
      'type' => 'datetime',
      'settings' => ['datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME],
    ]);
    $this->fieldStorage->save();

    $this->field = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'bundle' => 'entity_test',
      'required' => TRUE,
    ]);
    $this->field->save();

    $display_options = [
      'type' => 'un_date_datetime',
      'label' => 'hidden',
      'settings' => [
        'display_timezone' => FALSE,
        'month_format' => 'full',
      ],
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
    $batch =& batch_get();
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
   * Test datetimes in English.
   *
   * @dataProvider providerTestData
   */
  public function testDateTimeEnglish($expected, $date) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $date,
      ],
    ]);

    $this->assertStringContainsString($expected[$lang_code], (string) $this->renderIt('entity_test', $entity, $lang_code));
  }

  /**
   * Test datetimes in French.
   *
   * @dataProvider providerTestData
   */
  public function testDateTimeFrench($expected, $date) {
    $lang_code = 'fr';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $date,
      ],
    ]);

    $this->assertStringContainsString($expected[$lang_code], (string) $this->renderIt('entity_test', $entity, $lang_code));
  }

  /**
   * Test datetimes in Spanish.
   *
   * @dataProvider providerTestData
   */
  public function testDateTimeSpanish($expected, $date) {
    $lang_code = 'es';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $date,
      ],
    ]);

    $this->assertStringContainsString($expected[$lang_code], (string) $this->renderIt('entity_test', $entity, $lang_code));
  }

  /**
   * Test datetimes in Arabic.
   *
   * @dataProvider providerTestData
   */
  public function testDateTimeArabic($expected, $date) {
    $lang_code = 'ar';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $date,
      ],
    ]);

    $this->assertStringContainsString($expected[$lang_code], (string) $this->renderIt('entity_test', $entity, $lang_code));
  }

  /**
   * Test datetimes in Chinese.
   *
   * @dataProvider providerTestData
   */
  public function testDateTimeChinese($expected, $date) {
    $lang_code = 'zh-hans';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $date,
      ],
    ]);

    $this->assertStringContainsString($expected[$lang_code], (string) $this->renderIt('entity_test', $entity, $lang_code));
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'same' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 10.11 a.m.',
          'fr' => 'Date: 6 décembre 2023 10 h 11',
          'es' => 'Date: 6 Diciembre 2023 10.11 horas',
          'ar' => 'Date: 6 ديسمبر 2023' . ' 10.11',
          'zh-hans' => 'Date: 6 十二月 2023 10時11分',
        ],
        'date' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 10.11 p.m.',
          'fr' => 'Date: 6 décembre 2023 22 h 11',
          'es' => 'Date: 6 Diciembre 2023 22.11 horas',
          'ar' => 'Date: 6 ديسمبر 2023' . ' 22.11',
          'zh-hans' => 'Date: 6 十二月 2023 22時11分',
        ],
        'date' => '2023-12-06T22:11:12',
      ],
      '1am' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 1 a.m.',
          'fr' => 'Date: 6 décembre 2023 1 heure',
          'es' => 'Date: 6 Diciembre 2023 1 hora',
          'ar' => 'Date: 6 ديسمبر 2023' . ' 1',
          'zh-hans' => 'Date: 6 十二月 2023 1時正',
        ],
        'date' => '2023-12-06T01:00:00',
      ],
      'next_day' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 10 a.m.',
          'fr' => 'Date: 6 décembre 2023 10 heures',
          'es' => 'Date: 6 Diciembre 2023 10 horas',
          'ar' => 'Date: 6 ديسمبر 2023' . ' 10',
          'zh-hans' => 'Date: 6 十二月 2023 10時正',
        ],
        'date' => '2023-12-06T10:00:12',
      ],
      'all_day' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 midnight',
          'fr' => 'Date: 6 décembre 2023 minuit',
          'es' => 'Date: 6 Diciembre 2023 medianoche',
          'ar' => 'Date: 6 ديسمبر 2023' . ' منتصف الليل',
          'zh-hans' => 'Date: 6 十二月 2023 午夜',
        ],
        'date' => '2023-12-06T00:00:00',
      ],
      'all_day_multi' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 noon',
          'fr' => 'Date: 6 décembre 2023 midi',
          'es' => 'Date: 6 Diciembre 2023 mediodía',
          'ar' => 'Date: 6 ديسمبر 2023' . ' وقت الظهيرة',
          'zh-hans' => 'Date: 6 十二月 2023 中午',
        ],
        'date' => '2023-12-06T12:00:00',
      ],
    ];
  }

}
