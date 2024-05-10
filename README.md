# **Magento 2 Blog Extension** #

## Description ##

This is a simple blog extension for Magento 2. It supports posts listing in the admin grid under the menu Content / Blog and frontend as well. In the admin section, it is easy to add or edit posts and has multistore support. The extension also has a console command to generate random posts. 

## Features ##

- Module enable/disable

It is a separate module that does not change the default Magento files.

Support:
- Magento Community Edition  2.4.x

- Adobe Commerce 2.4.x

## Installation ##

** Important! Always install and test the extension in your development environment, and not on your live or production server. **

1. Backup Your Data
   Backup your store database and whole Magento 2 directory.

2. Enable extension
   Please use the following commands in your Magento 2 console:

   ```
   bin/magento module:enable Space_Blog

   bin/magento setup:upgrade 
   ```

## Configuration ##

Login to Magento backend (admin panel).  You can find the module configuration here: Stores / Configuration, in the left menu Space Extensions / Space Blog.

Settings:

### Configuration ###

Enable Blog: Here you can enable the extension.

## Additional ##

Example for custom post generation in console:

```
    bin/magento space:blog:generate --count=10
```

## Change Log ##

Version 1.0.0 - May 10, 2024
- Compatibility with Magento Community Edition  2.4.x

- Compatibility with Adobe Commerce 2.4.x

## Support ##

If you have any questions about the extension, please contact with me.

## License ##

MIT License.
