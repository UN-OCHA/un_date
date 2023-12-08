<?php

namespace Drupal\un_date\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\un_date\Trait\UnDateTimeTrait;

/**
 * Plugin implementation of the 'Default' formatter for 'datetime' fields.
 *
 * @FieldFormatter(
 *   id = "un_date_datetime",
 *   label = @Translation("Default (UN)"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class UnDateDateTime extends FormatterBase {

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
      /** @var \Drupal\datetime_range_timezone\Plugin\Field\FieldType\DateRangeTimezone $item */
      /** @var \Drupal\Core\Datetime\DrupalDateTime $date */
      $date = $item->date;

      $utc = $this->getSetting('convert_to_utc');
      $tz = $this->getSetting('display_timezone');
      $datetime_type = $this->getFieldSetting('datetime_type');

      $timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
      if ($datetime_type === DateTimeItem::DATETIME_TYPE_DATETIME) {
        $timezone = new \DateTimeZone(date_default_timezone_get());
      }
      $date->setTimeZone($timezone);

      $theme = 'un_date_date';
      $iso_start_date = $date->format('Y-m-d');

      if ($datetime_type === DateTimeItem::DATETIME_TYPE_DATETIME) {
        $theme = 'un_date_datetime';
        $iso_start_date = $date->format('c');
      }

      $elements[$delta] = [
        '#theme' => $theme . '__' . $theme_suggestions,
        '#iso_start_date' => $iso_start_date,
        '#start_date' => $this->formatDate($date, $utc),
        '#start_time' => $this->formatTime($date, $utc),
        '#timezone' => $timezone->getName(),
        '#display_timezone' => $tz,
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
