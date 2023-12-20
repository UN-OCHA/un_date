<?php

namespace Drupal\un_date\Trait;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\date_recur\DateRange;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\datetime\DateTimeComputed;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\datetime_range_timezone\Plugin\Field\FieldType\DateRangeTimezone;
use Drupal\un_date\UnDateRange;

/**
 * Common formatting methods.
 */
trait UnDateTimeTrait {

  /**
   * Actually a const, but only works in PHP8.2.
   *
   * @var string
   * @phpcs:disable Drupal.NamingConventions.ValidVariableName.LowerCamelName
   */
  public static string $SEPARATOR = '—';

  /**
   * List of supported formats for months.
   *
   * @var array
   */
  protected array $monthFormats = [
    'numeric' => 'As a number',
    'full' => 'Full month name',
    'abbreviation' => 'Abbreviated month name',
  ];

  /**
   * List of supported templates.
   *
   * @var array
   */
  protected array $templates = [
    'default' => 'Default',
    'un_date_date_block' => 'Date as block',
  ];

  /**
   * Format time.
   */
  protected function formatTime(\DateTime|\DateTimeImmutable|DrupalDateTime|DateTimeComputed $date, $show_timezone = FALSE) : string {
    if ($date instanceof DateTimeComputed) {
      $date = $date->getValue();
    }

    // Midnight.
    if (($date->format('G') == '0' || $date->format('G') == '24') && $date->format('i') === '00') {
      return t('midnight');
    }

    // Noon.
    if ($date->format('G') == '12' && $date->format('i') === '00') {
      return t('noon');
    }

    $ampm = '';
    $time_format = 'g.i';

    switch ($this->getLocale()) {
      case 'en':
        $time_format = 'g.i';
        if ($date->format('i') === '00') {
          $time_format = 'g';
        }

        $ampm = ' a.m.';
        if ($date->format('a') === 'pm') {
          $ampm = ' p.m.';
        }
        break;

      case 'fr':
        $time_format = 'G \h i';
        if ($date->format('i') === '00') {
          $time_format = 'G \h\e\u\r\e\s';
          if ($date->format('G') == '1') {
            $time_format = 'G \h\e\u\r\e';
          }
        }
        break;

      case 'es':
        $time_format = 'G.i \h\o\r\a\s';
        if ($date->format('i') === '00') {
          $time_format = 'G \h\o\r\a\s';
          if ($date->format('G') == '1') {
            $time_format = 'G \h\o\r\a';
          }
        }
        break;

      case 'ar':
        $time_format = 'G.i';
        if ($date->format('i') === '00') {
          $time_format = 'G';
        }
        break;

      case 'zh-hans':
        $time_format = 'G時i分';
        if ($date->format('i') === '00') {
          $time_format = 'G時正';
        }
        break;

    }

    return $date->format($time_format) . $ampm . $this->formatTimezone($date, $show_timezone);
  }

  /**
   * Format hour.
   */
  protected function formatHour(\DateTime|DrupalDateTime $date) : string {
    // Midnight.
    if (($date->format('G') == '0' || $date->format('G') == '24') && $date->format('i') === '00') {
      return t('midnight');
    }

    // Noon.
    if ($date->format('G') == '12' && $date->format('i') === '00') {
      return t('noon');
    }

    $time_format = 'g';

    switch ($this->getLocale()) {
      case 'en':
        $time_format = 'g';
        break;

      case 'fr':
      case 'es':
      case 'ar':
      case 'zh-hans':
        $time_format = 'G';
    }

    return $date->format($time_format);
  }

  /**
   * Format minute.
   */
  protected function formatMinute(\DateTime|DrupalDateTime $date) : string {
    if ($date->format('i') === '00') {
      return '';
    }

    $time_format = 'i';
    return $date->format($time_format);
  }

  /**
   * Format AM/PM.
   */
  protected function formatAmPm(\DateTime|DrupalDateTime $date) : string {
    // Midnight.
    if (($date->format('G') == '0' || $date->format('G') == '24') && $date->format('i') === '00') {
      return '';
    }

    // Noon.
    if ($date->format('G') == '12' && $date->format('i') === '00') {
      return '';
    }

    $ampm = '';

    switch ($this->getLocale()) {
      case 'en':
        $ampm = 'a.m.';
        if ($date->format('a') === 'pm') {
          $ampm = 'p.m.';
        }
        break;

    }

    return $ampm;
  }

