<?php

namespace Drupal\un_date\Trait;

use DateTime;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;

/**
 * Common formatting methods.
 */
trait UnDateTimeTrait {

  const SEPARATOR = '—';

  /**
   * Format time.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   Drupal date time object.
   * @param bool $to_utc
   *   Convert to UTC.
   * @param bool $show_timezone
   *   Show timezone.
   *
   * @return string
   *   Formatted time.
   */
  protected function formatTime(DateTime|DrupalDateTime $date, bool $to_utc = FALSE, $show_timezone = FALSE) {
    $options = [];
    if ($to_utc) {
      $options = [
        'timezone' => 'UTC',
      ];
    }

    $ampm = 'a.m.';
    if ($date instanceof DateTime) {
      if ($date->format('a') === 'pm') {
        $ampm = 'p.m.';
      }
    }
    else {
      if ($date->format('a', $options) === 'pm') {
        $ampm = 'p.m.';
      }
    }

    if ($date instanceof DateTime) {
      // Hide zero minutes.
      if ($date->format('i') === '00') {
        return $date->format('g') . ' ' . $ampm . $this->formatTimezone($date, $to_utc, $show_timezone);
      }
      else {
        return $date->format('g.i') . ' ' . $ampm . $this->formatTimezone($date, $to_utc, $show_timezone);
      }
    }

    // Hide zero minutes.
    if ($date->format('i', $options) === '00') {
      return $date->format('g', $options) . ' ' . $ampm . $this->formatTimezone($date, $to_utc, $show_timezone);
    }
    else {
      return $date->format('g.i', $options) . ' ' . $ampm . $this->formatTimezone($date, $to_utc, $show_timezone);
    }
  }

  /**
   * Format date.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   Drupal date time object.
   * @param bool $to_utc
   *   Convert to UTC.
   *
   * @return string
   *   Formatted date.
   */
  protected function formatDate(DateTime|DrupalDateTime|DateRangeItem $date, bool $to_utc = FALSE) {
    $options = [];
    if ($to_utc) {
      $options = [
        'timezone' => 'UTC',
      ];
    }

    if ($date instanceof DateTime) {
      return $date->format('d.m.Y');
    }

    return $date->format('d.m.Y', $options);
  }

  /**
   * Format datetime.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   Drupal date time object.
   * @param bool $to_utc
   *   Convert to UTC.
   * @param bool $show_timezone
   *   Show timezone.
   *
   * @return string
   *   Formatted date.
   */
  protected function formatDateTime(DateTime|DrupalDateTime|DateRangeItem $date, bool $to_utc = FALSE, $show_timezone = FALSE) {
    return $this->formatDate($date, $to_utc) . ' ' . $this->formatTime($date, $to_utc, $show_timezone);
  }

  /**
   * Format timezone.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   Drupal date time object.
   * @param bool $to_utc
   *   Convert to UTC.
   * @param bool $show_timezone
   *   Show timezone.
   *
   * @return string
   *   Formatted timezone.
   */
  protected function formatTimezone(DateTime|DrupalDateTime|DateRangeItem $date, bool $to_utc = FALSE, bool $show_timezone = FALSE) {
    if ($show_timezone) {
      if ($to_utc) {
        return ' UTC';
      }
      return ' ' . $date->getTimezone()->getName();
    }

    return '';
  }

  /**
   * Get timezone offset.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   Drupal date time object.
   *
   * @return string
   *   Offset.
   */
  protected function getTimezoneOffset(DateTime|DrupalDateTime|DateRangeItem $date) {
    return $date->getTimezone()->getOffset($date->getPhpDateTime());
  }

  /**
   * Get separator with spacing.
   */
  protected function getSeparator() {
    return ' ' . self::SEPARATOR . ' ';
  }

  /**
   * Is all day event.
   *
   * @param Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem $date_item
   *   Drupal date time object.
   *
   * @return bool
   *   TRUE if it's an all day event.
   */
  protected function allDay(DateRangeItem|DateRecurItem $date_item) {
    $options = [
      'timezone' => 'UTC',
    ];

    if ($date_item->start_date->format('Hi', $options) === '0000' && $date_item->end_date->format('Hi', $options) === '0000') {
      return TRUE;
    }

    if ($date_item->start_date->format('Hi', $options) === '0000' && $date_item->end_date->format('Hi', $options) === '2359') {
      return TRUE;
    }

    return FALSE;
  }

}
