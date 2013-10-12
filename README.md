FireGento_Logger
================
This extension provides advanced logging functionalities for Magento.

Facts
-----
- Version: 1.3.0
- Extension key: FireGento_Logger
- [Extension on GitHub](https://github.com/firegento/firegento-logger/)
- Composer name: `firegento/logger` on [packages.firegento.com](http://packages.firegento.com/)

Description
-----------
The purpose of this project is to have a simple framework for different logging adapters.
Originally developed as Hackathon_Logger but moved forewards and will now actively supported by the FireGento community.

### Features
Complete, working logger interfaces:
- File (Magento default)
- File (Advanced Format)
- E-Mail
- Database
- XMPP (Jabber, Google Talk)
- Graylog2
- RSyslog (UDP)
- Loggly (UDP/HTTPS)
- Chromelogger

It is possible to use **Multiple-Targets**!

### Other Features
- Log Live View (Like a tail in terminal)
- Report View (Shows content of a report in backend)

### Roadmap
- Nothing planned, yet!

Installation
------------
1. Install the module via modman `modman clone git@github.com:firegento/firegento-logger.git`
2. Clear the cache, logout from the admin panel and then login again.
3. Configure the differnet loggers in *System > Configuration > Advanced > FireGento Logger*.

External libraries
------------------
For XMPP we use https://github.com/cweiske/xmpphp.

Support
-------
If you encounter any problems or bugs, please create an issue on [GitHub](https://github.com/firegento/firegento-logger/issues).

Contribution
------------
Any contribution to the development of MageSetup is highly welcome. The best possibility to provide any code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
FireGento Team
* Website: [http://firegento.com](http://firegento.com)
* Twitter: [@firegento](https://twitter.com/firegento)

### Core Contributors

* Karl Spies
* Christoph
* Christian
* Claas
* Damian Luszczymak
* Colin
* Marco Becker
* Nicolai Essig
* Daniel Kröger

Licence
-------
[GNU General Public License, version 3 (GPLv3)](http://opensource.org/licenses/gpl-3.0)

Copyright
---------
(c) 2011-2013 FireGento Team