  /**
   * Format date.
   */
  protected function formatDate(\DateTime|\DateTimeImmutable|DrupalDateTime|DateTimeComputed $date, $month_format = 'numeric') : string {
    // Twig doens't have a setting.
    if (is_callable([$this, 'getSetting'])) {
      $month_format = $this->getSetting('month_format') ?? 'numeric';
    }

    $month_format = $this->validateMonthFormat($month_format);

    $date_format = 'j.m.Y';
    switch ($month_format) {
      case 'numeric':
        $date_format = 'j.m.Y';
        break;

      case 'full':
        $date_format = 'j F Y';
        break;

      case 'abbreviation':
        $date_format = 'j M. Y';

        if ($this->getLocale() == 'zh-hans') {
          $date_format = 'j M Y';
        }
        elseif ($this->getLocale() == 'ar') {
          $date_format = 'j M Y';
        }
        break;

    }

    // Always use DrupalDateTime for translations.
    if (!$date instanceof DrupalDateTime) {
      $date = (new DrupalDateTime())->createFromDateTime($date);
    }

    return $date->format($date_format);
  }

  /**
   * Format datetime.
   */
  protected function formatDateTime(\DateTime|\DateTimeImmutable|DrupalDateTime|DateTimeComputed|DateRangeItem $date, $month_format = 'numeric', $show_timezone = FALSE) : string {
    return $this->formatDate($date, $month_format) . ' ' . $this->formatTime($date, $show_timezone);
  }

  /**
   * Format HTML datetime.
   */
  protected function formatHtmlDateTime(\DateTime|\DateTimeImmutable|DrupalDateTime|DateTimeComputed|DateRangeItem $date, $month_format = 'numeric', $show_timezone = FALSE) : string {
    return $date->format('c');
  }

  /**
   * Format daterange.
   */
  public function formatDaterange($date, $month_format = 'numeric', $show_timezone = FALSE) : string {
    if (!$this->isDateRange($date)) {
      return $this->formatDateTime($date, $month_format, $show_timezone);
    }

    // Same.
    if ($this->sameDate($date)) {
      return $this->formatDateTime($date->start_date, $month_format, $show_timezone);
    }

    // Same day.
    if ($this->sameDay($date)) {
      if ($this->allDay($date)) {
        return $this->formatDate($date->start_date, $month_format);
      }
      return $this->formatDateTime($date->start_date, $month_format, FALSE) . $this->getSeparatorWithSpaces() . $this->formatTime($date->end_date, $show_timezone);
    }

    if ($this->allDay($date)) {
      return $this->formatDate($date->start_date, $month_format) . $this->getSeparatorWithSpaces() . $this->formatDate($date->end_date, $month_format);
    }

    return $this->formatDateTime($date->start_date, $month_format, FALSE) . $this->getSeparatorWithSpaces() . $this->formatDateTime($date->end_date, $month_format, $show_timezone);
  }

  /**
   * Format daterange times.
   */
  public function formatDaterangeTimes($date, $month_format = 'numeric', $show_timezone = FALSE) : string {
    if (!$this->isDateRange($date)) {
      return $this->formatTime($date, $show_timezone);
    }

    // Same.
    if ($date->start_date->format('c') == $date->end_date->format('c')) {
      return $this->formatTime($date->start_date, $show_timezone);
    }

    // Same day.
    if ($this->formatDate($date->start_date) === $this->formatDate($date->end_date)) {
      if ($this->allDay($date)) {
        return $this->t('All day');
      }
      return $this->formatTime($date->start_date, FALSE) . $this->getSeparatorWithSpaces() . $this->formatTime($date->end_date, $show_timezone);
    }

    if ($this->allDay($date)) {
      return $this->formatDate($date->start_date, $month_format) . $this->getSeparatorWithSpaces() . $this->formatDate($date->end_date, $month_format);
    }

    return $this->formatDateTime($date->start_date, $month_format, FALSE) . $this->getSeparatorWithSpaces() . $this->formatDateTime($date->end_date, $month_format, $show_timezone);
  }

  /**
   * Format timezone.
   */
  protected function formatTimezone(\DateTime|\DateTimeImmutable|DrupalDateTime|DateTimeComputed|DateRangeItem $date, bool $show_timezone = FALSE) : string {
    if ($show_timezone) {
      return ' ' . $date->getTimezone()->getName();
    }

    return '';
  }

  /**
   * Get timezone offset.
   */
  protected function getTimezoneOffset(\DateTime|\DateTimeImmutable|DrupalDateTime|DateTimeComputed|DateRangeItem $date) : string {
    return $date->getTimezone()->getOffset($date->getPhpDateTime());
  }

  /**
   * Get separator.
   */
  public function getSeparator() : string {
    return $this::$SEPARATOR;
  }

