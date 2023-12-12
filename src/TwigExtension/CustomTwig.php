<?php

namespace Drupal\un_date\TwigExtension;

use Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeFieldItemList;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\un_date\Trait\UnDateTimeTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Custom twig filters for dates.
 */
class CustomTwig extends AbstractExtension {

  use UnDateTimeTrait;

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('un_date', [$this, 'getUnDate']),
      new TwigFilter('un_time', [$this, 'getUnTime']),
      new TwigFilter('un_datetime', [$this, 'getUnDateTime']),
      new TwigFilter('un_daterange', [$this, 'getUnDaterange']),
      new TwigFilter('un_daterange_times', [$this, 'getUnDaterangeTimes']),
      new TwigFilter('un_daterange_named', [$this, 'getUnDaterangeNamed']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('un_is_all_day', [$this, 'isAllDay']),
      new TwigFunction('un_is_utc', [$this, 'isUtc']),
    ];
  }

  /**
   * Format date.
   */
  public function getUnDate($in, bool $to_utc = FALSE, $month_format = 'numeric') : string {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return '';
    }

    // Restrict to one date.
    if (isset($date_item->start_date)) {
      $date_item = $date_item->start_date;
    }

    return $this->formatDate($date_item, $to_utc, $month_format);
  }

  /**
   * Format time.
   */
  public function getUnTime($in, bool $to_utc = FALSE, bool $show_timezone = FALSE) : string {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return '';
    }

    // Restrict to one date.
    if (isset($date_item->start_date)) {
      $date_item = $date_item->start_date;
    }

    return $this->formatTime($date_item, $to_utc, $show_timezone);
  }

  /**
   * Format date and time.
   */
  public function getUnDateTime($in, bool $to_utc = FALSE, bool $show_timezone = FALSE, $month_format = 'numeric') : string {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return '';
    }

    // Restrict to one date.
    if (isset($date_item->start_date)) {
      $date_item = $date_item->start_date;
    }

    return $this->formatDateTime($date_item, $to_utc, $show_timezone, $month_format);
  }

  /**
   * Format daterange.
   */
  public function getUnDaterange($in, bool $to_utc = FALSE, $show_timezone = FALSE, $month_format = 'numeric') : string {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return '';
    }

    // Same.
    if ($date_item->start_date->format('c') == $date_item->end_date->format('c')) {
      return $this->formatDateTime($date_item->start_date, $to_utc, $show_timezone, $month_format);
    }

    // Same day.
    if ($this->formatDate($date_item->start_date) === $this->formatDate($date_item->end_date)) {
      if ($this->allDay($date_item)) {
        return $this->formatDate($date_item->start_date, $to_utc, FALSE, $month_format);
      }
      return $this->formatDateTime($date_item->start_date, $to_utc, FALSE, $month_format) . $this->getSeparator() . $this->formatTime($date_item->end_date, $to_utc, $show_timezone);
    }

    if ($this->allDay($date_item)) {
      return $this->formatDate($date_item->start_date, $to_utc, FALSE, $month_format) . $this->getSeparator() . $this->formatDate($date_item->end_date, $to_utc, FALSE, $month_format);
    }

    return $this->formatDateTime($date_item->start_date, $to_utc, FALSE, $month_format) . $this->getSeparator() . $this->formatDateTime($date_item->end_date, $to_utc, $show_timezone, $month_format);
  }

  /**
   * Format daterange.
   */
  public function getUnDaterangeTimes($in, bool $to_utc = FALSE, $show_timezone = FALSE, $month_format = 'numeric') : string {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return '';
    }

    /** @var Datetime */
    // Same.
    if ($date_item->start_date->format('c') == $date_item->end_date->format('c')) {
      return $this->formatTime($date_item->start_date, $to_utc, $show_timezone);
    }

    // Same day.
    if ($this->formatDate($date_item->start_date, $to_utc) === $this->formatDate($date_item->end_date, $to_utc)) {
      if ($this->allDay($date_item)) {
        return 'All day';
      }
      return $this->formatTime($date_item->start_date, $to_utc, FALSE) . $this->getSeparator() . $this->formatTime($date_item->end_date, $to_utc, $show_timezone);
    }

    if ($this->allDay($date_item)) {
      return $this->formatDate($date_item->start_date, $to_utc, FALSE, $month_format) . $this->getSeparator() . $this->formatDate($date_item->end_date, $to_utc, FALSE, $month_format);
    }

    return $this->formatDateTime($date_item->start_date, $to_utc, FALSE, $month_format) . $this->getSeparator() . $this->formatDateTime($date_item->end_date, $to_utc, $show_timezone, $month_format);
  }

  /**
   * Format daterange.
   */
  public function getUnDaterangeNamed($in, $format = 'default', $month_format = 'numeric') : string {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return '';
    }

    switch ($format) {
      case 'local_times':
        return $this->localTimes($date_item, $month_format);

      case 'default':
        return $this->getUnDaterange($date_item, $month_format);
    }

    return '';
  }

  /**
   * Get date range item.
   */
  protected function getDateItem($in) {
    if ($in instanceof DateRecurItem) {
      return $in;
    }

    if ($in instanceof DateRecurFieldItemList) {
      return $in->first();
    }

    if ($in instanceof DateRangeItem) {
      return $in;
    }

    if ($in instanceof DateRangeFieldItemList) {
      return $in->first();
    }

    if ($in instanceof DateTimeItem) {
      return $in;
    }

    if ($in instanceof DateTimeFieldItemList) {
      return $in->first();
    }

    if ($in instanceof \DateTime) {
      return $in;
    }

    return NULL;
  }

  /**
   * Format time.
   */
  protected function localTimes(DateRangeItem|DateRecurItem $daterange, $month_format = 'numeric') : string {
    $to_utc = FALSE;
    $show_timezone = TRUE;

    // Only output time if dates are equal.
    if ($this->formatDate($daterange->start_date, TRUE) === $this->formatDate($daterange->start_date, FALSE)) {
      if ($this->allDay($daterange)) {
        return '';
      }
      return $this->formatTime($daterange->start_date, $to_utc, FALSE) . ' — ' . $this->formatTime($daterange->end_date, $to_utc, $show_timezone);
    }
    else {
      return $this->formatDateTime($daterange->start_date, $to_utc, FALSE, $month_format) . ' — ' . $this->formatDateTime($daterange->end_date, $to_utc, $show_timezone, $month_format);
    }
  }

  /**
   * Is all day event.
   */
  public function isAllDay($in) : bool {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return FALSE;
    }

    if ($in instanceof \DateTime) {
      return FALSE;
    }

    return $this->allDay($date_item);
  }

  /**
   * Is UTC timezone.
   */
  public function isUtc($in) : bool {
    $date_item = $this->getDateItem($in);

    if (!$date_item) {
      return FALSE;
    }

    $timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
    if (isset($date_item->timezone)) {
      $timezone = new \DateTimeZone($date_item->timezone);
    }

    return $timezone->getName() === 'UTC';
  }

}
