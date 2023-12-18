<?php

declare(strict_types = 1);

namespace Drupal\un_date;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Defines a date range.
 */
class UnDateRange {

  /**
   * The start date.
   *
   * @var \DateTimeInterface
   * @phpcs:disable Drupal.NamingConventions.ValidVariableName.LowerCamelName
   * @phpcs:disable Drupal.Commenting.VariableComment.Missing
   */
  public readonly \DateTimeInterface $start_date;

  /**
   * The end date.
   *
   * @var \DateTimeInterface
   * @phpcs:disable Drupal.NamingConventions.ValidVariableName.LowerCamelName
   * @phpcs:disable Drupal.Commenting.VariableComment.Missing
   */
  public readonly \DateTimeInterface $end_date;

  /**
   * Creates a new DateRange.
   */
  public function __construct(\DateTimeInterface|DrupalDateTime|string $start, \DateTimeInterface|DrupalDateTime|string $end) {
    $this->start_date = clone $this->getDateTime($start);
    $this->end_date = clone $this->getDateTime($end);
    $this->validateDates();
  }

  /**
   * Get datatime.
   */
  protected function getDateTime(\DateTimeInterface|DrupalDateTime|string $date) {
    if (is_string($date)) {
      return new \DateTime($date);
    }

    if ($date instanceof DrupalDateTime) {
      return $date->getPhpDateTime();
    }

    return $date;
  }

  /**
   * Validates the start and end dates.
   *
   * @throws \InvalidArgumentException
   *   When there is a problem with the start and/or end date.
   */
  protected function validateDates(): void {
    // Normalize end date timezone.
    if ($this->start_date->getTimezone()->getName() !== $this->end_date->getTimezone()->getName()) {
      throw new \InvalidArgumentException('Provided dates must be the same timezone.');
    }

    if ($this->end_date < $this->start_date) {
      throw new \InvalidArgumentException('End date must not occur before start date.');
    }
  }

}
