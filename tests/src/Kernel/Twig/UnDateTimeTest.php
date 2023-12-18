<?php

namespace Drupal\Tests\un_date\Kernel\Twig;

use Drupal\date_recur\DateRange;
use Drupal\un_date\UnDateRange;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class UnDateTimeTest extends TwigBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Set an explicit site timezone.
    $this->config('system.date')
      ->set('timezone.user.configurable', 0)
      ->set('timezone.default', 'Europe/Brussels')
      ->save();
  }

  /**
   * Test filter in English.
   *
   * @x-dataProvider providerValidTestDataNoFormat
   * @x-dataProvider providerValidTestDataNumeric
   * @x-dataProvider providerValidTestDataFull
   * @x-dataProvider providerValidTestDataAbbreviation
   * @x-dataProvider providerValidTestDataAbbr
   */
  public function testValidEnglish($expected = NULL, $variable = NULL, $format = NULL) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerValidTestDataNoFormat(),
        $this->providerValidTestDataNumeric(),
        $this->providerValidTestDataFull(),
        $this->providerValidTestDataAbbreviation(),
        $this->providerValidTestDataAbbr(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];
        $format = $row['format'];

        $template = '{{ variable|un_datetime("' . $format . '") }}';
        $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_datetime("' . $format . '") }}';
      $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test filter in French.
   *
   * @x-dataProvider providerValidTestDataNoFormat
   * @x-dataProvider providerValidTestDataNumeric
   * @x-dataProvider providerValidTestDataFull
   * @x-dataProvider providerValidTestDataAbbreviation
   * @x-dataProvider providerValidTestDataAbbr
   */
  public function testValidFrench($expected = NULL, $variable = NULL, $format = NULL) {
    $lang_code = 'fr';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerValidTestDataNoFormat(),
        $this->providerValidTestDataNumeric(),
        $this->providerValidTestDataFull(),
        $this->providerValidTestDataAbbreviation(),
        $this->providerValidTestDataAbbr(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];
        $format = $row['format'];

        $template = '{{ variable|un_datetime("' . $format . '") }}';
        $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_datetime("' . $format . '") }}';
      $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test filter in Spanish.
   *
   * @x-dataProvider providerValidTestDataNoFormat
   * @x-dataProvider providerValidTestDataNumeric
   * @x-dataProvider providerValidTestDataFull
   * @x-dataProvider providerValidTestDataAbbreviation
   * @x-dataProvider providerValidTestDataAbbr
   */
  public function testValidSpanish($expected = NULL, $variable = NULL, $format = NULL) {
    $lang_code = 'es';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerValidTestDataNoFormat(),
        $this->providerValidTestDataNumeric(),
        $this->providerValidTestDataFull(),
        $this->providerValidTestDataAbbreviation(),
        $this->providerValidTestDataAbbr(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];
        $format = $row['format'];

        $template = '{{ variable|un_datetime("' . $format . '") }}';
        $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_datetime("' . $format . '") }}';
      $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test filter in Arabic.
   *
   * @x-dataProvider providerValidTestDataNoFormat
   * @x-dataProvider providerValidTestDataNumeric
   * @x-dataProvider providerValidTestDataFull
   * @x-dataProvider providerValidTestDataAbbreviation
   * @x-dataProvider providerValidTestDataAbbr
   */
  public function testValidArabic($expected = NULL, $variable = NULL, $format = NULL) {
    $lang_code = 'ar';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerValidTestDataNoFormat(),
        $this->providerValidTestDataNumeric(),
        $this->providerValidTestDataFull(),
        $this->providerValidTestDataAbbreviation(),
        $this->providerValidTestDataAbbr(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];
        $format = $row['format'];

        $template = '{{ variable|un_datetime("' . $format . '") }}';
        $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_datetime("' . $format . '") }}';
      $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test filter in Chinese.
   *
   * @x-dataProvider providerValidTestDataNoFormat
   * @x-dataProvider providerValidTestDataNumeric
   * @x-dataProvider providerValidTestDataFull
   * @x-dataProvider providerValidTestDataAbbreviation
   * @x-dataProvider providerValidTestDataAbbr
   */
  public function testValidChinese($expected = NULL, $variable = NULL, $format = NULL) {
    $lang_code = 'zh-hans';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerValidTestDataNoFormat(),
        $this->providerValidTestDataNumeric(),
        $this->providerValidTestDataFull(),
        $this->providerValidTestDataAbbreviation(),
        $this->providerValidTestDataAbbr(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];
        $format = $row['format'];

        $template = '{{ variable|un_datetime("' . $format . '") }}';
        $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_datetime("' . $format . '") }}';
      $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test filters.
   *
   * @x-dataProvider providerInvalidTestDataNoFormat
   */
  public function testEnInvalid($expected = NULL, $variable = NULL) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerInvalidTestDataNoFormat(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];

        $template = '{{ variable|un_datetime }}';
        $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_datetime }}';
      $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Valid examples - no format.
   */
  public function providerValidTestDataNoFormat() {
    return [
      __FUNCTION__ . '::DateTime1' => [
        'expected' => [
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTime2' => [
        'expected' => [
          'en' => '6.12.2023 8.11 p.m.',
          'fr' => '6.12.2023 20 h 11',
          'es' => '6.12.2023 20.11 horas',
          'ar' => '6.12.2023 20.11',
          'zh-hans' => '6.12.2023 20時11分',
        ],
        'date' => new \DateTime('2023-12-06T20:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTime3' => [
        'expected' => [
          'en' => '6.12.2023 10 a.m.',
          'fr' => '6.12.2023 10 heures',
          'es' => '6.12.2023 10 horas',
          'ar' => '6.12.2023 10',
          'zh-hans' => '6.12.2023 10時正',
        ],
        'date' => new \DateTime('2023-12-06T10:00:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTime4' => [
        'expected' => [
          'en' => '6.12.2023 10 a.m.',
          'fr' => '6.12.2023 10 heures',
          'es' => '6.12.2023 10 horas',
          'ar' => '6.12.2023 10',
          'zh-hans' => '6.12.2023 10時正',
        ],
        'date' => new \DateTime('2023-12-06T10:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTime5' => [
        'expected' => [
          'en' => '6.12.2023 1 a.m.',
          'fr' => '6.12.2023 1 heure',
          'es' => '6.12.2023 1 hora',
          'ar' => '6.12.2023 1',
          'zh-hans' => '6.12.2023 1時正',
        ],
        'date' => new \DateTime('2023-12-06T01:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTime6' => [
        'expected' => [
          'en' => '6.12.2023 midnight',
          'fr' => '6.12.2023 minuit',
          'es' => '6.12.2023 medianoche',
          'ar' => '6.12.2023 منتصف الليل',
          'zh-hans' => '6.12.2023 午夜',
        ],
        'date' => new \DateTime('2023-12-06T00:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTime7' => [
        'expected' => [
          'en' => '6.12.2023 noon',
          'fr' => '6.12.2023 midi',
          'es' => '6.12.2023 mediodía',
          'ar' => '6.12.2023 وقت الظهيرة',
          'zh-hans' => '6.12.2023 中午',
        ],
        'date' => new \DateTime('2023-12-06T12:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
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
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6.12.2023 10.11 a.m.',
          'fr' => '6.12.2023 10 h 11',
          'es' => '6.12.2023 10.11 horas',
          'ar' => '6.12.2023 10.11',
          'zh-hans' => '6.12.2023 10時11分',
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
          'en' => '6 December 2023 10.11 a.m.',
          'fr' => '6 décembre 2023 10 h 11',
          'es' => '6 diciembre 2023 10.11 horas',
          'ar' => implode('', ['6 ديسمبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 December 2023 10.11 a.m.',
          'fr' => '6 décembre 2023 10 h 11',
          'es' => '6 diciembre 2023 10.11 horas',
          'ar' => implode('', ['6 ديسمبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 December 2023 10.11 a.m.',
          'fr' => '6 décembre 2023 10 h 11',
          'es' => '6 diciembre 2023 10.11 horas',
          'ar' => implode('', ['6 ديسمبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 December 2023 10.11 a.m.',
          'fr' => '6 décembre 2023 10 h 11',
          'es' => '6 diciembre 2023 10.11 horas',
          'ar' => implode('', ['6 ديسمبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => 'full',
      ],
    ];
  }

  /**
   * Valid examples - abbreviation.
   */
  public function providerValidTestDataAbbreviation() {
    return [
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'abbreviation',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => 'abbreviation',
      ],
    ];
  }

  /**
   * Valid examples - abbr.
   */
  public function providerValidTestDataAbbr() {
    return [
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => 'abBr',
      ],
      __FUNCTION__ . '::DateTimeImmutable' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
        ],
        'date' => new \DateTimeImmutable('2023-12-06T10:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 Dec. 2023 10.11 a.m.',
          'fr' => '6 déc. 2023 10 h 11',
          'es' => '6 dic. 2023 10.11 horas',
          'ar' => implode('', ['6 دجنبر 2023', ' 10.11']),
          'zh-hans' => '6 十二月 2023 10時11分',
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
        'date' => new \stdClass(),
        'format' => '',
      ],
    ];
  }

}
