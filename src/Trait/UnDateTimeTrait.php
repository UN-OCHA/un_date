<?php

namespace Drupal\un_date\Trait;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\datetime_range_timezone\Plugin\Field\FieldType\DateRangeTimezone;

/**
 * Common formatting methods.
 */
trait UnDateTimeTrait {

  const SEPARATOR = '—';

  /**
   * Format time.
   */
  protected function formatTime(\DateTime|DrupalDateTime $date, bool $to_utc = FALSE, $show_timezone = FALSE) : string {
    $options = [];
    if ($to_utc) {
      $options = [
        'timezone' => 'UTC',
      ];
    }

    $ampm = 'a.m.';
    if ($date instanceof \DateTime) {
      if ($date->format('a') === 'pm') {
        $ampm = 'p.m.';
      }
    }
    else {
      if ($date->format('a', $options) === 'pm') {
        $ampm = 'p.m.';
      }
    }

    if ($date instanceof \DateTime) {
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

    return '';
  }

  /**
   * Format date.
   */
  protected function formatDate(\DateTime|DrupalDateTime|DateRangeItem $date, bool $to_utc = FALSE) : string {
    $options = [];
    if ($to_utc) {
      $options = [
        'timezone' => 'UTC',
      ];
    }

    if ($date instanceof \DateTime) {
      return $date->format('d.m.Y');
    }

    return $date->format('d.m.Y', $options);
  }

  /**
   * Format datetime.
   */
  protected function formatDateTime(\DateTime|DrupalDateTime|DateRangeItem $date, bool $to_utc = FALSE, $show_timezone = FALSE) : string {
    return $this->formatDate($date, $to_utc) . ' ' . $this->formatTime($date, $to_utc, $show_timezone);
  }

  /**
   * Format timezone.
   */
  protected function formatTimezone(\DateTime|DrupalDateTime|DateRangeItem $date, bool $to_utc = FALSE, bool $show_timezone = FALSE) : string {
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
   */
  protected function getTimezoneOffset(\DateTime|DrupalDateTime|DateRangeItem $date) : string {
    return $date->getTimezone()->getOffset($date->getPhpDateTime());
  }

  /**
   * Get separator with spacing.
   */
  protected function getSeparator() : string {
    return ' ' . self::SEPARATOR . ' ';
  }

  /**
   * Is all day event.
   */
  protected function allDay(DateRangeItem|DateRecurItem|DateRangeTimezone $date_item, $timezone = 'UTC') : bool {
    return $this->allDayStartEnd($date_item->start_date, $date_item->end_date, $timezone);
  }

  /**
   * Is all day event.
   */
  protected function allDayStartEnd(\DateTime|DrupalDateTime $start, \DateTime|DrupalDateTime $end, $timezone = 'UTC') : bool {
    if ($start->format('Hi') === '0000' && $end->format('Hi') === '0000') {
      return TRUE;
    }

    if ($start->format('Hi') === '0000' && $end->format('Hi') === '2359') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public static function defaultSettings() {
    return [
      'display_timezone' => TRUE,
      'convert_to_utc' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

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
   *
   * @codeCoverageIgnore
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary[] = $this->t('@action the timezone', [
      '@action' => $this->getSetting('display_timezone') ? 'Showing' : 'Hiding',
    ]);

    $summary[] = $this->t('@action to UTC', [
      '@action' => $this->getSetting('convert_to_utc') ? 'Convert' : 'Do not convert',
    ]);

    return $summary;
  }

}
