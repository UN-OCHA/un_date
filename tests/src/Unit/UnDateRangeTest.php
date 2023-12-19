<?php

namespace Drupal\Tests\un_date\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\un_date\UnDateRange;

/**
 * Tests UnDateRange.
 */
class UnDateRangeTest extends UnitTestCase {

  /**
   * End before start.
   */
  public function testEndBeforeStartString() {
    $this->expectException('InvalidArgumentException');
    new UnDateRange('2021-06-06T10:00:00', '2020-06-06T10:00:00');
  }

  /**
   * End before start.
   */
  public function testEndBeforeStartObject() {
    $this->expectException('InvalidArgumentException');
    new UnDateRange(new \DateTime('2021-06-06T10:00:00'), '2020-06-06T10:00:00');
  }

  /**
   * Different timezones.
   */
  public function testDifferentTz() {
    $a = new \DateTime('2021-06-06T10:00:00');
    $b = new \DateTime('2021-06-06T10:00:00+03:00');
    $this->expectException('InvalidArgumentException');
    new UnDateRange($a, $b);
  }

}
