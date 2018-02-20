UpdateStock on buy event
===

Setup
---
* ```php bin/magento module:enable Twentyone_UpdateStock```
* ```php bin/magento setup:upgrade```
* ```php bin/magento setup:di:compile```

Requirements
---
This project uses ConfigEnv(ahmed-oz/config-env) so to have environment specific variables

Working around
---
This is magento 2 module, it uses to keep stock in sync between atlier and magento, this project has one Console/TestCommand it contains some functions of Observers so they could be tested easily than waiting for magento event.
All Soap calls are managed by ServiceEntity/SoapEntity. 

Events & Observers
---
This module utilises events and observers of magento 2, it is listening to two event of magento order that are defined in etc/events.xml, at these event it fires the observers that does some checks and communicate data with atelier, one of before sales order where you check for availability, if available you proceed with order or else order is cancelled, in after observer you communicate order to atelier.
 
Usage
---
```php bin/magento Twentyone:UpdateStock```
This is handy console command written so to test observer and soap calls, so not just depend on event observers.
