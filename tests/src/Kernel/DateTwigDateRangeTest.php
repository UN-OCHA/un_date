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
class DateTwigDateRangeTest extends FieldKernelTestBase {

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
   * Test twgi filters on date ranges.
   *
   * @dataProvider providerTestDataDateRange
   * @dataProvider providerTestDataTimeRange
   * @dataProvider providerTestDataLocalTimes
   * @dataProvider providerTestDataTime
   * @dataProvider providerTestDataFilters
   * @dataProvider providerTestDataFiltersPart2
   */
  public function testTwigFiltersDateRange($template, $expected, $start, $end) {
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
  public function providerTestDataDateRange() {
    return [
      'same' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m. — 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      'next_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m. — 7.12.2023 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      'all_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'all_day_multi' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 — 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataTimeRange() {
    return [
      'same' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 10.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      'next_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      'all_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'all_day_multi' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '',
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
        'template' => '{{ variable|un_daterange("local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'template' => '{{ variable|un_daterange("local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      'next_day' => [
        'template' => '{{ variable|un_daterange("local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. — 7.12.2023 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      'all_day' => [
        'template' => '{{ variable|un_daterange("local_times") }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'all_day_multi' => [
        'template' => '{{ variable|un_daterange("local_times") }}',
        'expected' => '6.12.2023 — 7.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataTime() {
    return [
      'same' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      'next_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      'all_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => 'midnight',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'all_day_multi' => [
        'template' => '{{ variable|un_time }}',
        'expected' => 'midnight',
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
        'template' => '{{ variable|un_date }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'time' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime' => [
        'template' => '{{ variable|un_datetime }}',
        'expected' => '6.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'date_no_minutes' => [
        'template' => '{{ variable|un_date }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'time_no_minutes' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10 a.m.',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_no_minutes' => [
        'template' => '{{ variable|un_datetime }}',
        'expected' => '6.12.2023 10 a.m.',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_all_day_0' => [
        'template' => '{{ un_is_all_day(variable) }}',
        'expected' => '',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_all_day_1' => [
        'template' => '{{ un_is_all_day(variable) }}',
        'expected' => '1',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      'datetime_is_utc' => [
        'template' => '{{ un_is_utc(variable) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'date_full' => [
        'template' => '{{ variable|un_date("full") }}',
        'expected' => '6 December 2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_full' => [
        'template' => '{{ variable|un_datetime(false, "full") }}',
        'expected' => '6 December 2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'date_abbreviation' => [
        'template' => '{{ variable|un_date("abbreviation") }}',
        'expected' => '6 Dec. 2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'datetime_abbreviation' => [
        'template' => '{{ variable|un_datetime(false, "abbreviation") }}',
        'expected' => '6 Dec. 2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataFiltersPart2() {
    return [
      'un_year' => [
        'template' => '{{ variable|un_year }}',
        'expected' => '2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_month' => [
        'template' => '{{ variable|un_month }}',
        'expected' => '12',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_month_full' => [
        'template' => '{{ variable|un_month_full }}',
        'expected' => 'December',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_month_abbr' => [
        'template' => '{{ variable|un_month_abbr }}',
        'expected' => 'Dec',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_day' => [
        'template' => '{{ variable|un_day }}',
        'expected' => '6',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_hour' => [
        'template' => '{{ variable|un_hour }}',
        'expected' => '10',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_minute' => [
        'template' => '{{ variable|un_minute }}',
        'expected' => '11',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_ampm' => [
        'template' => '{{ variable|un_ampm }}',
        'expected' => 'a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_is_all_day' => [
        'template' => '{{ un_is_all_day(variable) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_is_utc' => [
        'template' => '{{ un_is_utc(variable) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_is_rtl' => [
        'template' => '{{ un_is_rtl() }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_separator' => [
        'template' => '{{ un_separator() }}',
        'expected' => '—',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      'un_duration' => [
        'template' => '{{ un_duration(variable) }}',
        'expected' => '1 day, 2 hours',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
    ];
  }

  /**
   * Render twig template.
   *
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
