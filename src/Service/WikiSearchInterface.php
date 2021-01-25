<?php

namespace Drupal\omnipedia_search\Service;

/**
 * The Omnipedia wiki search service interface.
 */
interface WikiSearchInterface {

  /**
   * Determine if the current route is a wiki search page.
   *
   * @return boolean
   *   True if the current route is a wiki search page; false otherwise.
   */
  public function isCurrentRouteSearchPage(): bool;

}
