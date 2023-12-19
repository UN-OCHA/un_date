<?php

namespace Drupal\Tests\un_date\Kernel\Twig;

use Drupal\date_recur\DateRange;
use Drupal\un_date\UnDateRange;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class UnDateRangeTimesTest extends TwigBase {

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
   * @x-dataProvider providerValidTestDataFullSameMonth
   * @x-dataProvider providerValidTestDataFullNextMonth
   * @x-dataProvider providerValidTestDataAbbrNextYear
   * @x-dataProvider providerValidTestDateAbbr
   */
  public function testValidEnglish($expected = NULL, $variable = NULL, $format = NULL) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerValidTestDataNoFormat(),
        $this->providerValidTestDataNumeric(),
        $this->providerValidTestDataFullSameMonth(),
        $this->providerValidTestDataFullNextMonth(),
        $this->providerValidTestDataAbbrNextYear(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];
        $format = $row['format'];

        $template = '{{ variable|un_daterange_times("' . $format . '") }}';
        $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_daterange_times("' . $format . '") }}';
      $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test filter in French.
   *
   * @x-dataProvider providerValidTestDataNoFormat
   * @x-dataProvider providerValidTestDataNumeric
   * @x-dataProvider providerValidTestDataFull
   * @x-dataProvider providerValidTestDatAbbreviation
   * @x-dataProvider providerValidTestDateAbbr
   */
  public function testValidFrench($expected = NULL, $variable = NULL, $format = NULL) {
    $lang_code = 'fr';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerValidTestDataNoFormat(),
        $this->providerValidTestDataNumeric(),
        $this->providerValidTestDataFullSameMonth(),
        $this->providerValidTestDataFullNextMonth(),
        $this->providerValidTestDataAbbrNextYear(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $variable = $row['date'];
        $format = $row['format'];

        $template = '{{ variable|un_daterange_times("' . $format . '") }}';
        $this->assertSame($expected[$lang_code], (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_daterange_times("' . $format . '") }}';
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

        $template = '{{ variable|un_daterange_times }}';
        $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $template = '{{ variable|un_daterange_times }}';
      $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Valid examples - no format.
   */
  public function providerValidTestDataNoFormat() {
    return [
      __FUNCTION__ . '::UnDateRange1' => [
        'expected' => [
          'en' => '10.11 a.m.',
          'fr' => '10 h 11',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange2' => [
        'expected' => [
          'en' => '8.11 p.m.',
          'fr' => '20 h 11',
        ],
        'date' => new UnDateRange('2023-12-06T20:11:12', '2023-12-06T20:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange3' => [
        'expected' => [
          'en' => '10 a.m.',
          'fr' => '10 heures',
        ],
        'date' => new UnDateRange('2023-12-06T10:00:12', '2023-12-06T10:00:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange4' => [
        'expected' => [
          'en' => '10 a.m.',
          'fr' => '10 heures',
        ],
        'date' => new UnDateRange('2023-12-06T10:00:00', '2023-12-06T10:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange5' => [
        'expected' => [
          'en' => '1 a.m.',
          'fr' => '1 heure',
        ],
        'date' => new UnDateRange('2023-12-06T01:00:00', '2023-12-06T01:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange6' => [
        'expected' => [
          'en' => 'midnight',
          'fr' => 'minuit',
        ],
        'date' => new UnDateRange('2023-12-06T00:00:00', '2023-12-06T00:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange7' => [
        'expected' => [
          'en' => 'noon',
          'fr' => 'midi',
        ],
        'date' => new UnDateRange('2023-12-06T12:00:00', '2023-12-06T12:00:00'),
        'format' => '',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '10.11 a.m.',
          'fr' => '10 h 11',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '10.11 a.m.',
          'fr' => '10 h 11',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T10:11:12')),
        'format' => '',
      ],
      __FUNCTION__ . '::DateRangeSameDay' => [
        'expected' => [
          'en' => '10.11 a.m. — 11.12 a.m.',
          'fr' => '10 h 11 — 11 h 12',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T11:12:12')),
        'format' => '',
      ],
      __FUNCTION__ . '::DateTime' => [
        'expected' => [
          'en' => '10.11 a.m.',
          'fr' => '10 h 11',
        ],
        'date' => new \DateTime('2023-12-06T10:11:12'),
        'format' => '',
      ],
      __FUNCTION__ . '::allDay' => [
        'expected' => [
          'en' => 'All day',
          'fr' => 'Toute la journée',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T00:00:00'), new \DateTime('2023-12-06T23:59:59')),
        'format' => '',
      ],
      __FUNCTION__ . '::allDay2' => [
        'expected' => [
          'en' => '6.12.2023 — 7.12.2023',
          'fr' => '6.12.2023 — 7.12.2023',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T00:00:00'), new \DateTime('2023-12-07T23:59:59')),
        'format' => '',
      ],
    ];
  }

  /**
   * Valid examples - numeric.
   */
  public function providerValidTestDataNumeric() {
    return [
      __FUNCTION__ . '::UnDateRange1' => [
        'expected' => [
          'en' => '10.11 a.m. — 2.11 p.m.',
          'fr' => '10 h 11 — 14 h 11',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T14:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange2' => [
        'expected' => [
          'en' => '8.11 p.m. — 9.11 p.m.',
          'fr' => '20 h 11 — 21 h 11',
        ],
        'date' => new UnDateRange('2023-12-06T20:11:12', '2023-12-06T21:11:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange3' => [
        'expected' => [
          'en' => '10 a.m. — 11 a.m.',
          'fr' => '10 heures — 11 heures',
        ],
        'date' => new UnDateRange('2023-12-06T10:00:12', '2023-12-06T11:00:12'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange4' => [
        'expected' => [
          'en' => '10 a.m. — 11 p.m.',
          'fr' => '10 heures — 23 heures',
        ],
        'date' => new UnDateRange('2023-12-06T10:00:00', '2023-12-06T23:00:00'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange5' => [
        'expected' => [
          'en' => '1 a.m. — 2 a.m.',
          'fr' => '1 heure — 2 heures',
        ],
        'date' => new UnDateRange('2023-12-06T01:00:00', '2023-12-06T02:00:00'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange6' => [
        'expected' => [
          'en' => 'midnight — 1 a.m.',
          'fr' => 'minuit — 1 heure',
        ],
        'date' => new UnDateRange('2023-12-06T00:00:00', '2023-12-06T01:00:00'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange7' => [
        'expected' => [
          'en' => 'noon — 2 p.m.',
          'fr' => 'midi — 14 heures',
        ],
        'date' => new UnDateRange('2023-12-06T12:00:00', '2023-12-06T14:00:00'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '10.11 a.m. — 10.11 a.m.',
          'fr' => '10 h 11 — 10 h 11',
        ],
        'date' => new UnDateRange('2023-12-06T10:11:12', '2023-12-06T10:11:13'),
        'format' => 'numeric',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '10.11 a.m. — 11.11 a.m.',
          'fr' => '10 h 11 — 11 h 11',
        ],
        'date' => new DateRange(new \DateTime('2023-12-06T10:11:12'), new \DateTime('2023-12-06T11:11:12')),
        'format' => 'numeric',
      ],
    ];
  }

  /**
   * Valid examples - full - same month.
   */
  public function providerValidTestDataFullSameMonth() {
    return [
      __FUNCTION__ . '::UnDateRange1' => [
        'expected' => [
          'en' => '6 November 2023 10.11 a.m. — 7 November 2023 2.11 p.m.',
          'fr' => '6 novembre 2023 10 h 11 — 7 novembre 2023 14 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T10:11:12', '2023-11-07T14:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange2' => [
        'expected' => [
          'en' => '6 November 2023 8.11 p.m. — 7 November 2023 9.11 p.m.',
          'fr' => '6 novembre 2023 20 h 11 — 7 novembre 2023 21 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T20:11:12', '2023-11-07T21:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange3' => [
        'expected' => [
          'en' => '6 November 2023 10 a.m. — 7 November 2023 11 a.m.',
          'fr' => '6 novembre 2023 10 heures — 7 novembre 2023 11 heures',
        ],
        'date' => new UnDateRange('2023-11-06T10:00:12', '2023-11-07T11:00:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange4' => [
        'expected' => [
          'en' => '6 November 2023 10 a.m. — 7 November 2023 11 p.m.',
          'fr' => '6 novembre 2023 10 heures — 7 novembre 2023 23 heures',
        ],
        'date' => new UnDateRange('2023-11-06T10:00:00', '2023-11-07T23:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange5' => [
        'expected' => [
          'en' => '6 November 2023 1 a.m. — 7 November 2023 2 a.m.',
          'fr' => '6 novembre 2023 1 heure — 7 novembre 2023 2 heures',
        ],
        'date' => new UnDateRange('2023-11-06T01:00:00', '2023-11-07T02:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange6' => [
        'expected' => [
          'en' => '6 November 2023 midnight — 7 November 2023 1 a.m.',
          'fr' => '6 novembre 2023 minuit — 7 novembre 2023 1 heure',
        ],
        'date' => new UnDateRange('2023-11-06T00:00:00', '2023-11-07T01:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange7' => [
        'expected' => [
          'en' => '6 November 2023 noon — 7 November 2023 2 p.m.',
          'fr' => '6 novembre 2023 midi — 7 novembre 2023 14 heures',
        ],
        'date' => new UnDateRange('2023-11-06T12:00:00', '2023-11-07T14:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 November 2023 10.11 a.m. — 7 November 2023 10.11 a.m.',
          'fr' => '6 novembre 2023 10 h 11 — 7 novembre 2023 10 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T10:11:12', '2023-11-07T10:11:13'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 November 2023 10.11 a.m. — 7 November 2023 11.11 a.m.',
          'fr' => '6 novembre 2023 10 h 11 — 7 novembre 2023 11 h 11',
        ],
        'date' => new DateRange(new \DateTime('2023-11-06T10:11:12'), new \DateTime('2023-11-07T11:11:12')),
        'format' => 'full',
      ],
    ];
  }

  /**
   * Valid examples - full - next month.
   */
  public function providerValidTestDataFullNextMonth() {
    return [
      __FUNCTION__ . '::UnDateRange1' => [
        'expected' => [
          'en' => '6 November 2023 10.11 a.m. — 7 December 2023 2.11 p.m.',
          'fr' => '6 novembre 2023 10 h 11 — 7 décembre 2023 14 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T10:11:12', '2023-12-07T14:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange2' => [
        'expected' => [
          'en' => '6 November 2023 8.11 p.m. — 7 December 2023 9.11 p.m.',
          'fr' => '6 novembre 2023 20 h 11 — 7 décembre 2023 21 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T20:11:12', '2023-12-07T21:11:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange3' => [
        'expected' => [
          'en' => '6 November 2023 10 a.m. — 7 December 2023 11 a.m.',
          'fr' => '6 novembre 2023 10 heures — 7 décembre 2023 11 heures',
        ],
        'date' => new UnDateRange('2023-11-06T10:00:12', '2023-12-07T11:00:12'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange4' => [
        'expected' => [
          'en' => '6 November 2023 10 a.m. — 7 December 2023 11 p.m.',
          'fr' => '6 novembre 2023 10 heures — 7 décembre 2023 23 heures',
        ],
        'date' => new UnDateRange('2023-11-06T10:00:00', '2023-12-07T23:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange5' => [
        'expected' => [
          'en' => '6 November 2023 1 a.m. — 7 December 2023 2 a.m.',
          'fr' => '6 novembre 2023 1 heure — 7 décembre 2023 2 heures',
        ],
        'date' => new UnDateRange('2023-11-06T01:00:00', '2023-12-07T02:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange6' => [
        'expected' => [
          'en' => '6 November 2023 midnight — 7 December 2023 1 a.m.',
          'fr' => '6 novembre 2023 minuit — 7 décembre 2023 1 heure',
        ],
        'date' => new UnDateRange('2023-11-06T00:00:00', '2023-12-07T01:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange7' => [
        'expected' => [
          'en' => '6 November 2023 noon — 7 December 2023 2 p.m.',
          'fr' => '6 novembre 2023 midi — 7 décembre 2023 14 heures',
        ],
        'date' => new UnDateRange('2023-11-06T12:00:00', '2023-12-07T14:00:00'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 November 2023 10.11 a.m. — 7 December 2023 10.11 a.m.',
          'fr' => '6 novembre 2023 10 h 11 — 7 décembre 2023 10 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T10:11:12', '2023-12-07T10:11:13'),
        'format' => 'full',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 November 2023 10.11 a.m. — 7 December 2023 11.11 a.m.',
          'fr' => '6 novembre 2023 10 h 11 — 7 décembre 2023 11 h 11',
        ],
        'date' => new DateRange(new \DateTime('2023-11-06T10:11:12'), new \DateTime('2023-12-07T11:11:12')),
        'format' => 'full',
      ],
    ];
  }

  /**
   * Valid examples - abbreviation - next year.
   */
  public function providerValidTestDataAbbrNextYear() {
    return [
      __FUNCTION__ . '::UnDateRange1' => [
        'expected' => [
          'en' => '6 Nov. 2023 10.11 a.m. — 7 Dec. 2024 2.11 p.m.',
          'fr' => '6 nov. 2023 10 h 11 — 7 déc. 2024 14 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T10:11:12', '2024-12-07T14:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange2' => [
        'expected' => [
          'en' => '6 Nov. 2023 8.11 p.m. — 7 Dec. 2024 9.11 p.m.',
          'fr' => '6 nov. 2023 20 h 11 — 7 déc. 2024 21 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T20:11:12', '2024-12-07T21:11:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange3' => [
        'expected' => [
          'en' => '6 Nov. 2023 10 a.m. — 7 Dec. 2024 11 a.m.',
          'fr' => '6 nov. 2023 10 heures — 7 déc. 2024 11 heures',
        ],
        'date' => new UnDateRange('2023-11-06T10:00:12', '2024-12-07T11:00:12'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange4' => [
        'expected' => [
          'en' => '6 Nov. 2023 10 a.m. — 7 Dec. 2024 11 p.m.',
          'fr' => '6 nov. 2023 10 heures — 7 déc. 2024 23 heures',
        ],
        'date' => new UnDateRange('2023-11-06T10:00:00', '2024-12-07T23:00:00'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange5' => [
        'expected' => [
          'en' => '6 Nov. 2023 1 a.m. — 7 Dec. 2024 2 a.m.',
          'fr' => '6 nov. 2023 1 heure — 7 déc. 2024 2 heures',
        ],
        'date' => new UnDateRange('2023-11-06T01:00:00', '2024-12-07T02:00:00'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange6' => [
        'expected' => [
          'en' => '6 Nov. 2023 midnight — 7 Dec. 2024 1 a.m.',
          'fr' => '6 nov. 2023 minuit — 7 déc. 2024 1 heure',
        ],
        'date' => new UnDateRange('2023-11-06T00:00:00', '2024-12-07T01:00:00'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange7' => [
        'expected' => [
          'en' => '6 Nov. 2023 noon — 7 Dec. 2024 2 p.m.',
          'fr' => '6 nov. 2023 midi — 7 déc. 2024 14 heures',
        ],
        'date' => new UnDateRange('2023-11-06T12:00:00', '2024-12-07T14:00:00'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::UnDateRange' => [
        'expected' => [
          'en' => '6 Nov. 2023 10.11 a.m. — 7 Dec. 2024 10.11 a.m.',
          'fr' => '6 nov. 2023 10 h 11 — 7 déc. 2024 10 h 11',
        ],
        'date' => new UnDateRange('2023-11-06T10:11:12', '2024-12-07T10:11:13'),
        'format' => 'abbr',
      ],
      __FUNCTION__ . '::DateRange' => [
        'expected' => [
          'en' => '6 Nov. 2023 10.11 a.m. — 7 Dec. 2024 11.11 a.m.',
          'fr' => '6 nov. 2023 10 h 11 — 7 déc. 2024 11 h 11',
        ],
        'date' => new DateRange(new \DateTime('2023-11-06T10:11:12'), new \DateTime('2024-12-07T11:11:12')),
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
      __FUNCTION__ . '::stdClass' => [
        'expected' => '',
        'date' => new \stdClass(),
        'format' => '',
      ],
    ];
  }

}
