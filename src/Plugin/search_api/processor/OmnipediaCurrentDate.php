<?php

declare(strict_types=1);

namespace Drupal\omnipedia_search\Plugin\search_api\processor;

use Drupal\omnipedia_core\Entity\Node;
use Drupal\omnipedia_date\Service\TimelineInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Processor\ProcessorInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Exclude wiki nodes from search results not matching the current date.
 *
 * Note that the 'stages' weights are set to specific values to place this
 * processor shortly after the ContentAccess processor to avoid having to check
 * nodes that the user doesn't have access to.
 *
 * @SearchApiProcessor(
 *   id           = "omnipedia_current_date",
 *   label        = @Translation("Omnipedia: current date"),
 *   description  = @Translation("Exclude wiki pages not matching the current date from searches."),
 *   stages       = {
 *     "pre_index_save"   = -5,
 *     "preprocess_query" = -25,
 *   },
 * )
 */
class OmnipediaCurrentDate extends ProcessorPluginBase {

  /**
   * The Omnipedia timeline service.
   *
   * @var \Drupal\omnipedia_date\Service\TimelineInterface
   */
  protected $timeline;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $pluginId, $pluginDefinition
  ) {
    $processor = parent::create(
      $container, $configuration, $pluginId, $pluginDefinition
    );

    $processor->setTimeline($container->get('omnipedia.timeline'));

    return $processor;
  }

  /**
   * Set the Omnipedia timeline service dependency.
   *
   * @param \Drupal\omnipedia_date\Service\TimelineInterface $timeline
   *   The Omnipedia timeline service.
   *
   * @return $this
   */
  public function setTimeline(TimelineInterface $timeline): ProcessorInterface {
    $this->timeline = $timeline;

    return $this;
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
   *
   * This adds the wiki node date field and ensures that it's a string, as
   * trying to treat it as a date can cause errors.
   */
  public function preIndexSave() {
    foreach ($this->index->getDatasources() as $datasourceId => $datasource) {
      /** @var string */
      $entityType = $datasource->getEntityTypeId();

      if ($entityType !== 'node') {
        continue;
      }

      $this->ensureField(
        $datasourceId, Node::getWikiNodeDateFieldName(), 'string'
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preprocessSearchQuery(QueryInterface $query) {
    // Adds the query condition that wiki nodes must have the current date.
    $query->getConditionGroup()->addCondition(
      Node::getWikiNodeDateFieldName(),
      $this->timeline->getDateFormatted('current', 'storage')
    );

    // Add the 'omnipedia_dates' cache context so that cached search results
    // vary by the current date. Note that the wiki search results view must be
    // re-saved after this processor is enabled or disabled because that's when
    // Views recalculates the cache metadata and not on a cache clear/rebuild,
    // counterintuitively enough.
    $query->addCacheContexts(['omnipedia_dates']);
  }

}
