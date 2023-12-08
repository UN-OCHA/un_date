<?php

declare(strict_types = 1);

namespace Drupal\un_date\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\DependencyTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\date_recur\DateRange;
use Drupal\date_recur\Entity\DateRecurInterpreterInterface;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\un_date\Trait\UnDateTimeTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Basic recurring date formatter.
 *
 * @FieldFormatter(
 *   id = "un_data_date_recur_basic",
 *   label = @Translation("Date recur formatter (UN)"),
 *   field_types = {
 *     "date_recur"
 *   }
 * )
 */
class UnDateDateRecurBasic extends FormatterBase {

  use DependencyTrait;
  use UnDateTimeTrait;

  protected const COUNT_PER_ITEM_ITEM = 'per_item';

  protected const COUNT_PER_ITEM_ALL = 'all_items';

  /**
   * Date format config ID.
   *
   * @var string|null
   */
  protected ?string $formatType;

  /**
   * Constructs a new DateRecurBasicFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $dateFormatStorage
   *   The date format entity storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $dateRecurInterpreterStorage
   *   The date recur interpreter entity storage.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, DateFormatterInterface $dateFormatter, EntityStorageInterface $dateFormatStorage, protected EntityStorageInterface $dateRecurInterpreterStorage) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $dateFormatter, $dateFormatStorage);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('date.formatter'),
      $container->get('entity_type.manager')->getStorage('date_format'),
      $container->get('entity_type.manager')->getStorage('date_recur_interpreter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      // Show number of occurrences.
      'show_next' => 5,
      // Whether number of occurrences should be per item or in total.
      'count_per_item' => TRUE,
      // Date format for occurrences.
      'occurrence_format_type' => 'medium',
      // Date format for end date, if same day as start date.
      'same_end_date_format_type' => 'medium',
      'interpreter' => NULL,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $originalFormatType = $form['format_type'];
    unset($form['format_type']);

    // Redefine format type to change the natural order of form fields.
    $form['format_type'] = $originalFormatType;
    $form['format_type']['#title'] = $this->t('Non-Repeating Date format');
    $form['format_type']['#description'] = $this->t('Date format used for field values without repeat rules.');
    $form['occurrence_format_type'] = $originalFormatType;
    $form['occurrence_format_type']['#title'] = $this->t('Start and end date format');
    $form['occurrence_format_type']['#default_value'] = $this->getSetting('occurrence_format_type');
    $form['occurrence_format_type']['#description'] = $this->t('Date format used for field values with repeat rules.');
    $form['same_end_date_format_type'] = $originalFormatType;
    $form['same_end_date_format_type']['#title'] = $this->t('Same day end date format');
    $form['same_end_date_format_type']['#description'] = $this->t('Date format used for end date if field value has repeat rule. Used only if occurs on same calendar day as start date.');
    $form['same_end_date_format_type']['#default_value'] = $this->getSetting('same_end_date_format_type');

    // Redefine separator to change the natural order of form fields.
    $originalSeparator = $form['separator'];
    unset($form['separator']);
    $form['separator'] = $originalSeparator;
    // Change the width of the field if not already set. (Not set by default)
    $form['separator']['#size'] ??= 5;

    // Redefine timezone to change the natural order of form fields.
    $originalTimezoneOverride = $form['timezone_override'];
    unset($form['timezone_override']);
    $form['timezone_override'] = $originalTimezoneOverride;
    $form['timezone_override']['#empty_option'] = $this->t('Use current user timezone');
    $form['timezone_override']['#description'] = $this->t('Change the timezone used for displaying dates (not recommended).');

    $interpreterOptions = array_map(
      fn (DateRecurInterpreterInterface $interpreter): string => $interpreter->label() ?? (string) $this->t('- Missing label -'),
      $this->dateRecurInterpreterStorage->loadMultiple()
    );
    $form['interpreter'] = [
      '#type' => 'select',
      '#title' => $this->t('Recurring date interpreter'),
      '#description' => $this->t('Choose a plugin for converting rules into a human readable description.'),
      '#default_value' => $this->getSetting('interpreter'),
      '#options' => $interpreterOptions,
      '#required' => FALSE,
      '#empty_option' => $this->t('- Do not show interpreted rule -'),
    ];

    $form['occurrences'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['container-inline']],
      '#tree' => FALSE,
    ];

    $form['occurrences']['show_next'] = [
      '#field_prefix' => $this->t('Show maximum of'),
      '#field_suffix' => $this->t('occurrences'),
      '#type' => 'number',
      '#min' => 0,
      '#default_value' => $this->getSetting('show_next'),
      '#attributes' => ['size' => 4],
      '#element_validate' => [[static::class, 'validateSettingsShowNext']],
    ];

    $form['occurrences']['count_per_item'] = [
      '#type' => 'select',
      '#options' => [
        static::COUNT_PER_ITEM_ITEM => $this->t('per field item'),
        static::COUNT_PER_ITEM_ALL => $this->t('across all field items'),
      ],
      '#default_value' => $this->getSetting('count_per_item') ? static::COUNT_PER_ITEM_ITEM : static::COUNT_PER_ITEM_ALL,
      '#element_validate' => [[static::class, 'validateSettingsCountPerItem']],
    ];

    return $form;
  }

  /**
   * Validation callback for count_per_item.
   *
   * @param array $element
   *   The element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  public static function validateSettingsCountPerItem(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $countPerItem = $element['#value'] == static::COUNT_PER_ITEM_ITEM;
    $arrayParents = array_slice($element['#array_parents'], 0, -2);
    $formatterForm = NestedArray::getValue($complete_form, $arrayParents);
    $parents = $formatterForm['#parents'];
    $parents[] = 'count_per_item';
    $form_state->setValue($parents, $countPerItem);
  }

  /**
   * Validation callback for show_next.
   *
   * @param array $element
   *   The element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  public static function validateSettingsShowNext(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $arrayParents = array_slice($element['#array_parents'], 0, -2);
    $formatterForm = NestedArray::getValue($complete_form, $arrayParents);
    $parents = $formatterForm['#parents'];
    $parents[] = 'show_next';
    $form_state->setValue($parents, $element['#value']);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $this->formatType = $this->getSetting('format_type');
    $summary = parent::settingsSummary();

    $countPerItem = $this->getSetting('count_per_item');
    $showOccurrencesCount = $this->getSetting('show_next');
    if ($showOccurrencesCount > 0) {
      $summary[] = $this->formatPlural(
        $showOccurrencesCount,
        'Show maximum of @count occurrence @per',
        'Show maximum of @count occurrences @per',
        ['@per' => $countPerItem ? $this->t('per field item') : $this->t('across all field items')]
      );
    }

    $start = new DrupalDateTime('today 9am');
    $endSameDay = clone $start;
    $endSameDay->setTime(17, 0, 0);
    $summary['sample_same_day'] = [
      '#type' => 'inline_template',
      '#template' => '{{ label }}: {{ sample }}',
      '#context' => [
        'label' => $this->t('Same day range'),
        'sample' => $this->buildDateRangeValue($start, $endSameDay, TRUE),
      ],
    ];
    $endDifferentDay = clone $endSameDay;
    $endDifferentDay->modify('+1 day');
    $summary['sample_different_day'] = [
      '#type' => 'inline_template',
      '#template' => '{{ label }}: {{ sample }}',
      '#context' => [
        'label' => $this->t('Different day range'),
        'sample' => $this->buildDateRangeValue($start, $endDifferentDay, TRUE),
      ],
    ];

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    // Whether maximum is per field item or in total.
    $isSharedMaximum = !$this->getSetting('count_per_item');
    // Maximum amount of occurrences to be displayed.
    $occurrenceQuota = (int) $this->getSetting('show_next');

    $elements = [];
    foreach ($items as $delta => $item) {
      $value = $this->viewItem($item, $occurrenceQuota);
      $occurrenceQuota -= ($isSharedMaximum ? count($value['#occurrences']) : 0);
      $elements[$delta] = $value;
      if ($occurrenceQuota <= 0) {
        break;
      }
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for a field item.
   *
   * @param \Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem $item
   *   A field item.
   * @param int $maxOccurrences
   *   Maximum number of occurrences to show for this field item.
   *
   * @return array
   *   A render array for a field item.
   */
  protected function viewItem(DateRecurItem $item, $maxOccurrences): array {
    $theme_suggestions = implode('__', [
      $this->viewMode,
      $item->getEntity()->getEntityTypeId(),
      $item->getEntity()->bundle(),
      $item->getFieldDefinition()->getName(),
    ]);

    $cacheability = new CacheableMetadata();
    $build = [
      '#theme' => 'un_date_date_recur_basic__' . $theme_suggestions,
      '#is_recurring' => $item->isRecurring(),
    ];

    /** @var \Drupal\date_recur\DateRange $first_upcoming */
    $first_upcoming = $this->getFirstUpcoming($item);
    $startDate = $first_upcoming->getStart();

    /** @var \Drupal\Core\Datetime\DrupalDateTime|null $endDate */
    $endDate = $first_upcoming->getEnd() ?? $startDate;
    if (!$startDate || !$endDate) {
      return $build;
    }

    $build = array_merge($this->buildDateRangeValue($startDate, $endDate, FALSE), $build);

    // Render the rule.
    if ($item->isRecurring() && $this->getSetting('interpreter')) {
      /** @var string|null $interpreterId */
      $interpreterId = $this->getSetting('interpreter');
      if ($interpreterId && ($interpreter = $this->dateRecurInterpreterStorage->load($interpreterId))) {
        assert($interpreter instanceof DateRecurInterpreterInterface);
        $rules = $item->getHelper()->getRules();
        $plugin = $interpreter->getPlugin();
        $cacheability->addCacheableDependency($interpreter);
        $build['#interpretation'] = $plugin->interpret($rules, 'en');
      }
    }

    // Occurrences are generated even if the item is not recurring.
    $build['#occurrences'] = array_map(
      function (DateRange $occurrence) use ($theme_suggestions): array {
        $startDate = DrupalDateTime::createFromDateTime($occurrence->getStart());
        $endDate = DrupalDateTime::createFromDateTime($occurrence->getEnd());
        return $this->buildDateRangeValue(
          $startDate,
          $endDate,
          TRUE,
          $theme_suggestions
        );
      },
      $this->getOccurrences($item, $maxOccurrences)
    );

    $cacheability->applyTo($build);
    return $build;
  }

