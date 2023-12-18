<?php

namespace Drupal\Tests\un_date\Kernel\Twig;

use Drupal\Core\Render\RenderContext;
use Drupal\date_recur\DateRange;
use Drupal\KernelTests\KernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\un_date\UnDateRange;
use stdClass;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class UnDateTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'date_recur',
    'datetime_range',
    'datetime',
    'un_date',
    'locale',
    'language',
    'system',
  ];

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationManager
   */
  protected $stringTranslator;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('locale', [
      'locales_location',
      'locales_source',
      'locales_target',
      'locale_file',
    ]);

    // Ensure we are building a new Language object for each test.
    $this->installConfig(['language']);

    $this->stringTranslator = $this->container->get('string_translation');
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
   * Test filter in English.
   *
   * @dataProvider providerValidTestDataNoFormat
   * @dataProvider providerValidTestDataNumeric
   * @dataProvider providerValidTestDataFull
   * @dataProvider providerValidTestDatAbbreviation
   * @dataProvider providerValidTestDateAbbr
   */
  public function testValidEnglish($expected, $variable, $format) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $template = '{{ variable|un_date("' . $format . '") }}';
    $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Test filter in French.
   *
   * @dataProvider providerValidTestDataNoFormat
   * @dataProvider providerValidTestDataNumeric
   * @dataProvider providerValidTestDataFull
   * @dataProvider providerValidTestDatAbbreviation
   * @dataProvider providerValidTestDateAbbr
   */
  public function testValidFrench($expected, $variable, $format) {
    $lang_code = 'fr';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $template = '{{ variable|un_date("' . $format . '") }}';
    $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Test filters.
   *
   * @dataProvider providerInvalidTestDataNoFormat
   */
  public function testEnInvalid($expected, $variable) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $template = '{{ variable|un_date }}';
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Valid examples - no format.
   */
  public function providerValidTestDataNoFormat() {
    return [
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => '',
      ],
    ];
  }

  /**
   * Valid examples - numeric.
   */
  public function providerValidTestDataNumeric() {
    return [
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6.12.2023',
          'fr' => '6.12.2023',
          'es' => '6.12.2023',
          'ar' => '6.12.2023',
          'zh' => '6.12.2023',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => 'numeric',
      ],
    ];
  }

  /**
   * Valid examples - full.
   */
  public function providerValidTestDataFull() {
    return [
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '6 December 2023',
          'fr' => '6 December 2023',
          'es' => '6 December 2023',
          'ar' => '6 December 2023',
          'zh' => '6 December 2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 December 2023',
          'fr' => '6 December 2023',
          'es' => '6 December 2023',
          'ar' => '6 December 2023',
          'zh' => '6 December 2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 December 2023',
          'fr' => '6 December 2023',
          'es' => '6 December 2023',
          'ar' => '6 December 2023',
          'zh' => '6 December 2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 December 2023',
          'fr' => '6 December 2023',
          'es' => '6 December 2023',
          'ar' => '6 December 2023',
          'zh' => '6 December 2023',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => 'full',
      ],
    ];
  }

  /**
   * Valid examples - abbreviation.
   */
  public function providerValidTestDatAbbreviation() {
    return [
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => 'abbreviation',
      ],
    ];
  }

  /**
   * Valid examples - abbr.
   */
  public function providerValidTestDateAbbr() {
    return [
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'abBr',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 Dec. 2023',
          'es' => '6 Dec. 2023',
          'ar' => '6 Dec. 2023',
          'zh' => '6 Dec. 2023',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => 'abbr',
      ],
    ];
  }

  /**
   * Invalid examples.
   */
  public function providerInvalidTestDataNoFormat() {
    return [
      __FUNCTION__ . '::NULL' => [
        'expected' => '',
        'date' => NULL,
        'format' => '',
      ],
      __FUNCTION__ . '::FALSE' => [
        'expected' => '',
        'date' => FALSE,
        'format' => '',
      ],
      __FUNCTION__ . '::TRUE' => [
        'expected' => '',
        'date' => TRUE,
        'format' => '',
      ],
      __FUNCTION__ . '::string' => [
        'expected' => '',
        'date' => 'string',
        'format' => '',
      ],
      __FUNCTION__ . '::int' => [
        'expected' => '',
        'date' => 42,
        'format' => '',
      ],
      __FUNCTION__ . '::stdClass' => [
        'expected' => '',
        'date' => new stdClass(),
        'format' => '',
      ],
    ];
  }

  /**
   * Render twig template.
   *
   * @return \Drupal\Component\Render\MarkupInterface
   *   The rendered HTML.
   */
  protected function renderObjectWithTwig($template, $variable) {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    $context = new RenderContext();
    return $renderer->executeInRenderContext($context, function () use ($renderer, $template, $variable) {
      $elements = [
        '#type' => 'inline_template',
        '#template' => $template,
        '#context' => ['variable' => $variable],
      ];
      return $renderer->render($elements);
    });
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

}
