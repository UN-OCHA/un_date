<?php

namespace Drupal\un_date;

/**
 * DateTimeZone with human friendly name.
 */
final class UnDateTimeZone extends \DateTimeZone {

  /**
   * {@inheritdoc}
   */
  public function __construct(string|\DateTimeZone $timezone) {
    if ($timezone instanceof \DateTimeZone) {
      parent::__construct($timezone->getName());
    }
    else {
      parent::__construct($timezone);
    }

    return $this;
  }

  /**
   * Return human friendly name.
   */
  public function getHumanFriendlyName() {
    $name = $this->getName();
    $name = str_replace('_', ' ', $name);

    return $name;
  }

}
