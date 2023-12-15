<?php

namespace Drupal\un_date\Trait;

use Drupal\Core\Form\FormStateInterface;
use Drupal\un_date\Plugin\Field\FieldFormatter\UnDateDateRecurBasic;

/**
 * Common formatting methods.
 */
trait UnDateTimeFormatterTrait {

  use UnDateTimeTrait;

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public static function defaultSettings() {
    if (self::class == UnDateDateRecurBasic::class) {
      return [
        'display_timezone' => TRUE,
        'month_format' => 'numeric',
        'template' => 'default',
        'show_next' => 5,
        'count_per_item' => TRUE,
        'interpreter' => 'un_interpreter',
      ] + parent::defaultSettings();
    }

    return [
      'display_timezone' => TRUE,
      'month_format' => 'numeric',
      'template' => 'default',
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

    $form['month_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Month format'),
      '#options' => $this->monthFormats,
      '#description' => $this->t('In which format will the month be displayed'),
      '#default_value' => $this->getSetting('month_format'),
    ];

    $form['template'] = [
      '#type' => 'select',
      '#title' => $this->t('Template'),
      '#options' => $this->templates,
      '#description' => $this->t('Template to use'),
      '#default_value' => $this->getSetting('template'),
    ];

    if (self::class == UnDateDateRecurBasic::class) {
      $form['interpreter'] = [
        '#type' => 'select',
        '#title' => $this->t('Recurring date interpreter'),
        '#description' => $this->t('Choose a plugin for converting rules into a human readable description.'),
        '#default_value' => $this->getSetting('interpreter'),
        '#options' => $this->getInterpreterOptions(),
        '#required' => FALSE,
        '#empty_option' => $this->t('- Do not show interpreted rule -'),
      ];

      $form['show_next'] = [
        '#field_prefix' => $this->t('Show maximum of'),
        '#field_suffix' => $this->t('occurrences'),
        '#type' => 'number',
        '#min' => 0,
        '#default_value' => $this->getSetting('show_next'),
        '#attributes' => ['size' => 4],
      ];
    }

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

    $summary[] = $this->t('Month display: @action', [
      '@action' => $this->monthFormats[$this->getSetting('month_format') ?? 'numeric'],
    ]);

    $summary[] = $this->t('Template: @action', [
      '@action' => $this->templates[$this->getSetting('template') ?? 'default'],
    ]);

    if ($this->getSetting('interpreter')) {
      $summary[] = $this->t('Human readable interpreter: @action', [
        '@action' => $this->getSetting('interpreter') ?? $this->t('Not specified'),
      ]);
    }

    if (self::class == UnDateDateRecurBasic::class) {
      $showOccurrencesCount = $this->getSetting('show_next');
      if ($showOccurrencesCount > 0) {
        $summary[] = $this->formatPlural(
          $showOccurrencesCount,
          'Show maximum of @count occurrence',
          'Show maximum of @count occurrences',
        );
      }
    }

    return $summary;
  }

  /**
   * Get an option list of interpreters.
   */
  protected function getInterpreterOptions() {
    return [];
  }

}
