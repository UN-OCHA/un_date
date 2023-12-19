<?php

namespace Drupal\un_date\TwigExtension;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\date_recur\DateRange;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\datetime\DateTimeComputed;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeFieldItemList;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\un_date\Trait\UnDateTimeTrait;
use Drupal\un_date\UnDateRange;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Custom twig filters for dates.
 */
class CustomTwig extends AbstractExtension {

  use UnDateTimeTrait;
  use StringTranslationTrait;

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
      new TwigFilter('un_timerange', [$this, 'getUnTimerange']),
      new TwigFilter('un_year', [$this, 'getUnyear']),
      new TwigFilter('un_month', [$this, 'getUnMonth']),
      new TwigFilter('un_month_full', [$this, 'getUnMonthFull']),
      new TwigFilter('un_month_abbr', [$this, 'getUnMonthAbbr']),
      new TwigFilter('un_day', [$this, 'getUnDay']),
      new TwigFilter('un_hour', [$this, 'getUnHour']),
      new TwigFilter('un_minute', [$this, 'getUnMinute']),
      new TwigFilter('un_ampm', [$this, 'getUnAmPm']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('un_is_same_date', [$this, 'isSameDate']),
      new TwigFunction('un_is_same_day', [$this, 'isSameDay']),
      new TwigFunction('un_is_same_month', [$this, 'isSameMonth']),
      new TwigFunction('un_is_same_year', [$this, 'isSameYear']),
      new TwigFunction('un_is_all_day', [$this, 'isAllDay']),
      new TwigFunction('un_is_utc', [$this, 'isUtc']),
      new TwigFunction('un_is_rtl', [$this, 'isRtl']),
      new TwigFunction('un_separator', [$this, 'getSeparator']),
      new TwigFunction('un_duration', [$this, 'getDuration']),
    ];
  }

  /**
   * Format date.
   */
  public function getUnDate($input, $month_format = 'numeric') : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }
    return $this->formatDate($date_item, $month_format);
  }

  /**
   * Format time.
   */
  public function getUnTime($input, bool $show_timezone = FALSE) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $this->formatTime($date_item, $show_timezone);
  }

  /**
   * Format date and time.
   */
  public function getUnDateTime($input, $month_format = 'numeric', bool $show_timezone = FALSE) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $this->formatDateTime($date_item, $month_format, $show_timezone);
  }

  /**
   * Format daterange.
   */
  public function getUnDaterange($input, $month_format = 'numeric', $show_timezone = FALSE) : string {
    $date_item = $this->getDateItem($input);

    if (!$date_item) {
      return '';
    }

    if (!$this->isDateRange($date_item)) {
      return $this->formatDateTime($date_item, $month_format, $show_timezone);
    }

    // Same.
    if ($this->sameDate($date_item)) {
      return $this->formatDateTime($date_item->start_date, $month_format, $show_timezone);
    }

    // Same day.
    if ($this->sameDay($date_item)) {
      if ($this->allDay($date_item)) {
        return $this->formatDate($date_item->start_date, $month_format);
      }
      return $this->formatDateTime($date_item->start_date, $month_format, FALSE) . $this->getSeparatorWithSpaces() . $this->formatTime($date_item->end_date, $show_timezone);
    }

    if ($this->allDay($date_item)) {
      return $this->formatDate($date_item->start_date, $month_format) . $this->getSeparatorWithSpaces() . $this->formatDate($date_item->end_date, $month_format);
    }

    return $this->formatDateTime($date_item->start_date, $month_format, FALSE) . $this->getSeparatorWithSpaces() . $this->formatDateTime($date_item->end_date, $month_format, $show_timezone);
  }

  /**
   * Format daterange.
   */
  public function getUnDaterangeTimes($input, $month_format = 'numeric', $show_timezone = FALSE) : string {
    $date_item = $this->getDateItem($input);

    if (!$date_item) {
      return '';
    }

    if (!$this->isDateRange($date_item)) {
      return $this->formatTime($date_item, $show_timezone);
    }

    // Same.
    if ($date_item->start_date->format('c') == $date_item->end_date->format('c')) {
      return $this->formatTime($date_item->start_date, $show_timezone);
    }

    // Same day.
    if ($this->formatDate($date_item->start_date) === $this->formatDate($date_item->end_date)) {
      if ($this->allDay($date_item)) {
        return $this->t('All day');
      }
      return $this->formatTime($date_item->start_date, FALSE) . $this->getSeparatorWithSpaces() . $this->formatTime($date_item->end_date, $show_timezone);
    }

    if ($this->allDay($date_item)) {
      return $this->formatDate($date_item->start_date, $month_format) . $this->getSeparatorWithSpaces() . $this->formatDate($date_item->end_date, $month_format);
    }

    return $this->formatDateTime($date_item->start_date, $month_format, FALSE) . $this->getSeparatorWithSpaces() . $this->formatDateTime($date_item->end_date, $month_format, $show_timezone);
  }

  /**
   * Format daterange.
   */
  public function getUnDaterangeNamed($input, $month_format = 'numeric', $format = 'default') : string {
    $date_item = $this->getDateRangeFromItem($input);

    if (!$date_item) {
      return '';
    }

    if (!$this->isDateRange($date_item)) {
      return '';
    }

    switch ($format) {
      case 'local_times':
        return $this->localTimes($date_item, $month_format);

      case 'default':
        return $this->getUnDaterange($date_item, $month_format, FALSE);
    }

    return '';
  }

  /**
   * Get date item.
   */
  protected function getDateItem($input) {
    if (is_numeric($input)) {
      return DrupalDateTime::createFromTimestamp($input);
    }

    if (is_string($input)) {
      $date = new DateTimePlus($input);
      if ($date->hasErrors()) {
        return NULL;
      }

      return $date->getPhpDateTime();
    }

    if ($input instanceof \DateTime) {
      return $input;
    }

    if ($input instanceof DateTimeComputed) {
      return $input->getValue();
    }

    if ($input instanceof \DateTimeImmutable) {
      return $input;
    }

    if ($input instanceof UnDateRange) {
      return $input;
    }

    if ($input instanceof DateRange) {
      return new UnDateRange($input->getStart(), $input->getEnd());
    }

    if ($input instanceof DateRecurItem) {
      return $input;
    }

    if ($input instanceof DateRecurFieldItemList) {
      return $input->first();
    }

    if ($input instanceof DateRangeItem) {
      return $input;
    }

    if ($input instanceof DateRangeFieldItemList) {
      return $input->first();
    }

    if ($input instanceof DateTimeItem) {
      return $input;
    }

    if ($input instanceof DateTimeFieldItemList) {
      return $input->first();
    }

    return NULL;
  }

  /**
   * Gat date from date item.
   */
  protected function getDateFromDateItem($input) {
    $date_item = $this->getDateItem($input);

    if (!$date_item) {
      return NULL;
    }

    // Restrict to one date.
    if ($this->isDateRange($date_item)) {
      $date_item = $this->getStartDate($date_item);
    }

    return $date_item;
  }

  /**
   * Gat date from date item.
   */
  protected function getDateRangeFromItem($input) {
    $date_item = $this->getDateItem($input);

    if (!$date_item) {
      return NULL;
    }

    if (!$this->isDateRange($date_item)) {
      return NULL;
    }

    return $date_item;
  }

  /**
   * Format time.
   */
  protected function localTimes($daterange, $month_format = 'numeric') : string {
    $show_timezone = TRUE;

    if ($this->sameDate($daterange)) {
      return '';
    }

    if ($this->sameDay($daterange)) {
      return $this->formatTime($daterange->start_date, FALSE) . ' — ' . $this->formatTime($daterange->end_date, $show_timezone);
    }

    return $this->formatDateTime($daterange->start_date, $month_format, FALSE) . ' — ' . $this->formatDateTime($daterange->end_date, $month_format, $show_timezone);
  }

  /**
   * Get start and end time.
   */
  public function getUnTimerange($daterange) : string {
    $show_timezone = TRUE;

    if ($this->allDay($daterange)) {
      return '';
    }

    return $this->formatTime($daterange->start_date, FALSE) . ' — ' . $this->formatTime($daterange->end_date, $show_timezone);
  }

  /**
   * Get year.
   */
  public function getUnyear($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $date_item->format('Y');
  }

  /**
   * Get month as number.
   */
  public function getUnMonth($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $date_item->format('m');
  }

  /**
   * Get full month name.
   */
  public function getUnMonthFull($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $date_item->format('F');
  }

  /**
   * Get abbreviaterd month name.
   */
  public function getUnMonthAbbr($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $date_item->format('M');
  }

  /**
   * Get day.
   */
  public function getUnDay($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $date_item->format('j');
  }

  /**
   * Get hour.
   */
  public function getUnHour($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $this->formatHour($date_item);
  }

  /**
   * Get minute.
   */
  public function getUnMinute($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $this->formatMinute($date_item);
  }

  /**
   * Get AM/PM.
   */
  public function getUnAmPm($input) : string {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return '';
    }

    return $this->formatAmPm($date_item);
  }

  /**
   * Is same day.
   */
  public function isSameDate($start, $end = NULL) : bool {
    $start = $this->getDateItem($start);

    if (!$start) {
      return FALSE;
    }

    if ($end) {
      $end = $this->getDateFromDateItem($end);

      if (!$end) {
        return FALSE;
      }

      return $this->sameDateStartEnd($start, $end);
    }

    // Make sure start is a range.
    if (!$this->isDateRange($start)) {
      return FALSE;
    }

    return $this->sameDate($start);
  }

  /**
   * Is same day.
   */
  public function isSameDay($start, $end = NULL) : bool {
    $start = $this->getDateItem($start);

    if (!$start) {
      return FALSE;
    }

    if ($end) {
      $end = $this->getDateFromDateItem($end);

      if (!$end) {
        return FALSE;
      }

      return $this->sameDayStartEnd($start, $end);
    }

    // Make sure start is a range.
    if (!$this->isDateRange($start)) {
      return FALSE;
    }

    return $this->sameDay($start);
  }

  /**
   * Is same month and year.
   */
  public function isSameMonth($start, $end = NULL) : bool {
    $start = $this->getDateItem($start);

    if (!$start) {
      return FALSE;
    }

    if ($end) {
      $end = $this->getDateFromDateItem($end);

      if (!$end) {
        return FALSE;
      }

      return $this->SameMonthStartEnd($start, $end);
    }

    // Make sure start is a range.
    if (!$this->isDateRange($start)) {
      return FALSE;
    }

    return $this->SameMonth($start);
  }

  /**
   * Is same year.
   */
  public function isSameYear($start, $end = NULL) : bool {
    $start = $this->getDateItem($start);

    if (!$start) {
      return FALSE;
    }

    if ($end) {
      $end = $this->getDateFromDateItem($end);

      if (!$end) {
        return FALSE;
      }

      return $this->sameYearStartEnd($start, $end);
    }

    // Make sure start is a range.
    if (!$this->isDateRange($start)) {
      return FALSE;
    }

    return $this->sameYear($start);
  }

  /**
   * Is all day.
   */
  public function isAllDay($start, $end = NULL) : bool {
    $start = $this->getDateItem($start);

    if (!$start) {
      return FALSE;
    }

    if ($end) {
      $end = $this->getDateFromDateItem($end);

      if (!$end) {
        return FALSE;
      }

      return $this->allDayStartEnd($start, $end);
    }

    // Make sure start is a range.
    if (!$this->isDateRange($start)) {
      return FALSE;
    }

    return $this->allDay($start);
  }

  /**
   * Is UTC timezone.
   */
  public function isUtc($input) : bool {
    $date_item = $this->getDateFromDateItem($input);

    if (!$date_item) {
      return FALSE;
    }

    $timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
    if (isset($date_item->timezone)) {
      $timezone = new \DateTimeZone($date_item->timezone);
    }

    return $timezone->getName() === 'UTC';
  }

  /**
   * Is Rtl language.
   */
  public function isRtl() : bool {
    return un_date_current_language_rtl();
  }

  /**
   * Get separator.
   */
  public function getSeparator() : string {
    return $this::SEPARATOR;
  }

  /**
   * Get separator with spaces.
   */
  public function getSeparatorWithSpaces() : string {
    return ' ' . $this->getSeparator() . ' ';
  }

  /**
   * Get duration.
   */
  public function getDuration($start, $end = NULL) : string {
    $start = $this->getDateItem($start);

    if (!$start) {
      return '';
    }

    if ($end) {
      $end = $this->getDateFromDateItem($end);

      if (!$end) {
        return '';
      }

      return $this->durationStartEnd($start, $end);
    }

    // Make sure start is a range.
    if (!$this->isDateRange($start)) {
      return FALSE;
    }

    return $this->duration($start);
  }

}
