# Sentry Integration (v3.4.0)

The content of this library provide the standard sentry/sdk without composer. Basically the `Sentry_Autoloader` - class 
is faking the composer autoloader. It is integrated by adding

```php
require_once Mage::getBaseDir('lib') . DS . 'sentry' . DS .  'Autoloader.php';
Sentry_Autoloader::register();
```
to the `Firegento_Logger_Model_Sentry` class. The used packages are written down below.

If you need to update or change http client e.g, just replace the packages and adopt the contents of the following files
where needed.

* autoload_files.php
* classmap.php
* fallback_dirs_psr4.php
* installed.php
* prefix_dirs_psr4.php
* prefix_lengths_psr4.php

and in case of changing packages also the discovery strategy fakers:

    'Composer\\InstalledVersions' => __DIR__ . '/snm/composer-faker/InstalledVersions.php',
    'Http\\Discovery\\Strategy\\CommonClassesStrategy' => __DIR__ . '/snm/discovery-strategy-faker/CommonClassesStrategy.php',
    'Http\\Discovery\\Strategy\\CommonPsr17ClassesStrategy' => __DIR__ . '/snm/discovery-strategy-faker/CommonPsr17ClassesStrategy.php',

for common classes and common psr 17 classes. The Faker for the installed versions should work fine if `installed.php` 
is adopted. 

## Requirements

* http-interop/http-factory-guzzle                                
  * psr/http-factory                                              
    * psr/http-message                                            
  * guzzlehttp/psr7  
    * ralouphie/getallheaders
* sentry/sentry                                                   
  * guzzlehttp/promises
  * jean85/pretty-package-versions                                
  * php-http/async-client-implementation
    * (**provided by**): symfony/http-client                      
      * psr/log                                                   
      * symfony/deprecation-contracts                             
      * symfony/http-client-contracts                             
      * symfony/polyfill-php73                                    
      * symfony/polyfill-php80                                     
      * symfony/service-contracts                                  
        * psr/container   
  * php-http/client-common                                        
    * php-http/httplug                                            
      * php-http/promise                                          
      * psr/http-client 
    * php-http/message
      * clue/stream-filter
      * php-http/message-factory
    * symfony/options-resolver
  * php-http/discovery
  * psr/http-message-implementation
    * (**provided by**): guzzlehttp/psr7
  * symfony/polyfill-uuid
* symfony/http-client