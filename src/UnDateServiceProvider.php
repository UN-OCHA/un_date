<?php

namespace Drupal\un_date;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the date formatter service.
 */
class UnDateServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('date.formatter');
    $definition->setClass(UnDateFormatter::class);
  }
}
