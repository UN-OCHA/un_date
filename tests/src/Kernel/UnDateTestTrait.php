<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;

trait UnDateTestTrait {
  /**
   * {@inheritdoc}
   */
  protected function doTimezoneConversion(string $value, string $timezone) : string {
    $datetime_type = $this->fieldStorage->getSetting('datetime_type');
    if ($datetime_type === DateRangeItem::DATETIME_TYPE_DATE) {
      $storage_format = DateTimeItemInterface::DATE_STORAGE_FORMAT;
    }
    else {
      $storage_format = DateTimeItemInterface::DATETIME_STORAGE_FORMAT;
    }

    $date = new DrupalDateTime($value, $timezone);
    $storage_timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
    $user_timezone = new \DateTimeZone(date_default_timezone_get());

    if ($datetime_type === DateRangeItem::DATETIME_TYPE_ALLDAY) {
      $date->setTimeZone($user_timezone)->setTime(0, 0, 0);
    }

    return $date->setTimezone($storage_timezone)->format($storage_format);
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
