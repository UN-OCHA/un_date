<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Render\RenderContext;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests Twig with MarkupInterface objects.
 *
 * @group Theme
 */
class DateTwigDateTimeTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'datetime',
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
      'date' => [
        'expected' => '06.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => new \DateTime('2023-12-06T10:11:12'),
      ],
      'time' => [
        'expected' => '10.11 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => new \DateTime('2023-12-06T10:11:12'),
      ],
      'datetime' => [
        'expected' => '06.12.2023 10.11 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => new \DateTime('2023-12-06T10:11:12'),
      ],
      'date_no_minutes' => [
        'expected' => '06.12.2023',
        'template' => '{{ variable|un_date }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      'time_no_minutes' => [
        'expected' => '10 a.m.',
        'template' => '{{ variable|un_time }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      'datetime_no_minutes' => [
        'expected' => '06.12.2023 10 a.m.',
        'template' => '{{ variable|un_datetime }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      'datetime_all_day' => [
        'expected' => '',
        'template' => '{{ un_is_all_day(variable) }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
      ],
      'datetime_is_utc' => [
        'expected' => '1',
        'template' => '{{ un_is_utc(variable) }}',
        'date' => new \DateTime('2023-12-06T10:00:12'),
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