  /**
   * Get first upcoming occurrence of a date.
   */
  protected function getFirstUpcoming(DateRecurItem $item) {
    if (!$item->isRecurring()) {
      return $item->getHelper()->getOccurrences()[0];
    }

    // Generate all occurences.
    $today = new \DateTime('now');
    $today->setTime(0, 0, 0, 0);
    foreach ($item->getHelper()->getOccurrences($today, NULL, 99) as $date) {
      if ($date->getStart()->getTimestamp() >= $today->getTimestamp()) {
        return $date;
      }
    }

    // Return first one, if no future one is found.
    return $item->getHelper()->getOccurrences()[0];
  }

  /**
   * Builds a date range suitable for rendering.
   */
  protected function buildDateRangeValue(DrupalDateTime|\DateTimeInterface $start_date, DrupalDateTime|\DateTimeInterface $end_date, $isOccurrence, $theme_suggestions = ''): array {
    $same_date = FALSE;
    $same_day = FALSE;

    $timezone = $start_date->getTimezone();
    $all_day = $this->allDayStartEnd($start_date, $end_date, $timezone);

    if ($start_date->getTimestamp() == $end_date->getTimestamp()) {
      $same_date = TRUE;
    }
    elseif ($this->formatDate($start_date) == $this->formatDate($end_date)) {
      $same_day = TRUE;
    }

    $iso_start_date = $start_date->format('c');
    $iso_end_date = $end_date->format('c') ?? '';

    $build = [
      '#iso_start_date' => $iso_start_date,
      '#iso_end_date' => $iso_end_date,
      '#start_date' => $this->formatDate($start_date),
      '#start_time' => $this->formatTime($start_date),
      '#end_date' => $this->formatDate($end_date),
      '#end_time' => $this->formatTime($end_date),
      '#timezone' => $timezone->getName(),
      '#display_timezone' => TRUE,
      '#same_date' => $same_date,
      '#same_day' => $same_day,
      '#all_day' => $all_day,
      '#cache' => [
        'contexts' => [
          'timezone',
        ],
      ],
    ];

    if ($isOccurrence) {
      $build['#theme'] = 'un_date_occurence__' . $theme_suggestions;
    }

    return $build;
  }

  /**
   * Get the occurrences for a field item.
   *
   * Occurrences are abstracted out to make it easier for extending formatters
   * to change.
   *
   * @param \Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem $item
   *   A field item.
   * @param int $maxOccurrences
   *   Maximum number of occurrences to render.
   *
   * @return \Drupal\date_recur\DateRange[]
   *   A render array.
   */
  protected function getOccurrences(DateRecurItem $item, $maxOccurrences): array {
    $start = new \DateTime('now');
    return $item->getHelper()
      ->getOccurrences($start, NULL, $maxOccurrences);
  }

}