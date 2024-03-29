<?php

namespace Drupal\un_date\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\un_date\Trait\UnDateTimeFormatterTrait;
use Drupal\un_date\UnDateRange;
use Drupal\un_date\UnDateTimeZone;

/**
 * Plugin implementation of the 'UN Default' formatter for 'daterange_timezone'.
 *
 * @FieldFormatter(
 *   id = "un_date_daterange_timezone",
 *   label = @Translation("Default (UN)"),
 *   field_types = {
 *     "daterange_timezone"
 *   }
 * )
 */
final class UnDateDateTimeRangeTimezone extends FormatterBase {

  use UnDateTimeFormatterTrait;

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $theme_suggestions = implode('__', [
      $this->viewMode,
      $items->getEntity()->getEntityTypeId(),
      $items->getEntity()->bundle(),
      $items->getFieldDefinition()->getName(),
    ]);

    foreach ($items as $delta => $item) {
      /** @var \Drupal\datetime_range_timezone\Plugin\Field\FieldType\DateRangeTimezone $item */
      /** @var \Drupal\Core\Datetime\DrupalDateTime $start_date */
      $start_date = $item->start_date;
      if (!$start_date) {
        continue;
      }

      /** @var \Drupal\Core\Datetime\DrupalDateTime $end_date */
      $end_date = $item->end_date;
      if (!$end_date) {
        $end_date = $start_date;
      }

      $tz = $this->getSetting('display_timezone');
      $same_date = FALSE;
      $same_day = FALSE;

      $timezone = new UnDateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
      if ($item->timezone) {
        $timezone = new UnDateTimeZone($item->timezone);
      }
      $start_date->setTimezone($timezone);
      $end_date->setTimezone($timezone);

      if ($start_date->format('c') == $end_date->format('c')) {
        $same_date = TRUE;
      }
      elseif ($this->formatDate($start_date) == $this->formatDate($end_date)) {
        $same_day = TRUE;
      }

      $elements[$delta] = [
        '#theme' => 'un_date_datetime_range__' . $theme_suggestions,
        '#daterange' => new UnDateRange($start_date, $end_date),
        '#start' => $start_date,
        '#end' => $end_date,
        '#iso_start_date' => $start_date ? $start_date->format('c') : '',
        '#iso_end_date' => $end_date ? $end_date->format('c') : '',
        '#start_date' => $this->formatDate($start_date),
        '#start_time' => $this->formatTime($start_date),
        '#end_date' => $this->formatDate($end_date),
        '#end_time' => $this->formatTime($end_date),
        '#timezone' => $timezone->getHumanFriendlyName(),
        '#display_timezone' => $tz,
        '#same_date' => $same_date,
        '#same_day' => $same_day,
        '#all_day' => $this->allDay($item),
        '#cache' => [
          'contexts' => [
            'timezone',
          ],
        ],
      ];
    }

    return $elements;
  }

}
