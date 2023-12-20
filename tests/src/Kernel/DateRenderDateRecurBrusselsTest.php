<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\entity_test\Entity\EntityTest;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 * @phpcs:disable DrupalPractice.Objects.StrictSchemaDisabled.StrictConfigSchema
 */
class DateRenderDateRecurBrusselsTest extends UnDateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'date_recur',
    'datetime_range',
    'datetime',
    'un_date',
  ];

  /**
   * {@inheritdoc}
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->testConfig = [
      'timezone' => 'Europe/Brussels',
      'storage' => [
        'type' => 'date_recur',
        'settings' => [],
      ],
      'display' => [
        'type' => 'un_data_date_recur_basic',
        'settings' => [
          'display_timezone' => TRUE,
        ]
      ],
    ];

    parent::setUp();
  }

  /**
   * Test with UTC timezone.
   *
   * @x-dataProvider providerTestDataUtc
   * @x-dataProvider providerTestDataRandom
   */
  public function testDateRangeUtc($expected = NULL, $start = NULL, $end = NULL, $timezone = NULL, $rrule = '') {
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
        $rrule = $row['rrule'] ?? '';

        $field_name = $this->fieldStorage->getName();
        // Create an entity.
        $entity = EntityTest::create([
          'name' => $this->randomString(),
          $field_name => [
            'value' => $this->doTimezoneConversion($start, $timezone),
            'end_value' => $this->doTimezoneConversion($end, $timezone),
            'timezone' => $timezone,
            'rrule' => $rrule,
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
          'rrule' => $rrule,
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
      __FUNCTION__ . '::same_utc' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::same_day_utc' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m. — 11.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::next_day_utc' => [
        'expected' => 'Start date: 6.12.2023 10.11 a.m. (UTC) End date: 7.12.2023 11.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::all_day_utc' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::all_day_2_utc' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T00:00:00',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::all_day_multi_utc' => [
        'expected' => 'Start date: 6.12.2023 End date: 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
        'timezone' => 'UTC',
      ],
      __FUNCTION__ . '::same_brussels' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m. (Europe/Brussels)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
        'timezone' => 'Europe/Brussels',
      ],
      __FUNCTION__ . '::same_day_brussels' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m. — 11.11 a.m. (Europe/Brussels)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
        'timezone' => 'Europe/Brussels',
      ],
      __FUNCTION__ . '::next_day_brussels' => [
        'expected' => 'Start date: 6.12.2023 10.11 a.m. (Europe/Brussels) End date: 7.12.2023 11.11 a.m. (Europe/Brussels)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
        'timezone' => 'Europe/Brussels',
      ],
      __FUNCTION__ . '::all_day_brussels' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
        'timezone' => 'Europe/Brussels',
      ],
      __FUNCTION__ . '::all_day_2_brussels' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T00:00:00',
        'timezone' => 'Europe/Brussels',
      ],
      __FUNCTION__ . '::all_day_multi_brussels' => [
        'expected' => 'Start date: 6.12.2023 End date: 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
        'timezone' => 'Europe/Brussels',
      ],
      __FUNCTION__ . '::same_brussels_rrule' => [
        'expected' => 'Date: 6.12.2038 10.11 a.m. (Europe/Brussels)',
        'start' => '2038-12-06T10:11:12',
        'end' => '2038-12-06T10:11:12',
        'timezone' => 'Europe/Brussels',
        'rrule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO',
      ],
      __FUNCTION__ . '::same_day_brussels_rrule' => [
        'expected' => 'Date: 6.12.2038 10.11 a.m. — 11.11 a.m. (Europe/Brussels)',
        'start' => '2038-12-06T10:11:12',
        'end' => '2038-12-06T11:11:12',
        'timezone' => 'Europe/Brussels',
        'rrule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO',
      ],
      __FUNCTION__ . '::next_day_brussels_rrule' => [
        'expected' => 'Start date: 6.12.2038 10.11 a.m. (Europe/Brussels) End date: 7.12.2038 11.11 a.m. (Europe/Brussels)',
        'start' => '2038-12-06T10:11:12',
        'end' => '2038-12-07T11:11:12',
        'timezone' => 'Europe/Brussels',
        'rrule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO',
      ],
      __FUNCTION__ . '::all_day_brussels_rrule' => [
        'expected' => 'Date: 6.12.2038',
        'start' => '2038-12-06T00:00:00',
        'end' => '2038-12-06T23:59:59',
        'timezone' => 'Europe/Brussels',
        'rrule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO',
      ],
      __FUNCTION__ . '::all_day_2_brussels_rrule' => [
        'expected' => 'Date: 6.12.2038',
        'start' => '2038-12-06T00:00:00',
        'end' => '2038-12-06T00:00:00',
        'timezone' => 'Europe/Brussels',
        'rrule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO',
      ],
      __FUNCTION__ . '::all_day_multi_brussels_rrule' => [
        'expected' => 'Start date: 6.12.2038 End date: 7.12.2038',
        'start' => '2038-12-06T00:00:00',
        'end' => '2038-12-07T23:59:59',
        'timezone' => 'Europe/Brussels',
        'rrule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO',
      ],
      __FUNCTION__ . '::all_day_multi_brussels_rrule_past' => [
        'expected' => 'Start date: 10.12.2018 End date: 11.12.2018',
        'start' => '2018-12-06T00:00:00',
        'end' => '2018-12-07T23:59:59',
        'timezone' => 'Europe/Brussels',
        'rrule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO;COUNT=1',
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
        'expected' => 'Date: 6.12.2023 10.11 a.m. — 11.11 a.m. (Europe/Amsterdam)',
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
