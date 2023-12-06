<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime_range_timezone\Plugin\Field\FieldType\DateRangeTimezone;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRangeFieldDateRangeTimezoneRenderTest extends FieldKernelTestBase {

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
    'datetime_range_timezone',
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
      'type' => 'daterange_timezone',
      'settings' => ['datetime_type' => DateRangeTimezone::DATETIME_TYPE_DATETIME],
    ]);
    $this->fieldStorage->save();

    $this->field = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'bundle' => 'entity_test',
      'required' => TRUE,
    ]);
    $this->field->save();

    $display_options = [
      'type' => 'un_date_daterange_timezone',
      'label' => 'hidden',
      'settings' => [
        'display_timezone' => TRUE,
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
   * @dataProvider providerTestDataUtc
   */
  public function testDateRangeUtc($expected, $start, $end, $timezone) {
    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $start,
        'end_value' => $end,
        'timezone' => $timezone,
      ],
    ]);

    $this->assertStringContainsString($expected, (string) $this->renderIt('entity_test', $entity));
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataUtc() {
    return [
      'same' => [
        'expected' => 'Date: 06.12.2023 10.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
        'timezone' => 'UTC',
      ],
      'same_day' => [
        'expected' => 'Date: 06.12.2023 10.11 a.m. — 11.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
        'timezone' => 'UTC',
      ],
      'next_day' => [
        'expected' => 'Start date: 06.12.2023 10.11 a.m. (UTC) End date: 07.12.2023 11.11 a.m. (UTC)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
        'timezone' => 'UTC',
      ],
      'all_day' => [
        'expected' => 'Date: 06.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
        'timezone' => 'UTC',
      ],
      'all_day_2' => [
        'expected' => 'Date: 06.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T00:00:00',
        'timezone' => 'UTC',
      ],
      'all_day_multi' => [
        'expected' => 'Start date: 06.12.2023 End date: 07.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
        'timezone' => 'UTC',
      ],
    ];
  }

  /**
   * @dataProvider providerTestDataRandom
   */
  public function testDateRangeRandom($expected, $start, $end, $timezone) {
    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $start,
        'end_value' => $end,
        'timezone' => $timezone,
      ],
    ]);

    $this->assertStringContainsString($expected, (string) $this->renderIt('entity_test', $entity));
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataRandom() {
    return [
      'same' => [
        'expected' => 'Date: 06.12.2023 10.11 a.m. (Europe/Kyiv)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
        'timezone' => 'Europe/Kyiv',
      ],
      'same_day' => [
        'expected' => 'Date: 06.12.2023 10.11 a.m. — 11.11 a.m. (Europe/Amsterdam)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
        'timezone' => 'Europe/Amsterdam',
      ],
      'next_day' => [
        'expected' => 'Start date: 06.12.2023 10.11 a.m. (Asia/Tokyo) End date: 07.12.2023 11.11 a.m. (Asia/Tokyo)',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
        'timezone' => 'Asia/Tokyo',
      ],
      'all_day' => [
        'expected' => 'Date: 06.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
        'timezone' => 'Australia/Melbourne',
      ],
      'all_day_2' => [
        'expected' => 'Date: 06.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T00:00:00',
        'timezone' => 'Europe/Bucharest',
      ],
      'all_day_multi' => [
        'expected' => 'Start date: 06.12.2023 End date: 07.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
        'timezone' => 'Europe/London',
      ],
    ];
  }

  protected function renderIt($entity_type, $entity) {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    $build = $view_builder->view($entity);
    $output = \Drupal::service('renderer')->renderRoot($build);

    $output = strip_tags($output->__toString());
    $output = preg_replace('/\s+/', ' ', $output);

    return $output;
  }

}
