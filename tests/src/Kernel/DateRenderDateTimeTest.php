<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRenderDateTimeTest extends UnDateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'datetime',
    'un_date',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->testConfig = [
      'timezone' => 'UTC',
      'storage' => [
        'type' => 'datetime',
        'settings' => ['datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME],
      ],
      'display' => [
        'type' => 'un_date_datetime',
        'settings' => [
          'display_timezone' => FALSE,
        ],
      ],
    ];

    parent::setUp();
  }

  /**
   * Test datetimes.
   *
   * @x-dataProvider providerTestData
   */
  public function testDateTime($expected = NULL, $date = NULL) {
    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestData(),
      );

      foreach ($data as $name => $row) {
        $expected = $row['expected'];
        $date = $row['date'];

        $field_name = $this->fieldStorage->getName();
        // Create an entity.
        $entity = EntityTest::create([
          'name' => $this->randomString(),
          $field_name => [
            'value' => $date,
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
          'value' => $date,
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
        'date' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'expected' => 'Date: 6.12.2023 10.11 a.m.',
        'date' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'expected' => 'Date: 6.12.2023 10 a.m.',
        'date' => '2023-12-06T10:00:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'expected' => 'Date: 6.12.2023 midnight',
        'date' => '2023-12-06T00:00:00',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'expected' => 'Date: 6.12.2023 midnight',
        'date' => '2023-12-06T00:00:00',
      ],
    ];
  }

}
