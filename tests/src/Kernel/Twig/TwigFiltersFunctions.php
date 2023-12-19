<?php

namespace Drupal\Tests\un_date\Kernel\Twig;

use Drupal\un_date\UnDateRange;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class TwigFiltersFunctions extends TwigBase {

  /**
   * Test filters.
   *
   * @x-dataProvider providerTestData
   */
  public function testTwigFilters($expected = NULL, $template = NULL, $variable = NULL) {
    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestData(),
        $this->providerTestDataDateRange(),
        $this->providerTestDataTimeRange(),
        $this->providerTestDataLocalTimes(),
        $this->providerTestDataTime(),
        $this->providerTestDataFilters(),
        $this->providerTestDataFiltersPart2(),
        $this->providerTestDataFiltersPart3(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $template = $row['template'];
        $variable = $row['date'] ?? '';
        $start = $row['start'] ?? '';
        $end = $row['end'] ?? '';

        if (empty($variable)) {
          $variable = new UnDateRange($start, $end);
        }

        $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Provide test examples.providerTestDataFiltersPart2
   */
  public function providerTestData() {
    return [
      __FUNCTION__ . '::date' => [
        'expected' => '6.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => new \DateTime('2023-12-06T10:11:12'),
      ],
      __FUNCTION__ . '::time' => [
        'expected' => '10.11 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => new \DateTime('2023-12-06T10:11:12'),
      ],
      __FUNCTION__ . '::datetime' => [
        'expected' => '6.12.2023 10.11 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => new \DateTime('2023-12-06T10:11:12'),
      ],
      __FUNCTION__ . '::date_no_minutes' => [
        'expected' => '6.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      __FUNCTION__ . '::time_no_minutes' => [
        'expected' => '10 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      __FUNCTION__ . '::datetime_no_minutes' => [
        'expected' => '6.12.2023 10 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      __FUNCTION__ . '::datetime_all_day' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      __FUNCTION__ . '::datetime_is_utc' => [
        'expected' => '1',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataDateRange() {
    return [
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m. — 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m. — 7.12.2023 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 — 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataTimeRange() {
    return [
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 10.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataLocalTimes() {
    return [
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. — 7.12.2023 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 — 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataTime() {
    return [
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => 'midnight',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'template' => '{{ variable|un_time }}',
        'expected' => 'midnight',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataFilters() {
    return [
      __FUNCTION__ . '::date' => [
        'template' => '{{ variable|un_date }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::time' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime' => [
        'template' => '{{ variable|un_datetime }}',
        'expected' => '6.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::date_no_minutes' => [
        'template' => '{{ variable|un_date }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::time_no_minutes' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10 a.m.',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_no_minutes' => [
        'template' => '{{ variable|un_datetime }}',
        'expected' => '6.12.2023 10 a.m.',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_all_day_0' => [
        'template' => '{{ un_is_all_day(variable) }}',
        'expected' => '',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_all_day_1' => [
        'template' => '{{ un_is_all_day(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::datetime_is_utc' => [
        'template' => '{{ un_is_utc(variable) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::date_full' => [
        'template' => '{{ variable|un_date("full") }}',
        'expected' => '6 December 2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_full' => [
        'template' => '{{ variable|un_datetime("full", false) }}',
        'expected' => '6 December 2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::date_abbreviation' => [
        'template' => '{{ variable|un_date("abbreviation") }}',
        'expected' => '6 Dec. 2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_abbreviation' => [
        'template' => '{{ variable|un_datetime("abbreviation", false) }}',
        'expected' => '6 Dec. 2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataFiltersPart2() {
    return [
      __FUNCTION__ . '::un_year' => [
        'template' => '{{ variable|un_year }}',
        'expected' => '2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_month' => [
        'template' => '{{ variable|un_month }}',
        'expected' => '12',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_month_full' => [
        'template' => '{{ variable|un_month_full }}',
        'expected' => 'December',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_month_abbr' => [
        'template' => '{{ variable|un_month_abbr }}',
        'expected' => 'Dec',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_day' => [
        'template' => '{{ variable|un_day }}',
        'expected' => '6',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_hour' => [
        'template' => '{{ variable|un_hour }}',
        'expected' => '10',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_minute' => [
        'template' => '{{ variable|un_minute }}',
        'expected' => '11',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_ampm' => [
        'template' => '{{ variable|un_ampm }}',
        'expected' => 'a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_is_all_day' => [
        'template' => '{{ un_is_all_day(variable) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_is_utc' => [
        'template' => '{{ un_is_utc(variable) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_is_rtl' => [
        'template' => '{{ un_is_rtl() }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_separator' => [
        'template' => '{{ un_separator() }}',
        'expected' => '—',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_duration' => [
        'template' => '{{ un_duration(variable, var2) }}',
        'expected' => '1 day, 2 hours',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_date' => [
        'template' => '{{ un_is_same_date(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_day' => [
        'template' => '{{ un_is_same_day(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_month' => [
        'template' => '{{ un_is_same_month(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_year' => [
        'template' => '{{ un_is_same_year(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_all_day2' => [
        'template' => '{{ un_is_all_day(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataFiltersPart3() {
    return [
      __FUNCTION__ . '::un_duration' => [
        'template' => '{{ un_duration(variable, var2) }}',
        'expected' => '1 day, 2 hours',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_date' => [
        'template' => '{{ un_is_same_date(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_day' => [
        'template' => '{{ un_is_same_day(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_month' => [
        'template' => '{{ un_is_same_month(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_year' => [
        'template' => '{{ un_is_same_year(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_all_day' => [
        'template' => '{{ un_is_all_day(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
    ];
  }

}
