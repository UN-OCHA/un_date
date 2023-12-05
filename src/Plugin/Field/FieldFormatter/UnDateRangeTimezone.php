<?php

namespace Drupal\un_date\Plugin\Field\FieldFormatter;

use DateTimeZone;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime_range_timezone\Plugin\Field\FieldFormatter\DateRangeTimezoneFormatterBase;
use Drupal\un_date\Trait\UnDateTimeTrait;

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

class UnDateRangeTimezone extends DateRangeTimezoneFormatterBase {

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
      /** @var \Drupal\Core\Datetime\DrupalDateTime $start_date */
      $start_date = $item->start_date;
      /** @var \Drupal\Core\Datetime\DrupalDateTime $end_date */
      $end_date = $item->end_date;

      $utc = $this->getSetting('convert_to_utc');
      $tz = $this->getSetting('display_timezone');
      $same_date = FALSE;
      $same_day = FALSE;

      if ($start_date == $end_date) {
        $same_date = TRUE;
      }
      elseif ($this->formatDate($start_date, $utc) == $this->formatDate($end_date, $utc)) {
        $same_day = TRUE;
      }

      $timezone = new DateTimeZone('UTC');
      if ($item->timezone) {
        $timezone = new DateTimeZone($item->timezone);
        $start_date->setTimezone($timezone);
        $end_date->setTimezone($timezone);
      }

      $elements[$delta] = [
        '#theme' => 'un_date_datetime_range_timezone__' . $theme_suggestions,
        '#iso_start_date' => $start_date ? $start_date->format('c') : '',
        '#iso_end_date' => $end_date ? $end_date->format('c') : '',
        '#start_date' => $this->formatDate($start_date, $utc),
        '#start_time' => $this->formatTime($start_date, $utc),
        '#end_date' => $this->formatDate($end_date, $utc),
        '#end_time' => $this->formatTime($end_date, $utc),
        '#separator' => $this->getSetting('separator'),
        '#timezone' => $timezone->getName(),
        '#display_timezone' => $tz,
        '#same_date' => $same_date,
        '#same_day' => $same_day,
        '#all_day' => FALSE,
        '#cache' => [
          'contexts' => [
            'timezone',
          ],
        ],
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'separator' => '-',
      'display_timezone' => FALSE,
      'convert_to_utc' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Date separator'),
      '#description' => $this->t('The string to separate the start and end dates'),
      '#default_value' => $this->getSetting('separator'),
    ];

    $form['display_timezone'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display Timezone'),
      '#description' => $this->t('Should we display the timezone after the formatted date?'),
      '#default_value' => $this->getSetting('display_timezone'),
    ];

    $form['convert_to_utc'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Convert to UTC'),
      '#description' => $this->t('Should we convert to UTC?'),
      '#default_value' => $this->getSetting('convert_to_utc'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    if ($separator = $this->getSetting('separator')) {
      $summary[] = $this->t('Separator: %separator', ['%separator' => $separator]);
    }

    $summary[] = $this->t('@action the timezone', [
      '@action' => $this->getSetting('display_timezone') ? 'Showing' : 'Hiding',
    ]);

    $summary[] = $this->t('@action to UTC', [
      '@action' => $this->getSetting('convert_to_utc') ? 'Convert' : 'Do not convert',
    ]);

    return $summary;
  }

}
