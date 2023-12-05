<?php

namespace Drupal\Tests\un_date\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\Core\Form\FormState;
use Drupal\Tests\datetime\Functional\DateTestBase;

/**
 * Tests Datetime field functionality.
 *
 * @group datetime
 */
class DateTimeFieldTest extends DateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['un_date', 'node', 'entity_test', 'datetime', 'field_ui'];

  /**
   * The default display settings to use for the formatters.
   *
   * @var array
   */
  protected $defaultSettings = ['timezone_override' => ''];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function getTestFieldType() {
    return 'datetime';
  }

  /**
   * Tests date field functionality.
   */
  public function testDateField() {
    $field_name = $this->fieldStorage->getName();

    // Loop through defined timezones to test that date-only fields work at the
    // extremes.
    foreach (static::$timezones as $timezone) {
      $this->setSiteTimezone($timezone);
      $this->assertEquals($timezone, $this->config('system.date')->get('timezone.default'), 'Time zone set to ' . $timezone);

      // Display creation form.
      $this->drupalGet('entity_test/add');

      // Build up a date in the UTC timezone. Note that using this will also
      // mimic the user in a different timezone simply entering '2012-12-31' via
      // the UI.
      $value = '2012-12-31 00:00:00';
      $date = new DrupalDateTime($value, DateTimeItemInterface::STORAGE_TIMEZONE);

      // Submit a valid date and ensure it is accepted.
      $date_format = DateFormat::load('html_date')->getPattern();
      $time_format = DateFormat::load('html_time')->getPattern();

      $edit = [
        "{$field_name}[0][value][date]" => $date->format($date_format),
      ];
      $this->submitForm($edit, 'Save');
      preg_match('|entity_test/manage/(\d+)|', $this->getUrl(), $match);
      $id = $match[1];
      $this->assertSession()->pageTextContains('entity_test ' . $id . ' has been created.');
      $this->assertSession()->responseContains($date->format($date_format));
      $this->assertSession()->responseNotContains($date->format($time_format));

      // Verify the date doesn't change if using a timezone that is UTC+12 when
      // the entity is edited through the form.
      $entity = EntityTest::load($id);
      $this->assertEquals('2012-12-31', $entity->{$field_name}->value);
      $this->drupalGet('entity_test/manage/' . $id . '/edit');
      $this->submitForm([], 'Save');
      $entity = EntityTest::load($id);
      $this->assertEquals('2012-12-31', $entity->{$field_name}->value);

      // Reset display options since these get changed below.
      $this->displayOptions = [
        'type' => 'un_date_datetime',
        'label' => 'hidden',
        'settings' => ['display_timezone' => TRUE],
      ];

      // Update the entity display settings.
      $this->displayOptions['settings'] = ['display_timezone' => TRUE] + $this->defaultSettings;
      $this->container->get('entity_display.repository')
        ->getViewDisplay($this->field->getTargetEntityTypeId(), $this->field->getTargetBundle(), 'full')
        ->setComponent($field_name, $this->displayOptions)
        ->save();

      $this->renderTestEntity($id);

      /** @var \Drupal\Core\Datetime\DateFormatterInterface $date_formatter */
      $date_formatter = $this->container->get('date.formatter');
      $expected = '31.12.2012';
      $expected_iso = $date_formatter->format($date->getTimestamp(), 'custom', 'c', DateTimeItemInterface::STORAGE_TIMEZONE);
      $output = $this->renderTestEntity($id);
      $this->assertStringContainsString($expected, $output, new FormattableMarkup('Formatted date field displayed as %expected with %expected_iso attribute in %timezone.', [
        '%expected' => $expected,
        '%expected_iso' => $expected_iso,
        '%timezone' => $timezone,
      ]));
      $this->assertStringContainsString($expected_iso, $output, new FormattableMarkup('Formatted date field displayed as %expected with %expected_iso attribute in %timezone.', [
        '%expected' => $expected,
        '%expected_iso' => $expected_iso,
        '%timezone' => $timezone,
      ]));
    }
  }

  /**
   * Tests date and time field.
   */
  public function testDatetimeField() {
    $field_name = $this->fieldStorage->getName();
    // Change the field to a datetime field.
    $this->fieldStorage->setSetting('datetime_type', 'datetime');
    $this->fieldStorage->save();

    // Display creation form.
    $this->drupalGet('entity_test/add');

    // Build up a date in the UTC timezone.
    $value = '2012-12-31 00:00:00';
    $date = new DrupalDateTime($value, DateTimeItemInterface::STORAGE_TIMEZONE);

    // Update the timezone to the system default.
    $date->setTimezone(timezone_open(date_default_timezone_get()));

    // Reset display options since these get changed below.
    $this->displayOptions = [
      'type' => 'un_date_datetime',
      'label' => 'hidden',
      'settings' => ['display_timezone' => TRUE],
    ];

    // Submit a valid date and ensure it is accepted.
    $date_format = DateFormat::load('html_date')->getPattern();
    $time_format = DateFormat::load('html_time')->getPattern();

    $edit = [
      "{$field_name}[0][value][date]" => $date->format($date_format),
      "{$field_name}[0][value][time]" => $date->format($time_format),
    ];
    $this->submitForm($edit, 'Save');
    preg_match('|entity_test/manage/(\d+)|', $this->getUrl(), $match);
    $id = $match[1];
    $this->assertSession()->pageTextContains('entity_test ' . $id . ' has been created.');
    $this->assertSession()->responseContains($date->format($date_format));
    $this->assertSession()->responseContains($date->format($time_format));

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');

    // Verify that the date is output according to the formatter settings.
    $this->displayOptions['settings'] = ['display_timezone' => TRUE] + $this->defaultSettings;
    $display_repository->getViewDisplay($this->field->getTargetEntityTypeId(), $this->field->getTargetBundle(), 'full')
      ->setComponent($field_name, $this->displayOptions)
      ->save();

    $this->renderTestEntity($id);
    // Verify that a date is displayed.
    $date_formatter = $this->container->get('date.formatter');
    $expected = '31.12.2012';
    $expected_iso = $date_formatter->format($date->getTimestamp(), 'custom', 'c', DateTimeItemInterface::STORAGE_TIMEZONE);
    $output = $this->renderTestEntity($id);
    $this->assertStringContainsString($expected, $output, new FormattableMarkup('Formatted date field displayed as %expected with %expected_iso attribute.', ['%expected' => $expected, '%expected_iso' => $expected_iso]));
    $this->assertStringContainsString($expected_iso, $output, new FormattableMarkup('Formatted date field displayed as %expected with %expected_iso attribute.', ['%expected' => $expected, '%expected_iso' => $expected_iso]));
  }

}
