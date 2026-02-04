<?php

declare(strict_types=1);

namespace Drupal\omnipedia_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Template\Attribute;
use Drupal\omnipedia_search\Service\WikiSearchInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\ViewExecutableFactory;
use function is_object;

/**
 * Omnipedia header hooks.
 */
class OmnipediaHeaderHooks {

  /**
   * Constructor; saves dependencies.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   *
   * @param \Drupal\views\ViewExecutableFactory $viewsExecutableFactory
   *   The Views executable factory.
   *
   * @param \Drupal\omnipedia_search\Service\WikiSearchInterface $wikiSearch
   *   The Omnipedia wiki search service.
   */
  public function __construct(
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly ViewExecutableFactory $viewsExecutableFactory,
    protected readonly WikiSearchInterface $wikiSearch,
  ) {}

  /**
   * Load and return the wiki search view executable.
   *
   * @return \Drupal\views\ViewExecutable|null
   *   The wiki search view executable or null if it can't be loaded.
   *
   * @see \Drupal\views\Views::getView()
   *   We load the view like in this static method except using dependency
   *   injection.
   */
  protected function getSearchViewExecutable(): ?ViewExecutable {

    /** @var \Drupal\views\ViewEntityInterface|null */
    $viewEntity = $this->entityTypeManager->getStorage('view')->load(
      'wiki_search',
    );

    if (!is_object($viewEntity)) {
      return null;
    }

    return $this->viewsExecutableFactory->get($viewEntity);

  }

  /**
   * Get the wiki search form.
   *
   * @return array
   *   The form render array or an empty array on error.
   */
  protected function getSearchForm(): array {

    // Don't display the search form on the wiki search page as it's redundant.
    if ($this->wikiSearch->isCurrentRouteSearchPage()) {
      return [];
    }

    /** @var \Drupal\views\ViewExecutable|null */
    $viewExecutable = $this->getSearchViewExecutable();

    if (!is_object($viewExecutable)) {
      return [];
    }

    // We have to build the display to ensure that various handlers are
    // initialized so that we don't cause any errors when building the form.
    $viewExecutable->build('page');

    return $viewExecutable->getDisplay()->getPlugin(
      'exposed_form',
    )->renderExposedForm(true);

  }

  /**
   * Alter the 'omnipedia_header' block build array.
   *
   * This adds search cache metadata.
   */
  #[Hook('block_build_omnipedia_header_alter')]
  public function blockBuildAlter(
    array &$build,
    BlockPluginInterface $block,
  ): void {

    /** @var \Drupal\views\ViewExecutable|null */
    $viewExecutable = $this->getSearchViewExecutable();

    if (is_object($viewExecutable)) {

      $build['#cache']['tags'] = Cache::mergeTags(
        $build['#cache']['tags'],
        $viewExecutable->getCacheTags(),
      );

    }

    // We want to add this context regardless of whether a search form was
    // successfully generated.
    $build['#cache']['contexts'] = Cache::mergeContexts(
      $build['#cache']['contexts'],
      ['omnipedia_is_wiki_search_page'],
    );

  }

  /**
   * Alter the theme registry.
   *
   * This adds the 'search_form' variable to the 'omnipedia_header' element.
   */
  #[Hook('theme_registry_alter')]
  public function themeRegistrAlter(array &$themeRegistry): void {

    if (
      !isset($themeRegistry['omnipedia_header']) ||
      isset($themeRegistry['omnipedia_header']['variables']['search_form'])
    ) {
      return;
    }

    $themeRegistry['omnipedia_header']['variables']['search_form'] = [];

  }

  /**
   * Prepares variables for the omnipedia-header.html.twig template.
   *
   * This generates the search form and sets it as the 'search_form' variable.
   */
  #[Hook('preprocess_omnipedia_header')]
  public function preprocessHeader(array &$variables): void {

    if (!empty($variables['search_form'])) {
      return;
    }

    /** @var array */
    $searchForm = $this->getSearchForm();

    if (empty($searchForm)) {
      return;
    }

    if (isset($searchForm['#attributes'])) {
      $searchForm['#attributes'] = new Attribute($searchForm['#attributes']);

    } else {
      $searchForm['#attributes'] = new Attribute();
    }

    $variables['search_form'] = $searchForm;

  }

}
