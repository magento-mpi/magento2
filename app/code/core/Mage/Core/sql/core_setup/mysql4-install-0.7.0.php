<?php
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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `core_config_data`;
CREATE TABLE `core_config_data` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `scope` enum('default','websites','stores','config') NOT NULL default 'default',
  `scope_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default 'general',
  `value` text NOT NULL,
  `old_value` text NOT NULL,
  `inherit` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_scope` (`scope`,`scope_id`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=889 ;

--
-- Dumping data for table `core_config_data`
--

INSERT INTO `core_config_data` (`config_id`, `scope`, `scope_id`, `path`, `value`, `old_value`, `inherit`) VALUES
(138, 'default', 0, 'system/filesystem/layout', '{{app_dir}}/design/frontend/default/layout/default', '', 0),
(139, 'default', 0, 'system/filesystem/template', '{{app_dir}}/design/frontend/default/template/default', '', 0),
(140, 'default', 0, 'system/filesystem/translate', '{{app_dir}}/design/frontend/default/translate', '', 0),
(141, 'default', 0, 'system/filesystem/base', '{{root_dir}}', '', 0),
(142, 'default', 0, 'system/filesystem/media', '{{root_dir}}/media', '', 0),
(143, 'default', 0, 'web/unsecure/protocol', 'http', '{{protocol}}', 0),
(144, 'default', 0, 'web/unsecure/host', '{{host}}', 'demo.magentocommerce.com', 0),
(145, 'default', 0, 'web/unsecure/port', '{{port}}', '80', 0),
(146, 'default', 0, 'web/unsecure/base_path', '{{base_path}}', '/', 0),
(147, 'default', 0, 'web/secure/protocol', 'https', 'https', 0),
(148, 'default', 0, 'web/secure/host', '{{host}}', 'demo.magentocommerce.com', 0),
(149, 'default', 0, 'web/secure/port', '{{port}}', '443', 0),
(150, 'default', 0, 'web/secure/base_path', '{{base_path}}', '/', 0),
(151, 'default', 0, 'web/url/media', '{{base_path}}media/', '/media/', 0),
(152, 'default', 0, 'web/url/js', '{{base_path}}js/', '/js/', 0),
(153, 'default', 0, 'system/filesystem/etc', '{{app_dir}}/etc', '{{app_dir}}/etc/', 0),
(154, 'default', 0, 'system/filesystem/code', '{{app_dir}}/code', '{{app_dir}}/code/', 0),
(155, 'default', 0, 'system/filesystem/upload', '{{root_dir}}/media/upload', '{{root_dir}}/media/upload/', 0),
(156, 'default', 0, 'system/filesystem/var', '{{var_dir}}', '', 0),
(157, 'default', 0, 'system/filesystem/session', '{{var_dir}}/session', '{{var_dir}}/session/', 0),
(158, 'default', 0, 'system/filesystem/cache_config', '{{var_dir}}/cache/config', '{{var_dir}}/cache/config/', 0),
(159, 'default', 0, 'system/filesystem/cache_layout', '{{var_dir}}/cache/layout', '{{var_dir}}/cache/layout/', 0),
(162, 'default', 0, 'general/country/default', 'US', 'US', 0),
(163, 'default', 0, 'general/country/allow', 'US', '', 0),
(178, 'default', 0, 'payment/authorizenet/test', '1', '0', 0),
(179, 'default', 0, 'system/filesystem/design', '{{app_dir}}/design', '', 0),
(180, 'default', 0, 'system/filesystem/skin', '{{root_dir}}/skin', '', 0),
(181, 'default', 0, 'design/package/name', 'default', '', 0),
(182, 'default', 0, 'design/package/area', 'frontend', '', 0),
(183, 'default', 0, 'design/package/theme', 'default', '', 0),
(184, 'default', 0, 'web/url/skin', '{{base_path}}skin/', '/skin/', 0),
(185, 'default', 0, 'web/url/upload', '{{base_path}}media/upload/', '{{base_path}}/media/upload/', 0),
(187, 'default', 0, 'payment/authorizenet/active', '0', '1', 0),
(188, 'default', 0, 'payment/authorizenet/cgi_url', 'https://secure.authorize.net:433/gateway/transact.dll', '', 0),
(189, 'default', 0, 'payment/authorizenet/login', '', 'irub782', 0),
(190, 'default', 0, 'payment/authorizenet/order_status', '1', '', 0),
(191, 'default', 0, 'payment/authorizenet/trans_key', '', '4845Xyr5bWrRJC2Z', 0),
(192, 'default', 0, 'payment/paypal/active', '0', '1', 0),
(193, 'default', 0, 'payment/paypal/order_status', '1', '', 0),
(196, 'default', 0, 'carriers/tablerate/active', '0', '', 0),
(197, 'default', 0, 'carriers/tablerate/title', '', '', 0),
(198, 'default', 0, 'carriers/pickup/active', '0', '', 0),
(199, 'default', 0, 'carriers/pickup/title', '', '', 0),
(200, 'default', 0, 'carriers/ups/active', '1', '0', 0),
(201, 'default', 0, 'carriers/ups/gateway_url', 'http://www.ups.com:80/using/services/rave/qcostcgi.cgi', '', 0),
(202, 'default', 0, 'carriers/ups/title', 'United Parcel Service', '', 0),
(203, 'default', 0, 'carriers/ups/container', 'CP', '', 0),
(204, 'default', 0, 'carriers/ups/dest_type', 'RES', '', 0),
(205, 'default', 0, 'carriers/ups/handling', '0', '', 0),
(206, 'default', 0, 'carriers/ups/pickup', 'CC', '', 0),
(207, 'default', 0, 'carriers/usps/active', '0', '', 0),
(208, 'default', 0, 'carriers/usps/gateway_url', '', '', 0),
(209, 'default', 0, 'carriers/usps/title', '', '', 0),
(216, 'default', 0, 'shipping/origin/country_id', '223', '', 0),
(217, 'default', 0, 'shipping/origin/region_id', '12', '1', 0),
(218, 'default', 0, 'shipping/origin/postcode', '90034', '', 0),
(219, 'default', 0, 'shipping/option/checkout_multiple', '1', '0', 0),
(220, 'default', 0, 'wishlist/general/active', '1', '', 0),
(221, 'default', 0, 'payment/authorizenet/email_customer', '0', '', 0),
(222, 'default', 0, 'payment/authorizenet/merchant_email', '', 'moshe@varien.com', 0),
(223, 'default', 0, 'payment/ccsave/model', 'payment/ccsave', '', 0),
(224, 'default', 0, 'payment/checkmo/model', 'payment/checkmo', '', 0),
(225, 'default', 0, 'payment/purchaseorder/model', 'payment/purchaseorder', '', 0),
(226, 'default', 0, 'payment/authorizenet/model', 'paygate/authorizenet', '', 0),
(227, 'default', 0, 'payment/paypal/model', 'paygate/paypal', '', 0),
(229, 'default', 0, 'payment/ccsave/active', '1', '', 0),
(230, 'default', 0, 'payment/checkmo/active', '1', '', 0),
(231, 'default', 0, 'payment/purchaseorder/active', '0', '1', 0),
(232, 'default', 0, 'carriers/fedex/active', '0', '1', 0),
(233, 'default', 0, 'carriers/fedex/gateway_url', 'https://gateway.fedex.com/GatewayDC', '', 0),
(234, 'default', 0, 'carriers/fedex/title', 'Federal Express', '', 0),
(235, 'default', 0, 'carriers/fedex/packaging', 'YOURPACKAGING', '', 0),
(236, 'default', 0, 'carriers/fedex/dropoff', 'REGULARPICKUP', '', 0),
(237, 'default', 0, 'payment/ccsave/title', 'Credit Card', '', 0),
(238, 'default', 0, 'payment/checkmo/title', 'Check / Money order', 'Credit Card', 0),
(239, 'default', 0, 'payment/purchaseorder/title', 'Purchase Order', 'Credit Card', 0),
(240, 'default', 0, 'payment/authorizenet/title', 'Credit Card (Authorize.net)', '', 0),
(241, 'default', 0, 'payment/paypal/title', 'Credit Card (Paypal)', '', 0),
(243, 'default', 0, 'carriers/fedex/account', '', '329311708', 0),
(244, 'default', 0, 'carriers/usps/userid', '', '652VARIE8323', 0),
(248, 'default', 0, 'carriers/dhl/shipping_key', '', '', 0),
(249, 'default', 0, 'carriers/dhl/shipment_type', 'P', '', 0),
(250, 'default', 0, 'carriers/dhl/active', '0', '0', 0),
(251, 'default', 0, 'carriers/dhl/gateway_url', 'https://eCommerce.airborne.com/ApiLandingTest.asp', '', 0),
(252, 'default', 0, 'carriers/dhl/title', 'DHL', '', 0),
(253, 'default', 0, 'carriers/freeshipping/active', '1', '0', 0),
(254, 'default', 0, 'carriers/flatrate/active', '1', '0', 0),
(255, 'default', 0, 'carriers/freeshipping/name', 'Free', '0', 0),
(256, 'default', 0, 'carriers/flatrate/name', 'Fixed', '0', 0),
(257, 'default', 0, 'carriers/tablerate/name', 'Best Way', '0', 0),
(258, 'default', 0, 'carriers/freeshipping/title', 'Free Shipping', '0', 0),
(259, 'default', 0, 'carriers/flatrate/title', 'Flat Rate', '0', 0),
(260, 'default', 0, 'carriers/freeshipping/cutoff_cost', '50', '0', 0),
(261, 'default', 0, 'carriers/flatrate/type', 'I', '0', 0),
(262, 'default', 0, 'carriers/flatrate/price', '5.00', '0', 0),
(263, 'default', 0, 'trans_email/ident_general/name', 'General', '', 0),
(264, 'default', 0, 'trans_email/ident_general/email', 'owner@example.com', 'owner@magento.varien.com', 0),
(265, 'default', 0, 'trans_email/ident_sales/name', 'Sales', '', 0),
(266, 'default', 0, 'trans_email/ident_sales/email', 'sales@example.com', 'sales@magento.varien.com', 0),
(267, 'default', 0, 'trans_email/ident_support/name', 'Customer support', '', 0),
(268, 'default', 0, 'trans_email/ident_support/email', 'support@example.com', 'support@magento.varien.com', 0),
(269, 'default', 0, 'trans_email/ident_custom1/name', 'Custom 1', '', 0),
(270, 'default', 0, 'trans_email/ident_custom1/email', 'custom1@example.com', 'custom1@magento.varien.com', 0),
(271, 'default', 0, 'trans_email/ident_custom2/name', 'Custom 2', '', 0),
(272, 'default', 0, 'trans_email/ident_custom2/email', 'custom2@example.com', 'custom2@magento.varien.com', 0),
(273, 'default', 0, 'catalog/images/category_upload_path', '{{root_dir}}/media/catalog/category/', '', 0),
(274, 'default', 0, 'catalog/images/category_upload_url', '{{base_path}}media/catalog/category/', '', 0),
(275, 'default', 0, 'catalog/images/product_upload_path', '{{root_dir}}/media/catalog/product/', '', 0),
(276, 'default', 0, 'catalog/images/product_upload_url', '{{base_path}}media/catalog/product/', '', 0),
(277, 'default', 0, 'customer/default/group', '1', '', 0),
(292, 'default', 0, 'carriers/tablerate/condition_name', 'package_weight', '', 0),
(349, 'default', 0, 'payment/verisign/active', '0', '0', 0),
(350, 'default', 0, 'payment/verisign/order_status', '1', '', 0),
(383, 'default', 0, 'payment/verisign/model', 'paygate/payflow_pro', '', 0),
(442, 'default', 0, 'payment/verisign/title', 'Credit Card (Verisign)', '', 0),
(472, 'default', 0, 'payment/verisign/sort_order', '6', '', 0),
(547, 'default', 0, 'payment/verisign/user', '', '', 0),
(548, 'default', 0, 'payment/verisign/vendor', '', '', 0),
(549, 'default', 0, 'payment/verisign/partner', '', '', 0),
(550, 'default', 0, 'payment/verisign/pwd', '', '', 0),
(551, 'default', 0, 'payment/verisign/tender', 'C', '', 0),
(553, 'default', 0, 'payment/verisign/verbosity', 'MEDIUM', '', 0),
(554, 'default', 0, 'payment/verisign/url', 'https://pilot-payflowpro.verisign.com/transaction', '', 0),
(555, 'default', 0, 'dev/mode/checksum', '1', '', 0),
(556, 'default', 0, 'design/package/translate', 'default', '', 0),
(557, 'default', 0, 'design/package/default_theme', 'default', '', 0),
(558, 'default', 0, 'carriers/dhl/free_method', 'G', '', 0),
(559, 'default', 0, 'carriers/dhl/cutoff_cost', '', '', 0),
(560, 'default', 0, 'carriers/fedex/free_method', 'FEDEXGROUND', '', 0),
(561, 'default', 0, 'carriers/fedex/cutoff_cost', '', '', 0),
(562, 'default', 0, 'carriers/ups/free_method', 'GND', '', 0),
(563, 'default', 0, 'carriers/ups/cutoff_cost', '', '', 0),
(564, 'default', 0, 'carriers/usps/free_method', '', 'PARCEL', 0),
(565, 'default', 0, 'carriers/usps/cutoff_cost', '', '', 0),
(566, 'default', 0, 'carriers/ups/model', 'usa/shipping_carrier_ups', '', 0),
(567, 'default', 0, 'carriers/dhl/model', 'usa/shipping_carrier_dhl', '', 0),
(568, 'default', 0, 'carriers/usps/model', 'usa/shipping_carrier_usps', '', 0),
(569, 'default', 0, 'carriers/fedex/model', 'usa/shipping_carrier_fedex', '', 0),
(570, 'default', 0, 'carriers/pickup/model', 'shipping/carrier_pickup', '', 0),
(571, 'default', 0, 'carriers/freeshipping/model', 'shipping/carrier_freeshipping', '', 0),
(572, 'default', 0, 'carriers/flatrate/model', 'shipping/carrier_flatrate', '', 0),
(573, 'default', 0, 'carriers/tablerate/model', 'shipping/carrier_tablerate', '', 0),
(577, 'default', 0, 'sales/new_order/email_identity', 'sales', '', 0),
(578, 'default', 0, 'sales/new_order/email_template', '2', '', 0),
(579, 'default', 0, 'web/default/no_route', 'cms/index/noRoute', '', 0),
(588, 'default', 0, 'web/default/front', 'cms', '', 0),
(589, 'default', 0, 'sales/totals_sort/subtotal', '10', '', 0),
(590, 'default', 0, 'sales/totals_sort/discount', '20', '', 0),
(591, 'default', 0, 'sales/totals_sort/shipping', '30', '', 0),
(592, 'default', 0, 'sales/totals_sort/tax', '40', '', 0),
(593, 'default', 0, 'sales/totals_sort/grand_total', '100', '', 0),
(594, 'default', 0, 'sales/order_update/email_identity', 'sales', '', 0),
(595, 'default', 0, 'sales/order_update/email_template', '4', '', 0),
(596, 'default', 0, 'customer/create_account/default_group', '1', '', 0),
(597, 'default', 0, 'customer/create_account/confirm', '0', '', 0),
(598, 'default', 0, 'customer/create_account/email_identity', 'general', '', 0),
(599, 'default', 0, 'customer/create_account/email_template', '1', '', 0),
(600, 'default', 0, 'customer/password/forgot_email_identity', 'support', '', 0),
(601, 'default', 0, 'customer/password/forgot_email_template', '3', '', 0),
(602, 'default', 0, 'payment/ccsave/order_status', '1', '', 0),
(603, 'default', 0, 'payment/ccsave/sort_order', '1', '', 0),
(604, 'default', 0, 'payment/checkmo/order_status', '1', '', 0),
(605, 'default', 0, 'payment/checkmo/sort_order', '2', '', 0),
(606, 'default', 0, 'payment/purchaseorder/order_status', '1', '', 0),
(607, 'default', 0, 'payment/purchaseorder/sort_order', '3', '', 0),
(608, 'default', 0, 'payment/authorizenet/sort_order', '4', '', 0),
(609, 'default', 0, 'payment/paypal/sort_order', '5', '', 0),
(610, 'default', 0, 'catalog/frontend/list_mode', 'grid-list', 'list-grid', 0),
(611, 'default', 0, 'catalog/frontend/product_per_page', '9', '10', 0),
(612, 'default', 0, 'catalog/product/default_tax_group', '2', '', 0),
(613, 'default', 0, 'web/cookie/cookie_domain', '', '', 0),
(614, 'default', 0, 'web/cookie/cookie_path', '', '', 0),
(615, 'default', 0, 'system/smtp/host', 'localhost', '', 0),
(616, 'default', 0, 'system/smtp/port', '25', '', 0),
(634, 'default', 0, 'wishlist/email/email_template', '7', '', 0),
(635, 'default', 0, 'carriers/dhl/allowed_methods', 'E,N,S,G', '', 0),
(636, 'default', 0, 'carriers/fedex/allowed_methods', 'PRIORITYOVERNIGHT,STANDARDOVERNIGHT,FIRSTOVERNIGHT,FEDEX2DAY,FEDEXEXPRESSSAVER,INTERNATIONALPRIORITY,INTERNATIONALECONOMY,INTERNATIONALFIRST,FEDEX1DAYFREIGHT,FEDEX2DAYFREIGHT,FEDEX3DAYFREIGHT,FEDEXGROUND,GROUNDHOMEDELIVERY,INTERNATIONALPRIORITY FREIGHT,INTERNATIONALECONOMY FREIGHT,EUROPEFIRSTINTERNATIONALPRIORITY', '', 0),
(637, 'default', 0, 'carriers/ups/allowed_methods', '1DM,1DML,1DA,1DAL,1DAPI,1DP,1DPL,2DM,2DML,2DA,2DAL,3DS,GND,GNDCOM,GNDRES,STD,XPR,WXS,XPRL,XDM,XDML,XPD', '', 0),
(639, 'default', 0, 'carriers/tablerate/sort_order', '', '', 0),
(640, 'default', 0, 'carriers/freeshipping/sort_order', '', '', 0),
(641, 'default', 0, 'carriers/flatrate/sort_order', '', '', 0),
(642, 'default', 0, 'carriers/ups/sort_order', '', '', 0),
(643, 'default', 0, 'carriers/usps/container', 'VARIABLE', '', 0),
(644, 'default', 0, 'carriers/usps/size', 'REGULAR', '', 0),
(645, 'default', 0, 'carriers/usps/machinable', 'true', '', 0),
(646, 'default', 0, 'carriers/usps/handling', '', '', 0),
(647, 'default', 0, 'carriers/usps/sort_order', '', '', 0),
(648, 'default', 0, 'carriers/fedex/handling', '', '', 0),
(649, 'default', 0, 'carriers/fedex/sort_order', '', '', 0),
(665, 'websites', 0, 'advanced/datashare/default', '0,1,5,6', '', 0),
(666, 'websites', 1, 'advanced/datashare/default', '1,5,6', '', 0),
(743, 'default', 0, 'web/cookie/cookie_lifetime', '', '', 0),
(744, 'default', 0, 'carriers/usps/methods', 'First-Class,Express Mail,Express Mail PO to PO,Priority Mail,Parcel Post,Express Mail Flat-Rate Envelope,Priority Mail Flat-Rate Box,Bound Printed Matter,Media Mail,Library Mail,Priority Mail Flat-Rate Envelope,Global Express Guaranteed,Global Express Guaranteed Non-Document Rectangular,Global Express Guaranteed Non-Document Non-Rectangular,Express Mail International (EMS),Express Mail International (EMS) Flat Rate Envelope,Priority Mail International,Priority Mail International Flat Rate Box', '', 0),
(745, 'default', 0, 'carriers/usps/allowed_methods', 'First-Class,Express Mail,Express Mail PO to PO,Priority Mail,Parcel Post,Express Mail Flat-Rate Envelope,Priority Mail Flat-Rate Box,Bound Printed Matter,Media Mail,Library Mail,Priority Mail Flat-Rate Envelope,Global Express Guaranteed,Global Express Guaranteed Non-Document Rectangular,Global Express Guaranteed Non-Document Non-Rectangular,Express Mail International (EMS),Express Mail International (EMS) Flat Rate Envelope,Priority Mail International,Priority Mail International Flat Rate Box', '', 0),
(760, 'default', 0, 'wishlist/email/email_identity', 'general', '', 0),
(761, 'default', 0, 'newsletter/subscription/confirm', '0', '', 0),
(762, 'default', 0, 'newsletter/subscription/confirm_email_identity', 'support', '', 0),
(763, 'default', 0, 'newsletter/subscription/confirm_email_template', '6', '', 0),
(764, 'default', 0, 'newsletter/subscription/success_email_identity', 'general', '', 0),
(765, 'default', 0, 'newsletter/subscription/success_email_template', '8', '', 0),
(766, 'default', 0, 'newsletter/subscription/un_email_identity', 'support', '', 0),
(767, 'default', 0, 'newsletter/subscription/un_email_template', '9', '', 0);

DROP TABLE IF EXISTS `core_config_field`;
CREATE TABLE `core_config_field` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `level` tinyint(1) unsigned NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `frontend_label` varchar(255) NOT NULL default '',
  `frontend_type` varchar(64) NOT NULL default 'text',
  `frontend_class` varchar(255) NOT NULL default '',
  `frontend_model` varchar(255) NOT NULL default '',
  `backend_model` varchar(255) NOT NULL default '',
  `source_model` varchar(255) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `show_in_default` tinyint(4) NOT NULL default '1',
  `show_in_website` tinyint(4) NOT NULL default '1',
  `show_in_store` tinyint(4) NOT NULL default '1',
  `module_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`field_id`),
  UNIQUE KEY `IDX_PATH` (`path`),
  KEY `path` (`level`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `core_email_template`;
CREATE TABLE `core_email_template` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) default NULL,
  `template_text` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `added_at` datetime default NULL,
  `modified_at` datetime default NULL,
  PRIMARY KEY  (`template_id`),
  UNIQUE KEY `template_code` (`template_code`),
  KEY `added_at` (`added_at`),
  KEY `modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Email templates';

insert  into `core_email_template`(`template_id`,`template_code`,`template_text`,`template_type`,`template_subject`,`template_sender_name`,`template_sender_email`,`added_at`,`modified_at`) values
(1,'New account (HTML)','               <style type=\"text/css\">\r\n           body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }\r\n      </style>\r\n\r\n<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">\r\n         <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\"\">\r\n             <tr>\r\n                    <td align=\"center\" valign=\"top\">\r\n                    <!-- [ header starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                                 <a href=\"{{store url=\"\"}}\"><img src=\"{{skin url=\"images/logo_email.gif\"}}\" alt=\"Magento\"  style=\"margin-bottom:10px;\" border=\"0\"/></a></td>\r\n                           </tr>\r\n                       </table>\r\n\r\n                    <!-- [ middle starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                             <p><strong>Dear {{var customer.name}}</strong>,<br/>\r\n                                Welcome to Magento Demo Store. To log in when visiting our site just click <a href=\"{{store url=\"customer/account/\"}}\" style=\"color:#1E7EC8;\">Login</a> or <a href=\"{{store url=\"customer/account/\"}}\" style=\"color:#1E7EC8;\">My Account</a> at the top of every page, and then enter your e-mail address and password.</p>\r\n\r\n         <p style=\"border:1px solid #BEBCB7; padding:13px 18px; background:#F8F7F5; \">\r\nUse the following values when prompted to log in:<br/>\r\nE-mail: {{var customer.email}}<br/>\r\nPassword: {{var customer.password}}<p>\r\n\r\n<p>When you log in to your account, you will be able to do the following:</p>\r\n\r\n<ul>\r\n<li>Proceed through checkout faster when making a purchase</li>\r\n<li> Check the status of orders</li>\r\n<li>View past orders</li>\r\n<li> Make changes to your account information</li>\r\n<li>Change your password</li>\r\n<li>Store alternative addresses (for shipping to multiple family members and friends!)</li>\r\n</ul>\r\n\r\n<p>If you have any questions about your account or any other matter, please feel free to contact us at \r\n<a href=\"mailto:magento@varien.com\" style=\"color:#1E7EC8;\">dummyemail@magentocommerce.com</a> or by phone at (800) DEMO-STORE.</p>\r\n<p>Thanks again!</p>\r\n\r\n\r\n                             </td>\r\n                           </tr>\r\n                       </table>\r\n                    \r\n                    </td>\r\n               </tr>\r\n           </table>\r\n            </div>\r\n',2,'Welcome, {{var customer.name}}!',NULL,NULL,NOW(),NOW()),
(2,'New order (HTML)','<style type=\"text/css\">\r\nbody,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }\r\n</style>\r\n\r\n<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">\r\n            <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\"\">\r\n             <tr>\r\n                    <td align=\"center\" valign=\"top\">\r\n                    <!-- [ header starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                                 <a href=\"{{store url=\"\"}}\"><img src=\"{{skin url=\"images/logo_email.gif\"}}\" alt=\"Magento\"  style=\"margin-bottom:10px;\" border=\"0\"/></a></td>\r\n                           </tr>\r\n                       </table>\r\n\r\n                    <!-- [ middle starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                             <p><strong>Hello {{var billing.name}}</strong>,<br/>\r\n                                Thank you for your order from Magento Demo Store. Once your package ships we will send an email with a link to track your order. You can check the status of your order by <a href=\"{{store url=\"customer/account/\"}}\" style=\"color:#1E7EC8;\">logging into your account</a>. If you have any questions about your order please contact us at <a href=\"mailto:dummyemail@magentocommerce.com\" style=\"color:#1E7EC8;\">dummyemail@magentocommerce.com</a> or call us at <nobr>(800) DEMO-NUMBER</nobr> Monday - Friday, 8am - 5pm PST.</p>\r\n <p>Your order confirmation is below. Thank you again for your business.</p>\r\n\r\n                                <h3 style=\"border-bottom:2px solid #eee; font-size:1.05em; padding-bottom:1px; \">Your Order #{{var order.increment_id}} <small>(placed on {{var order.getCreatedAtFormated(\'long\')}})</small></h3>\r\n                              <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                                 <thead>\r\n                                 <tr>\r\n                                        <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Billing\r\n                                       Information:</th>\r\n                                       <th width=\"3%\"></th>\r\n                                      <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Payment\r\n                                       Method:</th>\r\n                                    </tr>\r\n                                   </thead>\r\n                                    <tbody>\r\n                                 <tr>\r\n                                        <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\">{{var order.billing_address.getFormated(\'html\')}}</td>\r\n                                      <td>&nbsp;</td>\r\n                                     <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\"> {{var order.payment.getHtmlFormated(\'private\'))}}</td>\r\n                                 </tr>\r\n                                   </tbody>\r\n                                </table><br/>\r\n                                               <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n                                 <thead>\r\n                                 <tr>\r\n                                        <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Shipping\r\n                                      Information:</th>\r\n                                       <th width=\"3%\"></th>\r\n                                      <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Shipping\r\n                                      Method:</th>\r\n                                    </tr>\r\n                                   </thead>\r\n                                    <tbody>\r\n                                 <tr>\r\n                                        <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\">{{var order.shipping_address.getFormated(\'html\')}}</td>\r\n                                     <td>&nbsp;</td>\r\n                                     <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\">{{var order.shipping_description}}</td>\r\n                                   </tr>\r\n                                   </tbody>\r\n                                </table><br/>\r\n\r\n{{var items_html}}<br/>\r\n      {{var order.getEmailCustomerNote()}}\r\n                                <p>Thank you again,<br/><strong>Magento Demo Store</strong></p>\r\n\r\n\r\n                             </td>\r\n                           </tr>\r\n                       </table>\r\n\r\n                    </td>\r\n               </tr>\r\n           </table>\r\n            </div>',2,'New Order # {{var order.increment_id}}',NULL,NULL,NOW(),NOW()),
(3,'New password (HTML)','\r\n        <style type=\"text/css\">\r\n           body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }\r\n      </style>\r\n\r\n        <div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">\r\n         <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\"\">\r\n             <tr>\r\n                    <td align=\"center\" valign=\"top\">\r\n                    <!-- [ header starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                                 <p><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/media/logo_email.gif\"}}\" alt=\"Magento\" border=\"0\"/></a></p></td>\r\n                            </tr>\r\n                       </table>\r\n\r\n                    <!-- [ middle starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                             <p><strong>Dear {{var customer.name}},</strong>,<br/>\r\n                               Your new password is: {{var customer.password}}</p>\r\n                                                               <p>You can change your password at any time by logging into <a href=\"{{store url=\"customer/account/\"}}\" style=\"color:#1E7EC8;\">your account</a>.<p>\r\n                             \r\n                                <p>Thank you again,<br/><strong>Magento Demo Store</strong></p>\r\n\r\n\r\n                             </td>\r\n                           </tr>\r\n                       </table>\r\n                    \r\n                    </td>\r\n               </tr>\r\n           </table>\r\n            </div>\r\n',2,'New password for {{var customer.name}}',NULL,NULL,NOW(),NOW()),
(4,'Order update (HTML)','                <style type=\"text/css\">\r\n           body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }\r\n      </style>\r\n<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">\r\n         <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\"\">\r\n             <tr>\r\n                    <td align=\"center\" valign=\"top\">\r\n                    <!-- [ header starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                                 <a href=\"{{store url=\"\"}}\"><img src=\"{{skin url=\"images/logo_email.gif\"}}\" alt=\"Magento\"  style=\"margin-bottom:10px;\" border=\"0\"/></a></td>\r\n                           </tr>\r\n                       </table>\r\n\r\n                    <!-- [ middle starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                             <p><strong>Dear {{var billing.name}}</strong>,<br/>\r\n                             Your order # {{var order.increment_id}} has been <strong>{{var order.status.frontend_label}}</strong>.</p>\r\n                              <p>Your order was shipped to:<br/><address>{{var order.shipping_address.getFormated(\'html\')}}</address>\r\n<p>If you have any questions, please feel free to contact us at \r\n<a href=\"mailto:magento@varien.com\" style=\"color:#1E7EC8;\">dummyemail@magentocommerce.com</a> or by phone at (800) DEMO-STORE.</p>\r\n\r\n\r\n <p>Thank you again,<br/><strong>Magento Demo Store</strong></p>\r\n\r\n\r\n                             </td>\r\n                           </tr>\r\n                       </table>\r\n                    \r\n                    </td>\r\n               </tr>\r\n           </table>\r\n            </div>\r\n',2,'Order # {{var order.increment_id}} update',NULL,NULL,NOW(),NOW()),
(5,'New account (Plain)','Welcome {{var customer.name}}!\r\n\r\nThank you very much for creating an account.\r\n\r\nTo officially log in when you\'re visiting our site, simply click on \"Login\" or \"My Account\" located at the top of every page, and then enter your e-mail address and the password you have chosen.\r\n\r\n==========================================\r\n\r\nUse the following values when prompted to log in:\r\n\r\nE-mail: {{var customer.email}}\r\n\r\nPassword: {{var customer.password}}\r\n\r\n==========================================\r\n\r\nWhen you log in to your account, you will be able to do the following:\r\n\r\n* Proceed through checkout faster when making a purchase\r\n\r\n* Check the status of orders\r\n\r\n* View past orders\r\n\r\n* Make changes to your account information\r\n\r\n* Change your password\r\n\r\n* Store alternative addresses (for shipping to multiple family members and friends!)\r\n\r\nIf you have any questions about your account or any other matter, please feel free to contact us at \r\nmagento@varien.com or by phone at 1-111-111-1111.\r\n\r\n\r\nThanks again!',2,'Welcome {{var customer.name}}',NULL,NULL,NOW(),NOW()),
(6,'Newsletter subscription confirmation (HTML)','       <style type=\"text/css\">\r\n           body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }\r\n      </style>\r\n\r\n        <div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">\r\n         <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\"\">\r\n             <tr>\r\n                    <td align=\"center\" valign=\"top\">\r\n                    <!-- [ header starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                                 <p><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/media/logo_email.gif\"}}\" alt=\"Magento\" border=\"0\"/></a></p></td>\r\n                            </tr>\r\n                       </table>\r\n\r\n                    <!-- [ middle starts here] -->\r\n                      <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                          <tr>\r\n                                <td valign=\"top\">\r\n                             <p><strong>Dear {{var customer.name}},</strong>,<br/>\r\n                               Thank you for subscribing to our newsletter.</p>\r\n                                                              <p style=\"border:1px solid #BEBCB7; padding:13px 18px; background:#F8F7F5; \">To begin receiving the newsletter, you must first confirm your subscription by clicking on the following link:<br />\r\n<a href=\"{{var subscriber.getConfirmationLink()}}\" style=\"color:#1E7EC8;\">{{var subscriber.getConfirmationLink()}}</a><p>\r\n                              \r\n                                <p>Thank you again,<br/><strong>Magento Demo Store</strong></p>\r\n\r\n\r\n                             </td>\r\n                           </tr>\r\n                       </table>\r\n                    \r\n                    </td>\r\n               </tr>\r\n           </table>\r\n            </div>\r\n',2,'Newsletter subscription confirmation',NULL,NULL,NOW(),NOW()),
(7,'Share Wishlist','<style type=\"text/css\">\r\n    body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }\r\n</style>\r\n<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">\r\n    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">\r\n        <tr>\r\n            <td align=\"center\" valign=\"top\">\r\n            <!-- [ header starts here] -->\r\n                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                    <tr>\r\n                        <td valign=\"top\">\r\n                            <p><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\"}}\" alt=\"Magento\" border=\"0\"/></a></p></td>\r\n                    </tr>\r\n                </table>\r\n\r\n            <!-- [ middle starts here] -->\r\n                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">\r\n                    <tr>\r\n                        <td valign=\"top\">\r\n                        <p>Hey,<br/>\r\n                        Take a look at my wishlist from Magento Demo Store.</p> \r\n\r\n<p>{{var message}}</p>\r\n\r\n                        {{var items}}\r\n\r\n                        <br/>\r\n\r\n<p><strong><a href=\"{{var addAllLink}}\" style=\"color:#DC6809;\">Add all items to shopping cart</a></strong> | <strong><a href=\"{{var viewOnSiteLink}}\" style=\"color:#1E7EC8;\">View all items in the store</a></strong></p>\r\n                        \r\n                        <p>Thank you,<br/><strong>{{var customer.name}}</strong></p>\r\n\r\n\r\n                        </td>\r\n                    </tr>\r\n                </table>\r\n            \r\n            </td>\r\n        </tr>\r\n    </table>\r\n    </div>',2,'Take a look at {{var customer.name}}\'s wishlist',NULL,NULL,NOW(),NOW()),
(8,'Newsletter Subscription Success','Newsletter Subscription Success',2,'Newsletter Subscription Success',NULL,NULL,NOW(),NOW()),
(9,'Newsletter Unsubscription Success','Newsletter Unsubscription Success',2,'Newsletter Unsubscription Success',NULL,NULL,NOW(),NOW());

DROP TABLE IF EXISTS `core_language`;
CREATE TABLE `core_language` (
  `language_code` varchar(2) NOT NULL default '',
  `language_title` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Languages';

insert  into `core_language`(`language_code`,`language_title`) values ('aa','Afar'),('ab','Abkhazian'),('af','Afrikaans'),('am','Amharic'),('ar','Arabic'),('as','Assamese'),('ay','Aymara'),('az','Azerbaijani'),('ba','Bashkir'),('be','Byelorussian'),('bg','Bulgarian'),('bh','Bihari'),('bi','Bislama'),('bn','Bengali'),('bo','Tibetan'),('br','Breton'),('ca','Catalan'),('co','Corsican'),('cs','Czech'),('cy','Welsh'),('da','Danish'),('de','German'),('dz','Bhutani'),('el','Greek'),('en','English'),('eo','Esperanto'),('es','Spanish'),('et','Estonian'),('eu','Basque'),('fa','Persian'),('fi','Finnish'),('fj','Fiji'),('fo','Faeroese'),('fr','French'),('fy','Frisian'),('ga','Irish'),('gd','Gaelic'),('gl','Galician'),('gn','Guarani'),('gu','Gujarati'),('ha','Hausa'),('hi','Hindi'),('hr','Croatian'),('hu','Hungarian'),('hy','Armenian'),('ia','Interlingua'),('ie','Interlingue'),('ik','Inupiak'),('in','Indonesian'),('is','Icelandic'),('it','Italian'),('iw','Hebrew'),('ja','Japanese'),('ji','Yiddish'),('jw','Javanese'),('ka','Georgian'),('kk','Kazakh'),('kl','Greenlandic'),('km','Cambodian'),('kn','Kannada'),('ko','Korean'),('ks','Kashmiri'),('ku','Kurdish'),('ky','Kirghiz'),('la','Latin'),('ln','Lingala'),('lo','Laothian'),('lt','Lithuanian'),('lv','Latvian'),('mg','Malagasy'),('mi','Maori'),('mk','Macedonian'),('ml','Malayalam'),('mn','Mongolian'),('mo','Moldavian'),('mr','Marathi'),('ms','Malay'),('mt','Maltese'),('my','Burmese'),('na','Nauru'),('ne','Nepali'),('nl','Dutch'),('no','Norwegian'),('oc','Occitan'),('om','Oromo'),('or','Oriya'),('pa','Punjabi'),('pl','Polish'),('ps','Pashto'),('pt','Portuguese'),('qu','Quechua'),('rm','Rhaeto-Romance'),('rn','Kirundi'),('ro','Romanian'),('ru','Russian'),('rw','Kinyarwanda'),('sa','Sanskrit'),('sd','Sindhi'),('sg','Sangro'),('sh','Serbo-Croatian'),('si','Singhalese'),('sk','Slovak'),('sl','Slovenian'),('sm','Samoan'),('sn','Shona'),('so','Somali'),('sq','Albanian'),('sr','Serbian'),('ss','Siswati'),('st','Sesotho'),('su','Sudanese'),('sv','Swahili'),('sw','Swedish'),('ta','Tamil'),('te','Tegulu'),('tg','Tajik'),('th','Thai'),('ti','Tigrinya'),('tk','Turkmen'),('tl','Tagalog'),('tn','Setswana'),('to','Tonga'),('tr','Turkish'),('ts','Tsonga'),('tt','Tatar'),('tw','Twi'),('uk','Ukrainian'),('ur','Urdu'),('uz','Uzbek'),('vi','Vietnamese'),('vo','Volapuk'),('wo','Wolof'),('xh','Xhosa'),('yo','Yoruba'),('zh','Chinese'),('zu','Zulu');

DROP TABLE IF EXISTS `core_resource`;
CREATE TABLE `core_resource` (
  `code` varchar(50) NOT NULL default '',
  `version` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Resource version registry';

DROP TABLE IF EXISTS `core_session`;
CREATE TABLE `core_session` (
  `session_id` varchar(255) NOT NULL default '',
  `website_id` smallint(5) unsigned default NULL,
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `FK_SESSION_WEBSITE` (`website_id`),
  CONSTRAINT `FK_SESSION_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Session data store';

DROP TABLE IF EXISTS `core_store`;
CREATE TABLE `core_store` (
  `store_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `language_code` varchar(2) default NULL,
  `website_id` smallint(5) unsigned default '0',
  `name` varchar(32) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`store_id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK_STORE_LANGUAGE` (`language_code`),
  KEY `FK_STORE_WEBSITE` (`website_id`),
  KEY `is_active` (`is_active`,`sort_order`),
  CONSTRAINT `FK_STORE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_STORE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores';

insert  into `core_store`(`store_id`,`code`,`language_code`,`website_id`,`name`,`sort_order`,`is_active`) values (0,'default','en',0,'Default',0,1),(1,'base','en',1,'English Store',0,1);

DROP TABLE IF EXISTS `core_translate`;
CREATE TABLE `core_translate` (
  `key_id` int(10) unsigned NOT NULL auto_increment,
  `string` varchar(255) NOT NULL default '',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `translate` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`key_id`),
  UNIQUE KEY `IDX_CODE` (`string`,`store_id`),
  KEY `FK_CORE_TRANSLATE_STORE` (`store_id`),
  CONSTRAINT `FK_CORE_TRANSLATE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Translation data';


DROP TABLE IF EXISTS `core_website`;
CREATE TABLE `core_website` (
  `website_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`website_id`),
  UNIQUE KEY `code` (`code`),
  KEY `is_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

insert  into `core_website`(`website_id`,`code`,`name`,`sort_order`,`is_active`) values (0,'default','Default',0,1),(1,'base','Main Website',0,1);

DROP TABLE IF EXISTS `core_layout_update`;
CREATE TABLE `core_layout_update` (
  `layout_update_id` int(10) unsigned NOT NULL auto_increment,
  `handle` varchar(255) default NULL,
  `xml` text,
  PRIMARY KEY  (`layout_update_id`),
  KEY `handle` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `core_layout_link`;
CREATE TABLE `core_layout_link` (
  `layout_link_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `package` varchar(64) NOT NULL default '',
  `theme` varchar(64) NOT NULL default '',
  `layout_update_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`layout_link_id`),
  UNIQUE KEY `store_id` (`store_id`,`package`,`theme`,`layout_update_id`),
  KEY `FK_core_layout_link_update` (`layout_update_id`),
  CONSTRAINT `FK_core_layout_link_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_core_layout_link_update` FOREIGN KEY (`layout_update_id`) REFERENCES `core_layout_update` (`layout_update_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `core_url_rewrite`;
create table `core_url_rewrite` (
    `url_rewrite_id` int unsigned not null auto_increment primary key,
    `store_id` smallint unsigned not null,
    `id_path` varchar(255) not null,
    `request_path` varchar(255) not null,
    `target_path` varchar(255) not null,
    `options` varchar(255) not null,
    `type` int(1) NOT NULL  DEFAULT '0',
    `description` varchar(255) NULL,
    unique (`id_path`, `store_id`),
    unique (`request_path`, `store_id`),
    key (`target_path`, `store_id`),
    foreign key (`store_id`) references `core_store` (`store_id`) on delete cascade on update cascade
) engine=InnoDB default charset=utf8;

drop table if exists `core_url_rewrite_tag`;
create table `core_url_rewrite_tag` (
    `url_rewrite_tag_id` int unsigned not null auto_increment primary key,
    `url_rewrite_id` int unsigned not null,
    `tag` varchar(255),
    unique (`tag`, `url_rewrite_id`),
    key (`url_rewrite_id`),
    foreign key (`url_rewrite_id`) references `core_url_rewrite` (`url_rewrite_id`) on delete cascade on update cascade
) engine=InnoDB default charset=utf8;

drop table if exists `core_convert_profile`;
CREATE TABLE `core_convert_profile` (
  `profile_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `actions_xml` text,
  `gui_data` text,
  `direction` enum('import','export') default NULL,
  `entity_type` varchar(64) NOT NULL default '',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `data_transfer` enum('file', 'interactive'),
  PRIMARY KEY  (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `core_convert_history`;
CREATE TABLE `core_convert_history` (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL default '0',
  `action_code` varchar(64) default NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `performed_at` datetime default NULL,
  PRIMARY KEY  (`history_id`),
  KEY `FK_core_convert_history` (`profile_id`),
  CONSTRAINT `FK_core_convert_history` FOREIGN KEY (`profile_id`) REFERENCES `core_convert_profile` (`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();

$installer->setConfigData('allow/currency/code', 'ADP,AED,AFN,ALL,AMD,ANG,AOA,ARA,ARS,ATS,AUD,AWG,AZN,BAD,BAM,BBD,BDT,BEF,BGN,BHD,BIF,BMD,BND,BOB,BOP,BOV,BRC,BRL,BRN,BRR,BSD,BTN,BUK,BWP,BYR,BZD,CAD,CDF,CHE,CHF,CHW,CLF,CLP,CNY,COP,COU,CRC,CUP,CVE,CYP,CZK,DEM,DJF,DKK,DOP,DZD,ECS,EEK,EGP,EQE,ERN,ESP,ETB,EUR,FIM,FJD,FKP,FRF,GBP,GEK,GHS,GIP,GMD,GNF,GNS,GQE,GRD,GTQ,GWE,GWP,GYD,HKD,HNL,HRD,HRK,HTG,HUF,IDR,IEP,ILS,INR,IQD,INR,IQD,IRR,ISK,ITL,JMD,JOD,JPY,KES,KGS,KHR,KMF,KPW,KRW,KWD,KYD,KZT,LAK,LBP,LKR,LRD,LSL,LSM,LTL,LTT,LUF,LVL,LYD,MAD,MAF,MDL,MGA,MGF,MKD,MLF,MMK,MNT,MOP,MRO,MTL,MTP,MUR,MVR,MWK,MXN,MYR,MZE,MZN,NAD,NGN,NIC,NLG,NOK,NPR,NZD,OMR,PAB,PEI,PES,PGK,PHP,PKR,PLN,PTE,PYG,QAR,RHD,RON,RSD,RUB,RWF,SAR,SBD,SCR,SDG,SEK,SGD,SHP,SIT,SKK,SLL,SOS,SRD,SRG,STD,SVC,SYP,SZL,THB,TJR,TJS,TMM,TND,TOP,TPE,TRY,TTD,TWD,TZS,UAH,UGX,USD,UYU,UZS,VEB,VND,VUV,WST,XCD,YER,ZAR,ZMK,ZRN,ZRZ,ZWD');

$installer->setConfigData('general/country/allow', 'AF,AL,DZ,AS,AD,AO,AI,AQ,AG,AR,AM,AW,AU,AT,AZ,BS,BH,BD,BB,BY,BE,BZ,BJ,BM,BT,BO,BA,BW,BV,BR,IO,VG,BN,BG,BF,BI,KH,CM,CA,CV,KY,CF,TD,CL,CN,CX,CC,CO,KM,CG,CK,CR,HR,CU,CY,CZ,DK,DJ,DM,DO,EC,EG,SV,GQ,ER,EE,ET,FK,FO,FJ,FI,FR,GF,PF,TF,GA,GM,GE,DE,GH,GI,GR,GL,GD,GP,GU,GT,GN,GW,GY,HT,HM,HN,HK,HU,IS,IN,ID,IR,IQ,IE,IL,IT,CI,JM,JP,JO,KZ,KE,KI,KW,KG,LA,LV,LB,LS,LR,LY,LI,LT,LU,MO,MK,MG,MW,MY,MV,ML,MT,MH,MQ,MR,MU,YT,FX,MX,FM,MD,MC,MN,MS,MA,MZ,MM,NA,NR,NP,NL,AN,NC,NZ,NI,NE,NG,NU,NF,KP,MP,NO,OM,PK,PW,PA,PG,PY,PE,PH,PN,PL,PT,PR,QA,RE,RO,RU,RW,SH,KN,LC,PM,VC,WS,SM,ST,SA,SN,SC,SL,SG,SK,SI,SB,SO,ZA,GS,KR,ES,LK,SD,SR,SJ,SZ,SE,CH,SY,TW,TJ,TZ,TH,TG,TK,TO,TT,TN,TR,TM,TC,TV,VI,UG,UA,AE,GB,US,UM,UY,UZ,VU,VA,VE,VN,WF,EH,YE,ZM,ZW');
