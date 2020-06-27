# Magento Module for advanced logging
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-2-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->

The purpose of this project is to have a simple framework for different logging adapters.

Originally developed as Hackathon_Logger but moved forewards and will now actively supported by
firegento community.

See the [**Usage**](#usage) Chapter below to see how to use it.

Please be aware of the following restrictions:

* The ProxiBlue NewRelic extension uses the same logic to log to NewRelic and will block
  FireGento Logger extension unless you [revise its config.xml file](https://github.com/ProxiBlue/NewRelic#compatibility-with-firegento-logger).

Installation Instructions
-------------------------

### Via modman

- Install [modman](https://github.com/colinmollenhour/modman)
- Use the command from your Magento installation folder: `modman clone https://github.com/firegento/firegento-logger`

### Via composer
- Install [composer](http://getcomposer.org/download/)
- Install [Magento Composer](https://github.com/magento-hackathon/magento-composer-installer)
- Create a composer.json into your project like the following sample:

```json
{
    ...
    "require": {
        "firegento/logger":"*"
    },
    "repositories": [
	    {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
    "extra":{
        "magento-root-dir": "./"
    }
}
```

- Then from your `composer.json` folder: `php composer.phar install` or `composer install`

### Manually
- You can copy the files from the folders of this repository to the same folders of your installation


### Installation in ALL CASES
* Clear the cache, logout from the admin panel and then login again.

Uninstallation
--------------
* Remove all extension files from your Magento installation

## Usage

Configure the different loggers in `System > Configuration > Advanced > Firegento Logger`


## Further Information

### Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="http://colin.mollenhour.com/"><img src="https://avatars3.githubusercontent.com/u/38738?v=4" width="100px;" alt=""/><br /><sub><b>Colin Mollenhour</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=colinmollenhour" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/daim2k5"><img src="https://avatars3.githubusercontent.com/u/656150?v=4" width="100px;" alt=""/><br /><sub><b>Damian Luszczymak</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=daim2k5" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!


### Current Status of Project

Complete, working logger interfaces:
- File (Magento default)
- File (Advanced Format)
- E-Mail
- Database
- XMPP (Jabber, Google Talk)
- Graylog2
- RSyslog (UDP)
- Loggly (UDP/HTTPS)
- Papertrail (UDP)
- Chromelogger
- Logstash
- Airbrake

It is possible to use **Multiple-Targets**!

### Other Features
- Log Live View (Like a tail in terminal)
- Report View (Shows content of a report in backend)
- Manage modules log output (enable/disable log messages of extensions)

### Further work

### External libraries

For XMPP we use https://github.com/cweiske/xmpphp.
For ChromeLogger we use https://github.com/ccampbell/chromephp

### How to contribute

Make a fork, commit to develop branch and make a pull request

### Some Hints
* There are combinations that don't work together
  * You can't use Chromelogger with the embeded queueing model, because the queueing takes place after the response
is send to the client
** You can't use Papertrail with the embeded queueing model

Licence
-------
[GNU General Public License, version 3 (GPLv3)](http://opensource.org/licenses/gpl-3.0)

