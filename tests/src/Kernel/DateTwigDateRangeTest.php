<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Render\RenderContext;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;
use Drupal\un_date\UnDateRange;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateTwigDateRangeTest extends FieldKernelTestBase {

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
   * Test twig filters on date ranges.
   *
   * @x-dataProvider providerTestDataDateRange
   * @x-dataProvider providerTestDataLocalTimes
   * @x-dataProvider providerTestDataTime
   * @x-dataProvider providerTestDataFilters
   * @x-dataProvider providerTestDataFiltersPart2
   */
  public function testTwigFiltersDateRange($template = NULL, $expected = NULL, $start = NULL, $end = NULL) {
    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestDataDateRange(),
        $this->providerTestDataFilters(),
        $this->providerTestDataFiltersPart2(),
      );

      foreach ($data as $name => $row) {
        $template = $row['template'];
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

        $variable = $entity->{$field_name};
        $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable), $name);

        $variable = $entity->{$field_name}->first();
        $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable), $name);
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

      $variable = $entity->{$field_name};
      $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));

      $variable = $entity->{$field_name}->first();
      $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test twig filters on date ranges.
   *
   * @x-dataProvider providerTestDataDateRange
   * @x-dataProvider providerTestDataLocalTimes
   * @x-dataProvider providerTestDataTime
   * @x-dataProvider providerTestDataFilters
   * @x-dataProvider providerTestDataFiltersPart2
   */
  public function testTwigFiltersDateRangeClass($template = NULL, $expected = NULL, $start = NULL, $end = NULL) {
    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestDataDateRange(),
        $this->providerTestDataLocalTimes(),
        $this->providerTestDataTime(),
        $this->providerTestDataFilters(),
        $this->providerTestDataFiltersPart2(),
      );

      foreach ($data as $name => $row) {
        $template = $row['template'];
        $expected = $row['expected'];
        $start = $row['start'];
        $end = $row['end'];

        $variable = new UnDateRange($start, $end);
        $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable), $name);
      }
    }
    else {
      $variable = new UnDateRange($start, $end);
      $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
    }
  }

  /**
   * Test twig filters on date ranges.
   *
   * @dataProvider providerTestDataFiltersPart3
   */
  public function testTwigFiltersWithDoubleInput($template, $expected, $start, $end) {
    $variable = new UnDateRange($start, $end);
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));

    $variable = new \DateTime($start);
    $var2 = new \DateTime($end);
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable, $var2));
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataDateRange() {
    return [
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m. — 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023 10.11 a.m. — 7.12.2023 11.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_daterange }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::all_day_multi' => [
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
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 10.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T10:11:12',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-06T11:11:12',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T11:11:12',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_timerange }}',
        'expected' => '',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::all_day_multi' => [
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
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12 UTC',
        'end' => '2023-12-06T10:11:12 UTC',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. — 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12 UTC',
        'end' => '2023-12-06T11:11:12 UTC',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 10.11 a.m. — 7.12.2023 11.11 a.m. UTC',
        'start' => '2023-12-06T10:11:12 UTC',
        'end' => '2023-12-07T11:11:12 UTC',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T00:00:00 UTC',
        'end' => '2023-12-06T23:59:59 UTC',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'template' => '{{ variable|un_daterange("numeric", "local_times") }}',
        'expected' => '6.12.2023 — 7.12.2023',
        'start' => '2023-12-06T00:00:00 UTC',
        'end' => '2023-12-07T23:59:59 UTC',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataTime() {
    return [
      __FUNCTION__ . '::same' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12 UTC',
        'end' => '2023-12-06T10:11:12 UTC',
      ],
      __FUNCTION__ . '::same_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12 UTC',
        'end' => '2023-12-06T11:11:12 UTC',
      ],
      __FUNCTION__ . '::next_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12 UTC',
        'end' => '2023-12-07T11:11:12 UTC',
      ],
      __FUNCTION__ . '::all_day' => [
        'template' => '{{ variable|un_time }}',
        'expected' => 'midnight',
        'start' => '2023-12-06T00:00:00 UTC',
        'end' => '2023-12-06T23:59:59 UTC',
      ],
      __FUNCTION__ . '::all_day_multi' => [
        'template' => '{{ variable|un_time }}',
        'expected' => 'midnight',
        'start' => '2023-12-06T00:00:00 UTC',
        'end' => '2023-12-07T23:59:59 UTC',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataFilters() {
    return [
      __FUNCTION__ . '::date' => [
        'template' => '{{ variable|un_date }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::time' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime' => [
        'template' => '{{ variable|un_datetime }}',
        'expected' => '6.12.2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::date_no_minutes' => [
        'template' => '{{ variable|un_date }}',
        'expected' => '6.12.2023',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::time_no_minutes' => [
        'template' => '{{ variable|un_time }}',
        'expected' => '10 a.m.',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_no_minutes' => [
        'template' => '{{ variable|un_datetime }}',
        'expected' => '6.12.2023 10 a.m.',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_all_day_0' => [
        'template' => '{{ un_is_all_day(variable) }}',
        'expected' => '',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_all_day_1' => [
        'template' => '{{ un_is_all_day(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T00:00:00',
        'end' => '2023-12-06T23:59:59',
      ],
      __FUNCTION__ . '::datetime_is_utc' => [
        'template' => '{{ un_is_utc(variable) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:00:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::date_full' => [
        'template' => '{{ variable|un_date("full") }}',
        'expected' => '6 December 2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_full' => [
        'template' => '{{ variable|un_datetime("full", false) }}',
        'expected' => '6 December 2023 10.11 a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::date_abbreviation' => [
        'template' => '{{ variable|un_date("abbreviation") }}',
        'expected' => '6 Dec. 2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::datetime_abbreviation' => [
        'template' => '{{ variable|un_datetime("abbreviation", false) }}',
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
      __FUNCTION__ . '::un_year' => [
        'template' => '{{ variable|un_year }}',
        'expected' => '2023',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_month' => [
        'template' => '{{ variable|un_month }}',
        'expected' => '12',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_month_full' => [
        'template' => '{{ variable|un_month_full }}',
        'expected' => 'December',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_month_abbr' => [
        'template' => '{{ variable|un_month_abbr }}',
        'expected' => 'Dec',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_day' => [
        'template' => '{{ variable|un_day }}',
        'expected' => '6',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_hour' => [
        'template' => '{{ variable|un_hour }}',
        'expected' => '10',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_minute' => [
        'template' => '{{ variable|un_minute }}',
        'expected' => '11',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_ampm' => [
        'template' => '{{ variable|un_ampm }}',
        'expected' => 'a.m.',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_is_all_day' => [
        'template' => '{{ un_is_all_day(variable) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_is_utc' => [
        'template' => '{{ un_is_utc(variable) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_is_rtl' => [
        'template' => '{{ un_is_rtl() }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_separator' => [
        'template' => '{{ un_separator() }}',
        'expected' => '—',
        'start' => '2023-12-06T10:11:12',
        'end' => '2023-12-07T23:59:59',
      ],
      __FUNCTION__ . '::un_duration' => [
        'template' => '{{ un_duration(variable, var2) }}',
        'expected' => '1 day, 2 hours',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_date' => [
        'template' => '{{ un_is_same_date(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_day' => [
        'template' => '{{ un_is_same_day(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_month' => [
        'template' => '{{ un_is_same_month(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_same_year' => [
        'template' => '{{ un_is_same_year(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      __FUNCTION__ . '::un_is_all_day' => [
        'template' => '{{ un_is_all_day(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
    ];
  }

  /**
   * Provide test examples.
   */
  public function providerTestDataFiltersPart3() {
    return [
      'un_duration' => [
        'template' => '{{ un_duration(variable, var2) }}',
        'expected' => '1 day, 2 hours',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      'un_is_same_date' => [
        'template' => '{{ un_is_same_date(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      'un_is_same_day' => [
        'template' => '{{ un_is_same_day(variable, var2) }}',
        'expected' => '',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      'un_is_same_month' => [
        'template' => '{{ un_is_same_month(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      'un_is_same_year' => [
        'template' => '{{ un_is_same_year(variable, var2) }}',
        'expected' => '1',
        'start' => '2023-12-06T10:11:00',
        'end' => '2023-12-07T12:11:00',
      ],
      'un_is_all_day' => [
        'template' => '{{ un_is_all_day(variable, var2) }}',
        'expected' => '',
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
  protected function renderObjectWithTwig($template, $variable, $var2 = NULL) {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    $context = new RenderContext();
    return $renderer->executeInRenderContext($context, function () use ($renderer, $template, $variable, $var2) {
      $elements = [
        '#type' => 'inline_template',
        '#template' => $template,
        '#context' => [
          'variable' => $variable,
          'var2' => $var2,
        ],
      ];
      return $renderer->render($elements);
    });
  }

}
