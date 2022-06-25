<?php

declare(strict_types=1);

namespace Drupal\omnipedia_search\Service;

use Drupal\Core\Routing\StackedRouteMatchInterface;
use Drupal\omnipedia_search\Service\WikiSearchInterface;

/**
 * The Omnipedia wiki search service.
 */
class WikiSearch implements WikiSearchInterface {

  /**
   * The Drupal current route match service.
   *
   * @var \Drupal\Core\Routing\StackedRouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * Constructs this service object.
   *
   * @param \Drupal\Core\Routing\StackedRouteMatchInterface $currentRouteMatch
   *   The Drupal current route match service.
   */
  public function __construct(
    StackedRouteMatchInterface $currentRouteMatch
  ) {
    // Save dependencies.
    $this->currentRouteMatch = $currentRouteMatch;
  }

  /**
   * {@inheritdoc}
   */
  public function isCurrentRouteSearchPage(): bool {
    return $this->currentRouteMatch->getRouteName() === 'view.wiki_search.page';
  }

}
