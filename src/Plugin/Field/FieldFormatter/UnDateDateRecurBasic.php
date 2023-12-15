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
use Drupal\un_date\Plugin\DateRecurInterpreter\UnRRulelInterpreter;
use Drupal\un_date\Trait\UnDateTimeFormatterTrait;
use Drupal\un_date\UnDateRange;
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
  use UnDateTimeFormatterTrait;

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
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    // Maximum amount of occurrences to be displayed.
    $occurrenceQuota = (int) $this->getSetting('show_next');

    $elements = [];
    foreach ($items as $delta => $item) {
      $value = $this->viewItem($item, $occurrenceQuota);
      $elements[$delta] = $value;
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

    $theme = $this->getSetting('template') ?? 'default';
    if ($theme == 'default') {
      $theme = 'un_date_date_recur_basic';
    }

    $cacheability = new CacheableMetadata();
    $build = [
      '#theme' => $theme . '__' . $theme_suggestions,
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

        if ($plugin instanceof UnRRulelInterpreter) {
          $plugin->setSetting('display_timezone', $this->getSetting('display_timezone'));
          $plugin->setSetting('month_format', $this->getSetting('month_format'));
        }

        $build['#interpretation'] = $plugin->interpret($rules, un_date_current_language());
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
      '#parts' => $this->getParts($start_date, $end_date),
      '#daterange' => new UnDateRange($start_date, $end_date),
      '#start' => $start_date,
      '#end' => $end_date,
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
