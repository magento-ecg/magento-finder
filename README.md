magento-finder
==============

Magento Finder extends Symfony Finder component and finds special Magento files and directories that can be:

- modules;
- models, controllers, helpers, etc.

Going further, it finds rewrites, cron jobs and events declared, and many more on module or application level.
It uses [PHP-Parser](https://github.com/nikic/PHP-Parser) to work with AST.
