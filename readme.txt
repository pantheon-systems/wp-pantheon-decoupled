=== Pantheon Decoupled ===
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Installs dependencies and example content recommended for decoupled WordPress projects on Pantheon.

== Description ==

Installs the following dependencies for decoupled WordPress projects:
- wp-graphql for GraphQL support
- pantheon-advanced-page-cache and wp-graphql-smart-cache for improved caching and cache purging for decoupled use cases on the Pantheon platform.
- decoupled-preview to preview WordPress content on your front-end site.
- wp-webhooks to trigger front-end webhooks on WordPress events.
- wp-force-login to limit anonymous access to WordPress rendered content.

Also installs example content for decoupled WordPress projects to ensure that common front-end use cases are functional upon install.
* Example pages and posts.
* An example user with access to private content.
* Optionally example content using Advanced Custom Fields

And adds a Pantheon Front-end Sites settings page where users can:
* Configure and test decoupled preview sites
* Access relevant documentation and resources

== Installation ==

Install using composer.

```
composer require pantheon-systems/pantheon-decoupled

```

