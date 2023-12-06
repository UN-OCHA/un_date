<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Render\RenderContext;
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
class DateRangeFieldTwigTest extends FieldKernelTestBase {

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

    // Add a datetime range field.
    $this->fieldStorage = FieldStorageConfig::create([
      'field_name' => mb_strtolower($this->randomMachineName()),
      'entity_type' => 'entity_test',
      'type' => 'daterange',
      'settings' => ['datetime_type' => DateRangeItem::DATETIME_TYPE_DATETIME],
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
  public function testTwigFiltersDateRange($expected, $start, $end) {
    $template = '{{ variable|un_daterange }}';
    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $start,
        'end_value' => $end,
      ],
    ]);

    $variable = $entity->{$field_name};
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));

    $variable = $entity->{$field_name}->first();
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * @dataProvider providerTestDataTimes
   */
  public function testTwigFiltersDateRangeTimes($expected, $start, $end) {
    $template = '{{ variable|un_daterange_times }}';
    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $start,
        'end_value' => $end,
      ],
    ]);

    $variable = $entity->{$field_name};
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));

    $variable = $entity->{$field_name}->first();
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * @dataProvider providerTestData
   */
  public function testTwigFiltersDateRangeNamed($expected, $start, $end) {
    $template = '{{ variable|un_daterange_named }}';
    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $start,
        'end_value' => $end,
      ],
    ]);

    $variable = $entity->{$field_name};
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));

    $variable = $entity->{$field_name}->first();
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * @dataProvider providerTestDataLocalTimes
   */
  public function testTwigFiltersDateRangeNamedLocal($expected, $start, $end) {
    $template = '{{ variable|un_daterange_named("local_times") }}';
    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $start,
        'end_value' => $end,
      ],
    ]);

    $variable = $entity->{$field_name};
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));

    $variable = $entity->{$field_name}->first();
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * @dataProvider providerTestDataFilters
   */
  public function testTwigFiltersUnDate($expected, $template, $start, $end) {
    $field_name = $this->fieldStorage->getName();
    // Create an entity.
    $entity = EntityTest::create([
      'name' => $this->randomString(),
      $field_name => [
        'value' => $start,
        'end_value' => $end,
      ],
    ]);

    $variable = $entity->{$field_name};
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));

    $variable = $entity->{$field_name}->first();
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'same' => [
        'expected' => '06.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'expected' => '06.12.2023 10.11 a.m. — 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      'next_day' => [
        'expected' => '06.12.2023 10.11 a.m. — 07.12.2023 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      'all_day' => [
        'expected' => '06.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'all_day_multi' => [
        'expected' => '06.12.2023 — 07.12.2023',
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
      'same' => [
        'expected' => '10.11 a.m. — 10.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      'next_day' => [
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      'all_day' => [
        'expected' => '',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'all_day_multi' => [
        'expected' => '',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataTimes() {
    return [
      'same' => [
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'expected' => '10.11 a.m. — 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      'next_day' => [
        'expected' => '06.12.2023 10.11 a.m. — 07.12.2023 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      'all_day' => [
        'expected' => 'All day',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'all_day_multi' => [
        'expected' => '06.12.2023 — 07.12.2023',
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
      'date' => [
        'expected' => '06.12.2023',
        'template' => '{{ variable|un_date }}',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'time' => [
        'expected' => '10.11 a.m.',
        'template' => '{{ variable|un_time }}',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime' => [
        'expected' => '06.12.2023 10.11 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'date_no_minutes' => [
        'expected' => '06.12.2023',
        'template' => '{{ variable|un_date }}',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'time_no_minutes' => [
        'expected' => '10 a.m.',
        'template' => '{{ variable|un_time }}',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_no_minutes' => [
        'expected' => '06.12.2023 10 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_all_day_0' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_all_day_1' => [
        'expected' => '1',
        'template' => '{{ un_is_all_day(variable) }}',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'datetime_all_day_1' => [
        'expected' => '1',
        'template' => '{{ un_is_all_day(variable) }}',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_is_utc' => [
        'expected' => '1',
        'template' => '{{ un_is_utc(variable) }}',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * @return \Drupal\Component\Render\MarkupInterface
   *   The rendered HTML.
   */
  protected function renderObjectWithTwig($template, $variable) {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    $context = new RenderContext();
    return $renderer->executeInRenderContext($context, function () use ($renderer, $template, $variable) {
      $elements = [
        '#type' => 'inline_template',
        '#template' => $template,
        '#context' => ['variable' => $variable],
      ];
      return $renderer->render($elements);
    });
  }

}
