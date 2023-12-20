<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\entity_test\Entity\EntityTest;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRenderDateRangeTest extends UnDateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'datetime',
    'datetime_range',
    'un_date',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->testConfig = [
      'timezone' => 'UTC',
      'storage' => [
        'type' => 'daterange',
        'settings' => [
          'datetime_type' => DateRangeItem::DATETIME_TYPE_DATETIME,
        ],
      ],
      'display' => [
        'type' => 'un_date_daterange',
        'settings' => [
          'display_timezone' => FALSE,
        ],
      ],
    ];

    parent::setUp();
  }

  /**
   * Test date ranges.
   *
   * @x-dataProvider providerTestData
   */
  public function testDateRange($expected = NULL, $start = NULL, $end = NULL) {
    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestData(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $start = $row['start'];
        $end = $row['end'];

        $field_name = $this->fieldStorage->getName();
        // Create an entity.
        $entity = EntityTest::create([
          'name' => $this->randomString(),
          $field_name => [
            'value' => $start,
            'end_value' => $end,
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
          'value' => $start,
          'end_value' => $end,
        ],
      ]);

      $this->assertStringContainsString($expected, (string) $this->renderIt('entity_test', $entity));
    }
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      __FUNCTION__ . '::same' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m. — 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'expected' => 'Start date: 6.12.2023 10.11 a.m. End date: 7.12.2023 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::all_day_2' => [
        'expected' => 'Date: 6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T00:00:00',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'expected' => 'Start date: 6.12.2023 End date: 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

}
