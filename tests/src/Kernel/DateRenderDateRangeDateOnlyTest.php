<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRenderDateRangeDateOnlyTest extends FieldKernelTestBase {

  use UnDateTestTrait;

  /**
   * A field storage to use in this test class.
   *
   * @var \Drupal\field\Entity\FieldStorageConfig
   */
  protected $fieldStorage;

  /**
   * The field used in this test class.
   *
   * @var \Drupal\field\Entity\FieldConfig
   */
  protected $field;

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
    parent::setUp();

    // Set an explicit site timezone.
    $this->config('system.date')
      ->set('timezone.user.configurable', 0)
      ->set('timezone.default', 'UTC')
      ->save();

    // Add a datetime range field.
    $this->fieldStorage = FieldStorageConfig::create([
      'field_name' => mb_strtolower($this->randomMachineName()),
      'entity_type' => 'entity_test',
      'type' => 'daterange',
      'settings' => ['datetime_type' => DateRangeItem::DATETIME_TYPE_DATE],
    ]);
    $this->fieldStorage->save();

    $this->field = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'bundle' => 'entity_test',
      'required' => TRUE,
    ]);
    $this->field->save();

    $display_options = [
      'type' => 'un_date_daterange',
      'label' => 'hidden',
      'settings' => [
        'display_timezone' => FALSE,
      ],
    ];
    EntityViewDisplay::create([
      'targetEntityType' => $this->field->getTargetEntityTypeId(),
      'bundle' => $this->field->getTargetBundle(),
      'mode' => 'default',
      'status' => TRUE,
    ])->setComponent($this->fieldStorage->getName(), $display_options)
      ->save();
  }

  /**
   * Tests date ranges.
   *
   * @dataProvider providerTestData
   */
  public function testDateRange($expected, $start, $end) {
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

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'same' => [
        'expected' => 'Date: 06.12.2023',
        'start' => '2023-12-06',
        'end' => '2023-12-06',
      ],
      'same_day' => [
        'expected' => 'Date: 06.12.2023',
        'start' => '2023-12-06',
        'end' => '2023-12-06',
      ],
      'next_day' => [
        'expected' => 'Start date: 06.12.2023 End date: 07.12.2023',
        'start' => '2023-12-06',
        'end' => '2023-12-07',
      ],
      'all_day' => [
        'expected' => 'Date: 06.12.2023',
        'start' => '2023-12-06',
        'end' => '2023-12-06',
      ],
      'all_day_multi' => [
        'expected' => 'Start date: 06.12.2023 End date: 07.12.2023',
        'start' => '2023-12-06',
        'end' => '2023-12-07',
      ],
    ];
  }

}
