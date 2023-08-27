<?php

declare(strict_types=1);

namespace Drupal\omnipedia_search\Plugin\search_api\processor;

use Drupal\omnipedia_core\Entity\WikiNodeInfo;
use Drupal\omnipedia_core\Service\WikiNodeMainPageInterface;
use Drupal\omnipedia_core\Service\WikiNodeResolverInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Exclude wiki nodes from search results that are marked as hidden.
 *
 * @SearchApiProcessor(
 *   id           = "omnipedia_hidden_wiki_nodes",
 *   label        = @Translation("Omnipedia: hidden wiki pages"),
 *   description  = @Translation("Exclude wiki pages that are marked as hidden from searches."),
 *   stages       = {
 *     "alter_items" = 0,
 *   },
 * )
 */
class OmnipediaHiddenWikiNodes extends ProcessorPluginBase {

  /**
   * The Omnipedia wiki node main page service.
   *
   * @var \Drupal\omnipedia_core\Service\WikiNodeMainPageInterface
   */
  protected readonly WikiNodeMainPageInterface $wikiNodeMainPage;

  /**
   * The Omnipedia wiki node resolver service.
   *
   * @var \Drupal\omnipedia_core\Service\WikiNodeResolverInterface
   */
  protected readonly WikiNodeResolverInterface $wikiNodeResolver;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container, array $configuration, $pluginId,
    $pluginDefinition,
  ) {

    /** @var static $processor */
    $processor = parent::create(
      $container, $configuration, $pluginId, $pluginDefinition,
    );

    $processor->wikiNodeMainPage = $container->get(
      'omnipedia.wiki_node_main_page',
    );

    $processor->wikiNodeResolver = $container->get(
      'omnipedia.wiki_node_resolver',
    );

    return $processor;

  }

  /**
   * {@inheritdoc}
   *
   * Indicate that we only support node indexes.
   *
   * @todo Is there a way to indicate support only for indexes for wiki nodes?
   */
  public static function supportsIndex(IndexInterface $index) {

    foreach ($index->getDatasources() as $datasource) {
      if ($datasource->getEntityTypeId() === 'node') {
        return true;
      }
    }

    return false;

  }

  /**
   * {@inheritdoc}
   */
  public function alterIndexedItems(array &$items) {

    /** @var \Drupal\search_api\Item\ItemInterface $item */
    foreach ($items as $itemId => $item) {

      /** @var \Drupal\omnipedia_core\Entity\NodeInterface */
      $node = $item->getOriginalObject()->getValue();

      // Don't do anything with non-wiki nodes.
      if ($this->wikiNodeResolver->isWikiNode($node) !== true) {
        continue;
      }

      // Main pages are always hidden from search.
      if ($this->wikiNodeMainPage->isMainPage($node) === true) {

        unset($items[$itemId]);

        continue;

      }

      // If the wiki node field is explicitly set to true, hide this node.
      if ($node->get(
        WikiNodeInfo::HIDDEN_FROM_SEARCH_FIELD,
      )->getString() === '1') {

        unset($items[$itemId]);

      }

    }

  }

}
