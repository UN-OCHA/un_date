<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRangeFieldDateTimeRenderTest extends FieldKernelTestBase {

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
      'type' => 'datetime',
      'settings' => ['datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME],
    ]);
    $this->fieldStorage->save();

    $this->field = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'bundle' => 'entity_test',
      'required' => TRUE,
    ]);
    $this->field->save();

    $display_options = [
      'type' => 'un_date_datetime',
      'label' => 'hidden',
      'settings' => [
        'display_timezone' => FALSE,
        'convert_to_utc' => FALSE,
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
   * @dataProvider providerTestData
   */
  public function testDateTime($expected, $date) {
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

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'same' => [
        'expected' => 'Date: 06.12.2023 10.11 a.m.',
        'date' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'expected' => 'Date: 06.12.2023 10.11 a.m.',
        'date' => '2023-12-06T10:11:12',
      ],
      'next_day' => [
        'expected' => 'Date: 06.12.2023 10 a.m.',
        'date' => '2023-12-06T10:00:12',
      ],
      'all_day' => [
        'expected' => 'Date: 06.12.2023 12 a.m.',
        'date' => '2023-12-06T00:00:00',
      ],
      'all_day_multi' => [
        'expected' => 'Date: 06.12.2023 12 a.m.',
        'date' => '2023-12-06T00:00:00',
      ],
    ];
  }

}