  /**
   * Get separator with spaces.
   */
  public function getSeparatorWithSpaces() : string {
    return ' ' . $this->getSeparator() . ' ';
  }

  /**
   * Is object a date range.
   */
  protected function isDateRange($object) : bool {
    if ($object instanceof DateRangeItem
      || $object instanceof DateRange
      || $object instanceof DateRecurItem
      || $object instanceof DateRangeTimezone
      || $object instanceof UnDateRange) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get start date.
   */
  protected function getStartDate($object) {
    if (!$this->isDateRange($object)) {
      return NULL;
    }

    if ($object instanceof DateTimeComputed) {
      return $object->getValue();
    }

    if ($object instanceof DateRangeItem) {
      return $object->get('start_date')->getValue();
    }

    if ($object instanceof DateRange) {
      return $object->getStart();
    }

    if ($object instanceof DateRecurItem) {
      return $object->start_date;
    }

    if ($object instanceof DateRangeTimezone) {
      return $object->get('start_date');
    }

    if ($object instanceof UnDateRange) {
      return $object->start_date;
    }

    return NULL;
  }

  /**
   * Is same date.
   */
  protected function sameDate($date_item) : bool {
    return $this->sameDateStartEnd($date_item->start_date, $date_item->end_date);
  }

  /**
   * Is same date.
   */
  protected function sameDateStartEnd($start, $end) : bool {
    return $start->format('c') == $end->format('c');
  }

  /**
   * Is same month and year.
   */
  protected function sameMonth($date_item) : bool {
    return $this->sameMonthStartEnd($date_item->start_date, $date_item->end_date);
  }

  /**
   * Is same month and year.
   */
  protected function sameMonthStartEnd($start, $end) : bool {
    return $start->format('Ym') == $end->format('Ym');
  }

  /**
   * Is same year.
   */
  protected function sameYear($date_item) : bool {
    return $this->sameYearStartEnd($date_item->start_date, $date_item->end_date);
  }

  /**
   * Is same year.
   */
  protected function sameYearStartEnd($start, $end) : bool {
    return $start->format('Y') == $end->format('Y');
  }

  /**
   * Is same day.
   */
  protected function sameDay($date_item) : bool {
    return $this->sameDayStartEnd($date_item->start_date, $date_item->end_date);
  }

  /**
   * Is same day.
   */
  protected function sameDayStartEnd($start, $end) : bool {
    return $this->formatDate($start) == $this->formatDate($end);
  }

  /**
   * Is all day.
   */
  protected function allDay($date_item) : bool {
    return $this->allDayStartEnd($date_item->start_date, $date_item->end_date);
  }

  /**
   * Is all day.
   */
  protected function allDayStartEnd($start, $end) : bool {
    if ($start->format('Hi') === '0000' && $end->format('Hi') === '0000') {
      return TRUE;
    }

    if ($start->format('Hi') === '0000' && $end->format('Hi') === '2359') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Duration.
   */
  protected function duration(DateRangeItem|DateRecurItem|DateRangeTimezone|UnDateRange $date_item) : string {
    return $this->durationStartEnd($date_item->start_date, $date_item->end_date);
  }

  /**
   * Duration.
   */
  protected function durationStartEnd(\DateTime|DrupalDateTime $start, \DateTime|DrupalDateTime $end) : string {
    $output = [];
    $interval = $start->diff($end, TRUE);

    if ($interval->y) {
      $output[] = un_date_format_plural($interval->format("%y"), '1 year', '@count years');
    }
    if ($interval->m) {
      $output[] = un_date_format_plural($interval->format("%m"), '1 month', '@count months');
    }
    if ($interval->d) {
      $output[] = un_date_format_plural($interval->format("%d"), '1 day', '@count days');
    }
    if ($interval->h) {
      $output[] = un_date_format_plural($interval->format("%h"), '1 hour', '@count hours');
    }
    if ($interval->i) {
      $output[] = un_date_format_plural($interval->format("%i"), '1 minute', '@count minutes');
    }
    if ($interval->s) {
      $output[] = un_date_format_plural($interval->format("%s"), '1 second', '@count seconds');
    }

    return implode(', ', $output);
  }

  /**
   * Get current site language.
   */
  public function getLocale() {
    return \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

  /**
   * Validate and fallback for month format.
   */
  protected function validateMonthFormat($month_format) {
    if (!$month_format || !is_string($month_format)) {
      $month_format = 'numeric';
    }

    $month_format = strtolower($month_format);

    if ($month_format == 'abbr') {
      $month_format = 'abbreviation';
    }

    if (!array_key_exists($month_format, $this->monthFormats)) {
      $month_format = 'numeric';
    }

    return $month_format;
  }

}
