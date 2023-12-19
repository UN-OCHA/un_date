<?php

namespace Drupal\un_date;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\un_date\Trait\UnDateTimeTrait;

/**
 * Provides a service to handle various date related functionality.
 *
 * @ingroup i18n
 */
class UnDateFormatter extends DateFormatter {

  use UnDateTimeTrait;

  /**
   * {@inheritdoc}
   */
  public function format($timestamp, $type = 'medium', $format = '', $timezone = NULL, $langcode = NULL) {
    if (!isset($timezone)) {
      $timezone = date_default_timezone_get();
    }
    // Store DateTimeZone objects in an array rather than repeatedly
    // constructing identical objects over the life of a request.
    if (!isset($this->timezones[$timezone])) {
      $this->timezones[$timezone] = timezone_open($timezone);
    }

    if (empty($langcode)) {
      $langcode = $this->languageManager->getCurrentLanguage()->getId();
    }

    // Create a DrupalDateTime object from the timestamp and timezone.
    $create_settings = [
      'langcode' => $langcode,
      'country' => $this->country(),
    ];
    $date = DrupalDateTime::createFromTimestamp($timestamp, $this->timezones[$timezone], $create_settings);

    // Override default formats.
    switch ($type) {
      case 'short':
        return $this->formatDateTime($date, 'numeric');

      case 'medium':
        return $this->formatDateTime($date, 'abbreviation');

      case 'long':
        return $this->formatDateTime($date, 'full');

    }

    // If we have a non-custom date format use the provided date format pattern.
    if ($type !== 'custom') {
      if ($date_format = $this->dateFormat($type, $langcode)) {
        $format = $date_format->getPattern();
      }
    }

    // Fall back to the 'medium' date format type if the format string is
    // empty, either from not finding a requested date format or being given an
    // empty custom format string.
    if (empty($format)) {
      return $this->formatDateTime($date, 'abbreviation');
    }

    // Call $date->format().
    $settings = [
      'langcode' => $langcode,
    ];
    return $date->format($format, $settings);
  }

}
