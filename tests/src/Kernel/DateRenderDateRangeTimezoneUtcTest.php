<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\datetime_range_timezone\Plugin\Field\FieldType\DateRangeTimezone;
use Drupal\entity_test\Entity\EntityTest;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRenderDateRangeTimezoneUtcTest extends UnDateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'datetime',
    'datetime_range',
    'datetime_range_timezone',
    'un_date',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->testConfig = [
      'timezone' => 'UTC',
      'storage' => [
        'type' => 'daterange_timezone',
        'settings' => ['datetime_type' => DateRangeTimezone::DATETIME_TYPE_DATETIME],
      ],
      'display' => [
        'type' => 'un_date_daterange_timezone',
        'settings' => [
          'display_timezone' => TRUE,
        ],
      ],
    ];

    parent::setUp();
  }

  /**
   * Test UTC dates.
   *
   * @x-dataProvider providerTestDataUtc
   * @x-dataProvider providerTestDataRandom
   */
  public function testDateRangeUtc($expected = NULL, $start = NULL, $end = NULL, $timezone = NULL) {
    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestDataUtc(),
        $this->providerTestDataRandom(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $start = $row['start'];
        $end = $row['end'];
        $timezone = $row['timezone'];

        $field_name = $this->fieldStorage->getName();
        // Create an entity.
        $entity = EntityTest::create([
          'name' => $this->randomString(),
          $field_name => [
            'value' => $this->doTimezoneConversion($start, $timezone),
            'end_value' => $this->doTimezoneConversion($end, $timezone),
            'timezone' => $timezone,
          ],
        ]);

        $this->assertStringContainsString($expected, (string) $this->renderIt('entity_test', $entity), $name);
      }
    }
    else {
      $field_name = $this->fieldStorage->getName();
      // Create an entity.
      $entity = EntityTest::create([
        'name' => $this->randomString(),
        $field_name => [
          'value' => $this->doTimezoneConversion($start, $timezone),
          'end_value' => $this->doTimezoneConversion($end, $timezone),
          'timezone' => $timezone,
        ],
      ]);

      $this->assertStringContainsString($expected, (string) $this->renderIt('entity_test', $entity));
    }
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataUtc() {
    return [
      __FUNCTION__ . '::same' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::same_day' => [
        'expected' => 'Date: 6.12.2023 10.11 — 11.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::next_day' => [
        'expected' => 'Start date: 6.12.2023 10.11 a.m. (UTC) End date: 7.12.2023 11.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::all_day' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::all_day_2' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T00:00:00',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'expected' => 'Start date: 6.12.2023 End date: 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
        'timezone' => 'UTC',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataRandom() {
    return [
      __FUNCTION__ . '::same' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m. (Europe/Kyiv)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
        'timezone' => 'Europe/Kyiv',
      ],
      __FUNCTION__ . '::same_day' => [
        'expected' => 'Date: 6.12.2023 10.11 — 11.11 a.m. (Europe/Amsterdam)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
        'timezone' => 'Europe/Amsterdam',
      ],
      __FUNCTION__ . '::next_day' => [
        'expected' => 'Start date: 6.12.2023 10.11 a.m. (Asia/Tokyo) End date: 7.12.2023 11.11 a.m. (Asia/Tokyo)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
        'timezone' => 'Asia/Tokyo',
      ],
      __FUNCTION__ . '::all_day' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
        'timezone' => 'Australia/Melbourne',
      ],
      __FUNCTION__ . '::all_day_2' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T00:00:00',
        'timezone' => 'Europe/Bucharest',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'expected' => 'Start date: 6.12.2023 End date: 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
        'timezone' => 'Europe/London',
      ],
    ];
  }

}
