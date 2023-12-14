<?php

namespace Drupal\Tests\un_date\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\UnitTestCase;
use Drupal\un_date\Trait\UnDateTimeTrait;
use Drupal\un_date\UnRRuleHumanReadable;

/**
 * Tests RRule text output.
 *
 * @coversDefaultClass \Drupal\un_date\UnRRuleHumanReadable
 */
class UnRRuleHumanReadableTest extends UnitTestCase {

  use UnDateTimeTrait;

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);
    $container->set('string_translation', self::getStringTranslationStub());
  }

  /**
   * Tests RRule text output.
   *
   * @covers ::humanReadable
   *
   * @dataProvider providerTests
   */
  public function testHumanText($rule, $result) {
    $parser = new UnRRuleHumanReadable($rule);
    $output = $parser->humanReadable([
      'use_intl' => FALSE,
      'explicit_inifite' => TRUE,
      'dtstart' => FALSE,
      'include_start' => FALSE,
      'include_until' => TRUE,
      'date_formatter' => function ($date) {
        return $this->formatDate($date);
      },
    ]);

    self::assertSame($output, $result);
  }

  /**
   * Provides test strings.
   */
  public function providerTests() {
    $tests = [
      'FREQ=MONTHLY;BYDAY=TU' => [
        'FREQ=MONTHLY;BYDAY=TU',
        'Monthly on Tuesday, indefinitely',
      ],
      'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=1' => [
        'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=1',
        'Monthly on first Tuesday, indefinitely',
      ],
      'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=-1' => [
        'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=-1',
        'Monthly on last Tuesday, indefinitely',
      ],
      'FREQ=DAILY;INTERVAL=1;COUNT=3' => [
        'FREQ=DAILY;INTERVAL=1;COUNT=3',
        'Daily, 3 times',
      ],
      'FREQ=MONTHLY;BYDAY=TU,WE,TH;BYSETPOS=-6,-5,-4,4,5,6,10,11,12' => [
        'FREQ=MONTHLY;BYDAY=TU,WE,TH;BYSETPOS=-6,-5,-4,4,5,6,10,11,12',
        'Monthly on second, fourth and 2nd to last Tuesday, Wednesday and Thursday, indefinitely',
      ],
      'FREQ=DAILY;INTERVAL=1;COUNT=1' => [
        'FREQ=DAILY;INTERVAL=1;COUNT=1',
        '',
      ],
      'FREQ=WEEKLY;INTERVAL=1;COUNT=1' => [
        'FREQ=WEEKLY;INTERVAL=1;COUNT=1',
        '',
      ],
      'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE;COUNT=1' => [
        'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE;COUNT=1',
        'Weekly on Wednesday, one time',
      ],
      'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE,SA;COUNT=1' => [
        'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE,SA;COUNT=1',
        'Weekly on Wednesday and Saturday, one time',
      ],
      'FREQ=WEEKLY;INTERVAL=2;BYDAY=WE,SA;UNTIL=20220831T000000Z' => [
        'FREQ=WEEKLY;INTERVAL=2;BYDAY=WE,SA;UNTIL=20220831T000000Z',
        'Every other week on Wednesday and Saturday, until 31.08.2022',
      ],
    ];

    return $tests;
  }

}
