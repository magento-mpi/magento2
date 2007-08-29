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
/**
 * Update configuration values to support new locations of design packages
 */

truncate table `core_config_data`;

insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
(1,'default',0,'general/currency/base','USD','',1),
(2,'default',0,'general/currency/allow','USD,CAD,UAH,RUB','',1),
(3,'default',0,'general/currency/default','USD','',1),
(4,'default',0,'general/local/language','en','',1),
(6,'default',0,'system/filesystem/layout','{{app_dir}}/design/frontend/default/layout/default','',1),
(7,'default',0,'system/filesystem/template','{{app_dir}}/design/frontend/default/template/default','',1),
(8,'default',0,'system/filesystem/translate','{{app_dir}}/design/frontend/default/translate','',1),
(9,'default',0,'system/filesystem/base','{{root_dir}}','',1),
(10,'default',0,'system/filesystem/media','{{root_dir}}/media','',1),
(11,'default',0,'system/filesystem/skin','{{root_dir}}/skins/default','',1),
(12,'default',0,'web/unsecure/protocol','{{protocol}}','',1),
(13,'default',0,'web/unsecure/host','{{host}}','',1),
(14,'default',0,'web/unsecure/port','{{port}}','',1),
(15,'default',0,'web/unsecure/base_path','{{base_path}}','',1),
(16,'default',0,'web/secure/protocol','{{protocol}}','',1),
(17,'default',0,'web/secure/host','{{host}}','',1),
(18,'default',0,'web/secure/port','{{port}}','',1),
(19,'default',0,'web/secure/base_path','{{base_path}}','',1),
(20,'default',0,'web/url/media','{{base_path}}media/','',1),
(21,'default',0,'web/url/skin','{{base_path}}skins/default/','',1),
(22,'default',0,'web/url/js','{{base_path}}js/','',1),
(23,'default',0,'system/filesystem/etc','{{app_dir}}/etc/','',1),
(24,'default',0,'system/filesystem/code','{{app_dir}}/code/','',1),
(25,'default',0,'system/filesystem/upload','{{root_dir}}/media/upload/','',1),
(26,'default',0,'system/filesystem/var','{{var_dir}}','',1),
(27,'default',0,'system/filesystem/session','{{var_dir}}/session/','',1),
(28,'default',0,'system/filesystem/cache_config','{{var_dir}}/cache/config/','',1),
(29,'default',0,'system/filesystem/cache_layout','{{var_dir}}/cache/layout/','',1),
(32,'default',0,'web/default/front','catalog','',1),
(34,'default',0,'web/default/no_route','core/index/noRoute','',1),
(35,'default',0,'general/country/default','US','CA',1),
(36,'default',0,'general/country/allow','US,CA,UA','',1),
(39,'default',0,'advanced/datashare/customer','1','',1),
(40,'default',0,'advanced/datashare/customer_address','1','',1),
(41,'default',0,'advanced/datashare/quote','1','',1),
(42,'default',0,'advanced/datashare/quote_address','1','',1),
(43,'default',0,'advanced/datashare/order','1','',1),
(44,'default',0,'advanced/datashare/order_address','1','',1),
(45,'default',0,'advanced/datashare/order_payment','1','',1),
(46,'default',0,'advanced/datashare/wishlist','1','',1),
(47,'default',0,'general/local/date_format_short','%m/%d/%y','',1),
(48,'default',0,'general/local/date_format_medium','%a, %b %e %Y','',1),
(49,'default',0,'general/local/date_format_long','%A, %B %e %Y','',1),
(50,'default',0,'general/local/datetime_format_short','%m/%d/%y [%I:%M %p]','',1),
(51,'default',0,'general/local/datetime_format_medium','%a, %b %e %Y [%I:%M %p]','',1),
(52,'default',0,'general/local/datetime_format_long','%A, %B %e %Y [%I:%M %p]','',1),
(53,'default',0,'paygate/authorizenet/test','1','0',0);