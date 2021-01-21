<?php

namespace Drupal\omnipedia_search\Plugin\search_api\processor;

use Drupal\search_api\IndexInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;

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
      if (!$node->isWikiNode()) {
        continue;
      }

      if ($node->isHiddenFromSearch()) {
        unset($items[$itemId]);
      }
    }
  }

}
