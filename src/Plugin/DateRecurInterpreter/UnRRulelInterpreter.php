<?php

declare(strict_types = 1);

namespace Drupal\un_date\Plugin\DateRecurInterpreter;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\DependencyTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\date_recur\Plugin\DateRecurInterpreterPluginBase;
use Drupal\un_date\Trait\UnDateTimeTrait;
use Drupal\un_date\UnRRuleHumanReadable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an interpreter.
 *
 * @DateRecurInterpreter(
 *  id = "un_rl",
 *  label = @Translation("RRule interpreter (UN)"),
 * )
 *
 * @ingroup RLanvinPhpRrule
 */
final class UnRRulelInterpreter extends DateRecurInterpreterPluginBase implements ContainerFactoryPluginInterface, PluginFormInterface {

  use DependencyTrait;
  use UnDateTimeTrait;

  /**
   * Constructs a new RlInterpreter.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $dateFormatStorage
   *   The date format storage.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, protected DateFormatterInterface $dateFormatter, protected EntityStorageInterface $dateFormatStorage) {
    parent::__construct([], $plugin_id, $plugin_definition);
    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('date.formatter'),
      $container->get('entity_type.manager')->getStorage('date_format')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'display_timezone' => TRUE,
      'month_format' => 'numeric',
      'show_start_date' => TRUE,
      'show_until' => TRUE,
      'show_infinite' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function interpret(array $rules, string $language, ?\DateTimeZone $timeZone = NULL): string {
    $pluginConfig = $this->getConfiguration();

    if (!in_array($language, $this->supportedLanguages())) {
      throw new \Exception('Language not supported.');
    }

    $options = [
      'use_intl' => TRUE,
      'locale' => un_date_current_language(),
      'include_start' => $pluginConfig['show_start_date'],
      'include_until' => $pluginConfig['show_until'],
      'explicit_infinite' => $pluginConfig['show_infinite'],
    ];

    $month_format = $this->getSetting('month_format');
    $dateFormatter = function (\DateTimeInterface $date) use ($month_format) : string {
      return $this->formatDate($date, $month_format);
    };
    $options['date_formatter'] = $dateFormatter;

    $strings = [];
    foreach ($rules as $rule) {
      $rrule = new UnRRuleHumanReadable($rule->getParts());
      $strings[] = $rrule->humanReadable($options);
    }

    return implode(', ', $strings);
  }

  /**
   * Wrapper for configuration.
   */
  public function getSetting(string $name) {
    return $this->configuration[$name] ?? '';
  }

  /**
   * Set a configuration.
   */
  public function setSetting(string $name, mixed $value) {
    $this->configuration[$name] = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['display_timezone'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display Timezone'),
      '#description' => $this->t('Should we display the timezone after the formatted date?'),
      '#default_value' => $this->configuration['display_timezone'],
    ];

    $form['month_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Month format'),
      '#options' => $this->monthFormats,
      '#description' => $this->t('In which format will the month be displayed'),
      '#default_value' => $this->configuration['month_format'],
    ];

    $form['show_start_date'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show the start date'),
      '#default_value' => $this->configuration['show_start_date'],
    ];

    $form['show_until'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show the until date'),
      '#default_value' => $this->configuration['show_until'],
    ];

    $form['show_infinite'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show infinite if infinite.'),
      '#default_value' => $this->configuration['show_infinite'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['show_start_date'] = $form_state->getValue('show_start_date');
    $this->configuration['show_until'] = $form_state->getValue('show_until');
    $this->configuration['date_format'] = $form_state->getValue('date_format');
    $this->configuration['show_infinite'] = $form_state->getValue('show_infinite');
  }

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public function calculateDependencies(): array {
    /** @var string $dateFormatId */
    $dateFormatId = $this->configuration['date_format'];
    $dateFormat = $this->dateFormatStorage->load($dateFormatId);
    if ($dateFormat) {
      $this->addDependency('config', $dateFormat->getConfigDependencyName());
    }
    return $this->dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public function supportedLanguages(): array {
    return [
      'en',
      'es',
      'fr',
      'ar',
      'zh-hans',
    ];
  }

}
