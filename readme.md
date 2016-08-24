DHL Online Retoure Extension
============================

The DHL Online Retoure extension for Magento enables customers to create return
shipping labels for their orders.

Facts
-----
- version: 1.0.0
- extension key: Dhl_OnlineRetoure
- [extension on Magento Connect](http://www.magentocommerce.com/magento-connect/dhl-onlineretoure-1234.html)
- Magento Connect 2.0 extension key: http://connect20.magentocommerce.com/community/Dhl_OnlineRetoure
- [extension on GitLab](https://git.netresearch.de/dhl/online-retoure-m1)
- [direct download link](http://connect.magentocommerce.com/community/get/Dhl_OnlineRetoure-1.0.0.tgz)

Description
-----------
This extension integrates a _Return Order_ button on the order page in the
customer account section _My Orders_. The customer then confirms his sender
address and retrieves the return shipping label.

Optionally, a link to the address confirmation page can be added to the
transactional emails.

Requirements
------------
- PHP >= 5.5.0

Compatibility
-------------
- Magento CE >= 1.7

Installation Instructions
-------------------------

1. Install the extension via Magento Connect with the key shown above or install
   via composer / modman.
2. Clear the cache, logout from the admin panel and then login again.

More information on configuration and integration into custom themes can be found
in the documentation.

Uninstallation
--------------
1. Remove all extension files from your Magento installation
2. Revoke block permissions at `System → Permissions → Blocks`.
3. Clean up the database.


    DELETE FROM `core_config_data` WHERE `path` LIKE 'shipping/dhlonlineretoure/%';
    
    DELETE FROM `core_resource` WHERE `code` = 'dhl_onlineretoure_setup';

Support
-------
In case of questions or problems, please have a look at the
[Support Portal (FAQ)](http://dhl.support.netresearch.de/) first.

If the issue cannot be resolved, you can contact the support team via the
[Support Portal](http://dhl.support.netresearch.de/) or by sending an email
to <dhl.support@netresearch.de>.

Developer
---------
Christoph Aßmann | [Netresearch GmbH & Co. KG](http://www.netresearch.de/) | [@mam08ixo](https://twitter.com/mam08ixo)

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2013 Netresearch GmbH & Co. KG
(c) 2016 DHL Paket GmbH
