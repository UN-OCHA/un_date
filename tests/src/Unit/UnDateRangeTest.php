<?php

namespace Drupal\Tests\un_date\Unit;

use DateTime;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Tests\UnitTestCase;
use Drupal\un_date\UnDateRange;

/**
 * Tests UnDateRange.
 */
class UnDateRangeTest extends UnitTestCase {
  public function testEndBeforeStartString() {
    $this->expectException('InvalidArgumentException');
    new UnDateRange('2021-06-06T10:00:00', '2020-06-06T10:00:00');
  }

  public function testEndBeforeStartObject() {
    $this->expectException('InvalidArgumentException');
    new UnDateRange(new DateTime('2021-06-06T10:00:00'), '2020-06-06T10:00:00');
  }

  public function testDifferentTz() {
    $a = new DateTime('2021-06-06T10:00:00');
    $b = new DateTime('2021-06-06T10:00:00+03:00');
    $this->expectException('InvalidArgumentException');
    new UnDateRange($a, $b);
  }

}
