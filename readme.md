This contains the source files for the "*Omnipedia - Search*" Drupal module,
which provides search-related functionality for
[Omnipedia](https://omnipedia.app/).

⚠️ ***[Why open source? / Spoiler warning](https://omnipedia.app/open-source)***

----

# Description

This contains a cache context, [Search
API](https://www.drupal.org/project/search_api) processor plug-ins, a service to
determine if the current route is a wiki search route, and some  configuration
that defines our Search API index and server, along with the View that displays
search results.

----

# Requirements

* [Drupal 9.5 or 10](https://www.drupal.org/download) ([Drupal 8 is end-of-life](https://www.drupal.org/psa-2021-11-30))

* PHP 8.1

* [Composer](https://getcomposer.org/)


## Drupal dependencies

Before attempting to install this, you must add the Composer repositories as
described in the installation instructions for these dependencies:

* The [`omnipedia_content`](https://github.com/neurocracy/drupal-omnipedia-content), [`omnipedia_core`](https://github.com/neurocracy/drupal-omnipedia-core), [`omnipedia_date`](https://github.com/neurocracy/drupal-omnipedia-date), and [`omnipedia_main_page`](https://github.com/neurocracy/drupal-omnipedia-main-page) modules.

----

# Installation

## Composer

### Set up

Ensure that you have your Drupal installation set up with the correct Composer
installer types such as those provided by [the `drupal/recommended-project`
template](https://www.drupal.org/docs/develop/using-composer/starting-a-site-using-drupal-composer-project-templates#s-drupalrecommended-project).
If you're starting from scratch, simply requiring that template and following
[the Drupal.org Composer
documentation](https://www.drupal.org/docs/develop/using-composer/starting-a-site-using-drupal-composer-project-templates)
should get you up and running.

### Repository

In your root `composer.json`, add the following to the `"repositories"` section:

```json
"drupal/omnipedia_search": {
  "type": "vcs",
  "url": "https://github.com/neurocracy/drupal-omnipedia-search.git"
}
```

### Installing

Once you've completed all of the above, run `composer require
"drupal/omnipedia_search:4.x-dev@dev"` in the root of your project to have
Composer install this and its required dependencies for you.

----

# Major breaking changes

The following major version bumps indicate breaking changes:

* 4.x - Requires Drupal 9.5 or [Drupal 10](https://www.drupal.org/project/drupal/releases/10.0.0).
