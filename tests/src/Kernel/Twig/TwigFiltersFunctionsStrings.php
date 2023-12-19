<?php

namespace Drupal\Tests\un_date\Kernel\Twig;

use Drupal\un_date\UnDateRange;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class TwigFiltersFunctionsStrings extends TwigBase {

  /**
   * Test filters.
   *
   * @x-dataProvider providerTestDataString
   * @x-dataProvider providerTestDataTimestamp
   */
  public function testTwigFilters($expected = NULL, $template = NULL, $variable = NULL) {
    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestDataString(),
        $this->providerTestDataTimestamp(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $template = $row['template'];
        $variable = $row['date'] ?? '';

        $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataString() {
    return [
      __FUNCTION__ . '::date' => [
        'expected' => '6.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::time' => [
        'expected' => '10.11 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::datetime' => [
        'expected' => '6.12.2023 10.11 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::date_no_minutes' => [
        'expected' => '6.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => '2023-12-06T10:00:12',
      ],
      __FUNCTION__ . '::time_no_minutes' => [
        'expected' => '10 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => '2023-12-06T10:00:12',
      ],
      __FUNCTION__ . '::datetime_no_minutes' => [
        'expected' => '6.12.2023 10 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => '2023-12-06T10:00:12',
      ],
      __FUNCTION__ . '::datetime_all_day' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => '2023-12-06T10:00:12',
      ],
      __FUNCTION__ . '::datetime_is_utc' => [
        'expected' => '1',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => '2023-12-06T10:00:12',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataTimestamp() {
    return [
      __FUNCTION__ . '::date' => [
        'expected' => '6.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => 1701857472,
      ],
      __FUNCTION__ . '::time' => [
        'expected' => '10.11 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => 1701857472,
      ],
      __FUNCTION__ . '::datetime' => [
        'expected' => '6.12.2023 10.11 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => 1701857472,
      ],
      __FUNCTION__ . '::date_no_minutes' => [
        'expected' => '6.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => 1701856812,
      ],
      __FUNCTION__ . '::time_no_minutes' => [
        'expected' => '10 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => 1701856812,
      ],
      __FUNCTION__ . '::datetime_no_minutes' => [
        'expected' => '6.12.2023 10 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => 1701856812,
      ],
      __FUNCTION__ . '::datetime_all_day' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => 1701856812,
      ],
      __FUNCTION__ . '::datetime_is_utc' => [
        'expected' => '1',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => 1701856812,
      ],
    ];
  }

}
