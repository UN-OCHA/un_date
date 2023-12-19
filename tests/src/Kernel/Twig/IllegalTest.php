<?php

namespace Drupal\Tests\un_date\Kernel\Twig;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class IllegalTest extends TwigBase {

  /**
   * Test filters.
   *
   * @dataProvider providerTestData
   */
  public function testTwigFilters($expected, $template, $variable) {
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'un_date' => [
        'expected' => '',
        'template' => '{{ variable|un_date }}',
        'date' => new \stdClass(),
      ],
      'un_time' => [
        'expected' => '',
        'template' => '{{ variable|un_time }}',
        'date' => new \stdClass(),
      ],
      'un_datetime' => [
        'expected' => '',
        'template' => '{{ variable|un_datetime }}',
        'date' => new \stdClass(),
      ],
      'un_daterange' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange }}',
        'date' => new \stdClass(),
      ],
      'un_daterange_times' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_times }}',
        'date' => new \stdClass(),
      ],
      'un_daterange_named' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_named }}',
        'date' => new \stdClass(),
      ],
      'un_is_all_day' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_is_utc' => [
        'expected' => '',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_date_string' => [
        'expected' => '',
        'template' => '{{ variable|un_date }}',
        'date' => 'test',
      ],
      'un_time_string' => [
        'expected' => '',
        'template' => '{{ variable|un_time }}',
        'date' => 'test',
      ],
      'un_datetime_string' => [
        'expected' => '',
        'template' => '{{ variable|un_datetime }}',
        'date' => 'test',
      ],
      'un_daterange_string' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange }}',
        'date' => 'test',
      ],
      'un_daterange_times_string' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_times }}',
        'date' => 'test',
      ],
      'un_daterange_named_string' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_named }}',
        'date' => 'test',
      ],
      'un_is_all_day_string' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => 'test',
      ],
      'un_is_utc_string' => [
        'expected' => '',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => 'test',
      ],
      'un_year' => [
        'expected' => '',
        'template' => '{{ variable|un_year }}',
        'date' => 'test',
      ],
      'un_month' => [
        'expected' => '',
        'template' => '{{ variable|un_month }}',
        'date' => 'test',
      ],
      'un_month_full' => [
        'expected' => '',
        'template' => '{{ variable|un_month_full }}',
        'date' => 'test',
      ],
      'un_month_abbr' => [
        'expected' => '',
        'template' => '{{ variable|un_month_abbr }}',
        'date' => 'test',
      ],
      'un_day' => [
        'expected' => '',
        'template' => '{{ variable|un_day }}',
        'date' => 'test',
      ],
      'un_hour' => [
        'expected' => '',
        'template' => '{{ variable|un_hour }}',
        'date' => 'test',
      ],
      'un_minute' => [
        'expected' => '',
        'template' => '{{ variable|un_minute }}',
        'date' => 'test',
      ],
      'un_ampm' => [
        'expected' => '',
        'template' => '{{ variable|un_ampm }}',
        'date' => 'test',
      ],
      'un_duration' => [
        'expected' => '',
        'template' => '{{ un_duration(variable) }}',
        'date' => 'test',
      ],
      'un_year_object' => [
        'expected' => '',
        'template' => '{{ variable|un_year }}',
        'date' => new \stdClass(),
      ],
      'un_month_object' => [
        'expected' => '',
        'template' => '{{ variable|un_month }}',
        'date' => new \stdClass(),
      ],
      'un_month_full_object' => [
        'expected' => '',
        'template' => '{{ variable|un_month_full }}',
        'date' => new \stdClass(),
      ],
      'un_month_abbr_object' => [
        'expected' => '',
        'template' => '{{ variable|un_month_abbr }}',
        'date' => new \stdClass(),
      ],
      'un_day_object' => [
        'expected' => '',
        'template' => '{{ variable|un_day }}',
        'date' => new \stdClass(),
      ],
      'un_hour_object' => [
        'expected' => '',
        'template' => '{{ variable|un_hour }}',
        'date' => new \stdClass(),
      ],
      'un_minute_object' => [
        'expected' => '',
        'template' => '{{ variable|un_minute }}',
        'date' => new \stdClass(),
      ],
      'un_ampm_object' => [
        'expected' => '',
        'template' => '{{ variable|un_ampm }}',
        'date' => new \stdClass(),
      ],
      'un_is_all_day_object' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_is_utc_object' => [
        'expected' => '',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_duration_object' => [
        'expected' => '',
        'template' => '{{ un_duration(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_is_same_date' => [
        'expected' => '',
        'template' => '{{ un_is_same_date(variable) }}',
        'date' => 'test',
      ],
      'un_is_same_day' => [
        'expected' => '',
        'template' => '{{ un_is_same_day(variable) }}',
        'date' => 'test',
      ],
      'un_is_same_month' => [
        'expected' => '',
        'template' => '{{ un_is_same_month(variable) }}',
        'date' => 'test',
      ],
      'un_is_same_year' => [
        'expected' => '',
        'template' => '{{ un_is_same_year(variable) }}',
        'date' => 'test',
      ],
      'un_is_same_date_object' => [
        'expected' => '',
        'template' => '{{ un_is_same_date(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_is_same_day_object' => [
        'expected' => '',
        'template' => '{{ un_is_same_day(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_is_same_month_object' => [
        'expected' => '',
        'template' => '{{ un_is_same_month(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_is_same_year_object' => [
        'expected' => '',
        'template' => '{{ un_is_same_year(variable) }}',
        'date' => new \stdClass(),
      ],
    ];
  }

}
