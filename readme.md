This contains the source files for the "*Omnipedia - Search*" Drupal module,
which provides search-related functionality for
[Omnipedia](https://omnipedia.app/).

⚠️⚠️⚠️ ***Here be potential spoilers. Proceed at your own risk.*** ⚠️⚠️⚠️

----

# Why open source?

We're dismayed by how much knowledge and technology is kept under lock and key
in the videogame industry, with years of work often never seeing the light of
day when projects are cancelled. We've gotten to where we are by building upon
the work of countless others, and we want to keep that going. We hope that some
part of this codebase is useful or will inspire someone out there.

----

# Description

This contains a cache context, [Search
API](https://www.drupal.org/project/search_api) processor plug-ins, a service to
determine if the current route is a wiki search route, and some  configuration
that defines our Search API index and server, along with the View that displays
search results.

----

# Planned improvements

* [Enable caching for search results view](https://github.com/neurocracy/drupal-omnipedia-search/issues/1)

----

# Requirements

* [Drupal 9](https://www.drupal.org/download) ([Drupal 8 is end-of-life](https://www.drupal.org/psa-2021-11-30))

* PHP 7.4

* [Composer](https://getcomposer.org/)

## Drupal dependencies

Follow the Composer installation instructions for these dependencies first:

* The [`omnipedia_content`](https://github.com/neurocracy/drupal-omnipedia-content) and [`omnipedia_date`](https://github.com/neurocracy/drupal-omnipedia-date) modules.

----

# Installation

Ensure that you have your Drupal installation set up with the correct Composer
installer types such as those provided by [the ```drupal\recommended-project```
template](https://www.drupal.org/docs/develop/using-composer/starting-a-site-using-drupal-composer-project-templates#s-drupalrecommended-project).
If you're starting from scratch, simply requiring that template and following
[the Drupal.org Composer
documentation](https://www.drupal.org/docs/develop/using-composer/starting-a-site-using-drupal-composer-project-templates)
should get you up and running.

Then, in your root ```composer.json```, add the following to the
```"repositories"``` section:

```json
"drupal/omnipedia_search": {
  "type": "vcs",
  "url": "https://github.com/neurocracy/drupal-omnipedia-search.git"
}
```

Then, in your project's root, run ```composer require
"drupal/omnipedia_search:3.x-dev@dev"``` to have Composer install the module
and its required dependencies for you.
