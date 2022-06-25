<?php

declare(strict_types=1);

namespace Drupal\omnipedia_search\Cache\Context;

use Drupal\Core\Cache\Context\CalculatedCacheContextInterface;
use Drupal\omnipedia_search\Service\WikiSearchInterface;

/**
 * Defines the Omnipedia is wiki search page cache context service.
 *
 * Cache context ID: 'omnipedia_is_wiki_search_page'.
 *
 * This allows for caching to vary on whether the current route is a wiki search
 * page.
 */
class IsWikiSearchPageCacheContext implements CalculatedCacheContextInterface {

  /**
   * The Omnipedia wiki search service.
   *
   * @var \Drupal\omnipedia_search\Service\WikiSearchInterface
   */
  protected WikiSearchInterface $wikiSearch;

  /**
   * Service constructor; saves dependencies.
   *
   * @param \Drupal\omnipedia_search\Service\WikiSearchInterface $wikiSearch
   *   The Omnipedia wiki search service.
   */
  public function __construct(
    WikiSearchInterface $wikiSearch
  ) {
    $this->wikiSearch = $wikiSearch;
  }

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return \t('Omnipedia is wiki search page');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext($parameter = null) {
    if ($this->wikiSearch->isCurrentRouteSearchPage()) {
      return 'is_wiki_search_page';
    }

    return 'is_not_wiki_search_page';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($parameter = null) {
    return new CacheableMetadata();
  }
}
