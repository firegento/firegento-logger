# Magento Advanced Logging

The purpose of this project is to have a simple framework for different logging adapters.

## Usage

Install the module via modman `modman clone git@github.com:magento-hackathon/Logger.git`

Refresh the caches afterwards.

Then configure the different loggers in `System > Configuration > Advanced > Advanced Logger`

## Further Information

### Core Contributors

* Karl Spies
* Christoph
* Christian
* Claas
* Damian Luszczymak
* Colin
* Marco Becker
* Nicolai Essig

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
- Chromelogger

It is possible to use **Multiple-Targets**!

### Other Features
- Log Live View (Like a tail in terminal)
- Report View (Shows content of a report in backend)

### Further work

### External libraries

For XMPP we use https://github.com/cweiske/xmpphp.
