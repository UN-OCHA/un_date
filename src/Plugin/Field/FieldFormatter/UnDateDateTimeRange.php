<?php

namespace Drupal\un_date\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\un_date\Trait\UnDateTimeTrait;

/**
 * Plugin implementation of the 'UN Default' formatter for 'daterange_timezone'.
 *
 * @FieldFormatter(
 *   id = "un_date_daterange",
 *   label = @Translation("Default (UN)"),
 *   field_types = {
 *     "daterange"
 *   }
 * )
 */
class UnDateDateTimeRange extends FormatterBase {

  use UnDateTimeTrait;

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
      /** @var \Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem $item */
      /** @var \Drupal\Core\Datetime\DrupalDateTime $start_date */
      $start_date = $item->start_date;
      /** @var \Drupal\Core\Datetime\DrupalDateTime $end_date */
      $end_date = $item->end_date;

      $utc = $this->getSetting('convert_to_utc');
      $tz = $this->getSetting('display_timezone');
      $same_date = FALSE;
      $same_day = FALSE;

      if ($start_date->getTimestamp() == $end_date->getTimestamp()) {
        $same_date = TRUE;
      }
      elseif ($this->formatDate($start_date, $utc) == $this->formatDate($end_date, $utc)) {
        $same_day = TRUE;
      }

      $datetime_type = $this->getFieldSetting('datetime_type');

      $timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
      if ($datetime_type === DateTimeItem::DATETIME_TYPE_DATETIME) {
        $timezone = new \DateTimeZone(date_default_timezone_get());
      }

      $theme = 'un_date_date_range';
      $iso_start_date = $start_date->format('Y-m-d');
      $iso_end_date = $end_date->format('Y-m-d') ?? '';

      if ($datetime_type === DateTimeItem::DATETIME_TYPE_DATETIME) {
        $theme = 'un_date_datetime_range';
        $iso_start_date = $start_date->format('c');
        $iso_end_date = $end_date->format('c') ?? '';
      }

      $elements[$delta] = [
        '#theme' => $theme . '__' . $theme_suggestions,
        '#iso_start_date' => $iso_start_date,
        '#iso_end_date' => $iso_end_date,
        '#start_date' => $this->formatDate($start_date, $utc),
        '#start_time' => $this->formatTime($start_date, $utc),
        '#end_date' => $this->formatDate($end_date, $utc),
        '#end_time' => $this->formatTime($end_date, $utc),
        '#timezone' => $timezone->getName(),
        '#display_timezone' => $tz,
        '#same_date' => $same_date,
        '#same_day' => $same_day,
        '#all_day' => $this->allDay($item, $timezone->getName()),
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
