# Magento Module for advanced logging
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-26-orange.svg?style=flat-square)](#contributors-)
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
    <td align="center"><a href="https://rouven.io/"><img src="https://avatars3.githubusercontent.com/u/393419?v=4" width="100px;" alt=""/><br /><sub><b>Rouven Alexander Rieker</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=therouv" title="Code">💻</a></td>
    <td align="center"><a href="https://www.reachdigital.nl/"><img src="https://avatars2.githubusercontent.com/u/1244416?v=4" width="100px;" alt=""/><br /><sub><b>Paul Hachmang</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=paales" title="Code">💻</a></td>
    <td align="center"><a href="https://aelia.co/"><img src="https://avatars1.githubusercontent.com/u/292434?v=4" width="100px;" alt=""/><br /><sub><b>Diego</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=daigo75" title="Code">💻</a></td>
    <td align="center"><a href="http://www.ffuenf.de/"><img src="https://avatars3.githubusercontent.com/u/50462?v=4" width="100px;" alt=""/><br /><sub><b>Achim Rosenhagen</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=arosenhagen" title="Code">💻</a></td>
    <td align="center"><a href="http://www.fabian-blechschmidt.de/"><img src="https://avatars1.githubusercontent.com/u/379680?v=4" width="100px;" alt=""/><br /><sub><b>Fabian Blechschmidt</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=Schrank" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.hipex.io/"><img src="https://avatars1.githubusercontent.com/u/984466?v=4" width="100px;" alt=""/><br /><sub><b>Freek Gruntjes</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=Fgruntjes" title="Code">💻</a></td>
    <td align="center"><a href="https://www.sandstein.de/"><img src="https://avatars2.githubusercontent.com/u/23700116?v=4" width="100px;" alt=""/><br /><sub><b>Wilfried Wolf</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=wilfriedwolf" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/tkdb"><img src="https://avatars3.githubusercontent.com/u/5831065?v=4" width="100px;" alt=""/><br /><sub><b>tkdb</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=tkdb" title="Code">💻</a></td>
    <td align="center"><a href="https://www.diglin.com/"><img src="https://avatars2.githubusercontent.com/u/1337461?v=4" width="100px;" alt=""/><br /><sub><b>Sylvain Rayé</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=sylvainraye" title="Code">💻</a></td>
    <td align="center"><a href="http://www.mb-tec.eu/"><img src="https://avatars2.githubusercontent.com/u/13970869?v=4" width="100px;" alt=""/><br /><sub><b>Matthias Büsing</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=mb-tec" title="Code">💻</a></td>
    <td align="center"><a href="http://avidonline.co.nz/"><img src="https://avatars2.githubusercontent.com/u/924802?v=4" width="100px;" alt=""/><br /><sub><b>Dane Lowe</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=danelowe" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/LeeSaferite"><img src="https://avatars3.githubusercontent.com/u/47386?v=4" width="100px;" alt=""/><br /><sub><b>Lee Saferite</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=LeeSaferite" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/JeroenVanLeusden"><img src="https://avatars2.githubusercontent.com/u/14925052?v=4" width="100px;" alt=""/><br /><sub><b>Jeroen</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=JeroenVanLeusden" title="Code">💻</a></td>
    <td align="center"><a href="https://steverobbins.com/"><img src="https://avatars0.githubusercontent.com/u/3498562?v=4" width="100px;" alt=""/><br /><sub><b>Steve Robbins</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=steverobbins" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/sergeykalenyuk"><img src="https://avatars0.githubusercontent.com/u/1863773?v=4" width="100px;" alt=""/><br /><sub><b>Sergey Kalenyuk</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=sergeykalenyuk" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/nhp"><img src="https://avatars3.githubusercontent.com/u/512911?v=4" width="100px;" alt=""/><br /><sub><b>Nils Preuß</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=nhp" title="Code">💻</a></td>
    <td align="center"><a href="https://www.matthias-zeis.com/"><img src="https://avatars2.githubusercontent.com/u/371060?v=4" width="100px;" alt=""/><br /><sub><b>Matthias Zeis</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=mzeis" title="Code">💻</a></td>
    <td align="center"><a href="http://www.jeroenvermeulen.eu/"><img src="https://avatars1.githubusercontent.com/u/658024?v=4" width="100px;" alt=""/><br /><sub><b>Jeroen Vermeulen</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=jeroenvermeulen" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/infabo"><img src="https://avatars0.githubusercontent.com/u/3999104?v=4" width="100px;" alt=""/><br /><sub><b>Ingo Fabbri</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=infabo" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.colinodell.com/"><img src="https://avatars1.githubusercontent.com/u/202034?v=4" width="100px;" alt=""/><br /><sub><b>Colin O'Dell</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=colinodell" title="Code">💻</a></td>
    <td align="center"><a href="https://www.mothership.de/"><img src="https://avatars1.githubusercontent.com/u/1199310?v=4" width="100px;" alt=""/><br /><sub><b>Andreas</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=andreasemer" title="Code">💻</a></td>
    <td align="center"><a href="http://www.aadmathijssen.nl/"><img src="https://avatars0.githubusercontent.com/u/3796971?v=4" width="100px;" alt=""/><br /><sub><b>Aad Mathijssen</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=aadmathijssen" title="Code">💻</a></td>
    <td align="center"><a href="http://www.proxiblue.com.au/"><img src="https://avatars2.githubusercontent.com/u/4994260?v=4" width="100px;" alt=""/><br /><sub><b>Lucas van Staden</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=ProxiBlue" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/kkrieger85"><img src="https://avatars2.githubusercontent.com/u/4435523?v=4" width="100px;" alt=""/><br /><sub><b>Kevin Krieger</b></sub></a><br /><a href="https://github.com/firegento/firegento-logger/commits?author=kkrieger85" title="Documentation">📖</a></td>
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

