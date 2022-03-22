# sentry integration

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
    * (provided by): guzzlehttp/psr7
  * symfony/polyfill-uuid
* symfony/http-client