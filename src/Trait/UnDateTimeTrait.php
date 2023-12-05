<?php

namespace Drupal\un_date\Trait;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Common formatting methods.
 */
trait UnDateTimeTrait {

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
  protected function formatTime(DrupalDateTime $date, bool $to_utc = FALSE, $show_timezone = FALSE) {
    $options = [];
    if ($to_utc) {
      $options = [
        'timezone' => 'UTC',
      ];
    }

    $ampm = 'a.m.';
    if ($date->format('a', $options) === 'pm') {
      $ampm = 'p.m.';
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
  protected function formatDate(DrupalDateTime $date, bool $to_utc = FALSE) {
    $options = [];
    if ($to_utc) {
      $options = [
        'timezone' => 'UTC',
      ];
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
  protected function formatDateTime(DrupalDateTime $date, bool $to_utc = FALSE, $show_timezone = FALSE) {
    return $this->formatDate($date, $to_utc) . ' ' . $this->formatTime($date, $to_utc, $show_timezone);
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
  protected function formatDateTimeRange(DrupalDateTime $from, DrupalDateTime $to, bool $to_utc = FALSE, $show_timezone = FALSE) {
    return $this->formatDate($from, $to_utc) . ' ' . $this->formatTime($from, $to_utc) . ' ' . $this->formatTime($to, $to_utc, $show_timezone);
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
  protected function formatTimezone(DrupalDateTime $date, bool $to_utc = FALSE, bool $show_timezone = FALSE) {
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
  protected function getTimezoneOffset(DrupalDateTime $date) {
    return $date->getTimezone()->getOffset($date->getPhpDateTime());
  }

}
