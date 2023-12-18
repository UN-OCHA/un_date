<?php

namespace Drupal\Tests\un_date\Kernel\Twig;

use Drupal\date_recur\DateRange;
use Drupal\un_date\UnDateRange;
use stdClass;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class UnDateTest extends TwigBase {


  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

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
   * Test filter in Spanish.
   *
   * @dataProvider providerValidTestDataNoFormat
   * @dataProvider providerValidTestDataNumeric
   * @dataProvider providerValidTestDataFull
   * @dataProvider providerValidTestDatAbbreviation
   * @dataProvider providerValidTestDateAbbr
   */
  public function testValidSpanish($expected, $variable, $format) {
    $lang_code = 'es';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $template = '{{ variable|un_date("' . $format . '") }}';
    $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Test filter in Arabic.
   *
   * @dataProvider providerValidTestDataNoFormat
   * @dataProvider providerValidTestDataNumeric
   * @dataProvider providerValidTestDataFull
   * @dataProvider providerValidTestDatAbbreviation
   * @dataProvider providerValidTestDateAbbr
   */
  public function testValidArabic($expected, $variable, $format) {
    $lang_code = 'ar';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    $template = '{{ variable|un_date("' . $format . '") }}';
    $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Test filter in Chinese.
   *
   * @dataProvider providerValidTestDataNoFormat
   * @dataProvider providerValidTestDataNumeric
   * @dataProvider providerValidTestDataFull
   * @dataProvider providerValidTestDatAbbreviation
   * @dataProvider providerValidTestDateAbbr
   */
  public function testValidChinese($expected, $variable, $format) {
    $lang_code = 'zh-hans';

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
          'zh-hans' => '6.12.2023',
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
          'zh-hans' => '6.12.2023',
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
          'zh-hans' => '6.12.2023',
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
          'zh-hans' => '6.12.2023',
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
          'zh-hans' => '6.12.2023',
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
          'zh-hans' => '6.12.2023',
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
          'zh-hans' => '6.12.2023',
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
          'zh-hans' => '6.12.2023',
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
          'fr' => '6 décembre 2023',
          'es' => '6 diciembre 2023',
          'ar' => '6 ديسمبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 December 2023',
          'fr' => '6 décembre 2023',
          'es' => '6 diciembre 2023',
          'ar' => '6 ديسمبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 December 2023',
          'fr' => '6 décembre 2023',
          'es' => '6 diciembre 2023',
          'ar' => '6 ديسمبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 December 2023',
          'fr' => '6 décembre 2023',
          'es' => '6 diciembre 2023',
          'ar' => '6 ديسمبر 2023',
          'zh-hans' => '6 十二月 2023',
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
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
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
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'abBr',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023',
          'fr' => '6 déc. 2023',
          'es' => '6 dic. 2023',
          'ar' => '6 دجنبر 2023',
          'zh-hans' => '6 十二月 2023',
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

}
