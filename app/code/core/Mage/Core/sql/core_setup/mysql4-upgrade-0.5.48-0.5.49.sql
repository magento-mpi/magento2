/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
UPDATE `core_config_field` SET `frontend_label` = 'Enabled' WHERE `path` ='web_track/google/urchin_enable';
UPDATE `core_config_field` SET `frontend_label` = 'Need to Confirm' WHERE `path` ='newsletter/subscription/confirm';
UPDATE `core_config_field` SET `frontend_label` = 'Need to Confirm' WHERE `path` ='customer/create_account/confirm';
UPDATE `core_config_field` SET `frontend_label` = 'Store Email Addresses' WHERE `path` ='trans_email';
UPDATE `core_config_field` SET `frontend_label` = 'API Login ID' WHERE `path` ='paygate/authorizenet/login';
UPDATE `core_config_field` SET `frontend_label` = 'API Login ID' WHERE `path` ='payment/authorizenet/login';
UPDATE `core_config_field` SET `frontend_label` = 'ZIP/Postal Code' WHERE `path` ='shipping/origin/postcode';
UPDATE `core_config_field` SET `frontend_label` = 'Products per page' WHERE `path` ='atalog/frontend/product_per_page';
