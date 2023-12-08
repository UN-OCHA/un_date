<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Render\RenderContext;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class DateTwigIllegalTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'un_date',
  ];

  /**
   * Test filters.
   *
   * @dataProvider providerTestData
   */
  public function testTwigFilters($expected, $template, $variable) {
    $this->assertSame($expected, (string) $this->renderObjectWithTwig($template, $variable));
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'un_date' => [
        'expected' => '',
        'template' => '{{ variable|un_date }}',
        'date' => new \stdClass(),
      ],
      'un_time' => [
        'expected' => '',
        'template' => '{{ variable|un_time }}',
        'date' => new \stdClass(),
      ],
      'un_datetime' => [
        'expected' => '',
        'template' => '{{ variable|un_datetime }}',
        'date' => new \stdClass(),
      ],
      'un_daterange' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange }}',
        'date' => new \stdClass(),
      ],
      'un_daterange_times' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_times }}',
        'date' => new \stdClass(),
      ],
      'un_daterange_named' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_named }}',
        'date' => new \stdClass(),
      ],
      'un_is_all_day' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_is_utc' => [
        'expected' => '',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => new \stdClass(),
      ],
      'un_date_string' => [
        'expected' => '',
        'template' => '{{ variable|un_date }}',
        'date' => 'test',
      ],
      'un_time_string' => [
        'expected' => '',
        'template' => '{{ variable|un_time }}',
        'date' => 'test',
      ],
      'un_datetime_string' => [
        'expected' => '',
        'template' => '{{ variable|un_datetime }}',
        'date' => 'test',
      ],
      'un_daterange_string' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange }}',
        'date' => 'test',
      ],
      'un_daterange_times_string' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_times }}',
        'date' => 'test',
      ],
      'un_daterange_named_string' => [
        'expected' => '',
        'template' => '{{ variable|un_daterange_named }}',
        'date' => 'test',
      ],
      'un_is_all_day_string' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => 'test',
      ],
      'un_is_utc_string' => [
        'expected' => '',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => 'test',
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
