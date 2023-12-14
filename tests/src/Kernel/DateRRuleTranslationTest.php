<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;
use Drupal\un_date\Trait\UnDateTimeTrait;
use Drupal\un_date\UnRRuleHumanReadable;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRRuleTranslationTest extends FieldKernelTestBase {

  use UnDateTestTrait;
  use UnDateTimeTrait;

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
   * Test RRule in English.
   *
   * @dataProvider providerTestData
   */
  public function testRruleEnglish($rule, $result) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $parser = new UnRRuleHumanReadable($rule);
    $output = $parser->humanReadable([
      'use_intl' => TRUE,
      'locale' => $lang_code,
      'explicit_inifite' => TRUE,
      'dtstart' => FALSE,
      'include_start' => FALSE,
      'include_until' => TRUE,
      'date_formatter' => function ($date) {
        return $this->formatDate($date, 'numeric');
      },
    ]);

    self::assertSame($output, $result[$lang_code]);
  }

  /**
   * Test RRule in French.
   *
   * @dataProvider providerTestData
   */
  public function testRruleFrench($rule, $result) {
    $lang_code = 'fr';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $parser = new UnRRuleHumanReadable($rule);
    $output = $parser->humanReadable([
      'use_intl' => TRUE,
      'locale' => $lang_code,
      'explicit_inifite' => TRUE,
      'dtstart' => FALSE,
      'include_start' => FALSE,
      'include_until' => TRUE,
      'date_formatter' => function ($date) {
        return $this->formatDate($date, 'numeric');
      },
    ]);

    self::assertSame($result[$lang_code], $output);
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'FREQ=DAILY;UNTIL=20000131T140000Z;BYMONTH=1' => [
        'FREQ=DAILY;UNTIL=20000131T140000Z;BYMONTH=1',
        [
          'en' => 'Daily in January, until 31.01.2000',
          'fr' => 'Tous les jours en janvier, jusqu\'au 31.01.2000',
        ],
      ],
      'FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200' => [
        'FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200',
        [
          'en' => 'Every 3 years on the first, the 100th and the 200th days of the year, 10 times',
          'fr' => 'Tous les 3 ans les 1er, 100e et 200e jours de l\'année, 10 fois',
        ],
      ],
      'FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO' => [
        'FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO',
        [
          'en' => 'Yearly on Monday on week 20, indefinitely',
          'fr' => 'Tous les ans le lundi la semaine 20, indéfiniment',
        ],
      ],
      'FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13' => [
        'FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13',
        [
          'en' => 'Monthly on Friday on the 13th of the month, indefinitely',
          'fr' => 'Tous les mois le vendredi le 13 du mois, indéfiniment',
        ],
      ],
      'FREQ=MONTHLY;BYDAY=TU' => [
        'FREQ=MONTHLY;BYDAY=TU',
        [
          'en' => 'Monthly on Tuesday, indefinitely',
          'fr' => 'Tous les mois le mardi, indéfiniment',
        ],
      ],
      'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=1' => [
        'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=1',
        [
          'en' => 'Monthly on the first Tuesday, indefinitely',
          'fr' => 'Tous les mois, mais seulement le premier mardi, indéfiniment',
        ],
      ],
      'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=-1' => [
        'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=-1',
        [
          'en' => 'Monthly on the last Tuesday, indefinitely',
          'fr' => 'Tous les mois, mais seulement le dernier mardi, indéfiniment',
        ],
      ],
      'FREQ=DAILY;INTERVAL=1;COUNT=3' => [
        'FREQ=DAILY;INTERVAL=1;COUNT=3',
        [
          'en' => 'Daily, 3 times',
          'fr' => 'Tous les jours, 3 fois',
        ],
      ],
      'FREQ=DAILY;INTERVAL=1;COUNT=1' => [
        'FREQ=DAILY;INTERVAL=1;COUNT=1',
        [
          'en' => '',
          'fr' => '',
        ],
      ],
      'FREQ=WEEKLY;INTERVAL=1;COUNT=1' => [
        'FREQ=WEEKLY;INTERVAL=1;COUNT=1',
        [
          'en' => '',
          'fr' => '',
        ],
      ],
      'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE;COUNT=1' => [
        'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE;COUNT=1',
        [
          'en' => 'Weekly on Wednesday, one time',
          'fr' => 'Toutes les semaines le mercredi, une fois',
        ],
      ],
      'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE,SA;COUNT=1' => [
        'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE,SA;COUNT=1',
        [
          'en' => 'Weekly on Wednesday and Saturday, one time',
          'fr' => 'Toutes les semaines les mercredi et samedi, une fois',
        ],
      ],
      'FREQ=WEEKLY;INTERVAL=2;BYDAY=WE,SA;UNTIL=20220831T000000Z' => [
        'FREQ=WEEKLY;INTERVAL=2;BYDAY=WE,SA;UNTIL=20220831T000000Z',
        [
          'en' => 'Every other week on Wednesday and Saturday, until 31.08.2022',
          'fr' => 'Une semaine sur deux les mercredi et samedi, jusqu\'au 31.08.2022',
        ],
      ],
    ];
  }

}
