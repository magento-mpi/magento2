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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();
$connection->insert($installer->getTable('cms/page'), array(
    'title'             => 'Important Message about Cookies',
    'root_template'     => 'one_column',
    'identifier'        => 'cookies',
    'content'           => "<style type=\"text/css\">\r\n<!--\r\n.cookieTitle {\r\n    color: #E76200;\r\n    font-weight: bold;\r\n}\r\n-->\r\n</style>\r\n<p>Please Enable Cookies in your Web Browser to Continue</p>\r\n<p class=\"cookieTitle\">What are cookies?</p>\r\n<p>Cookies are short pieces of data that are sent to your computer when you visit a website. On later visits, this data is then returned to that website.</p>\r\n<p class=\"cookieTitle\"><strong> Why do I need to enable cookies in my Web browser in order to shop at Magento?</strong></p>\r\n<p>Cookies allow us to recognize you automatically whenever you visit our site so that we can personalize your experience and provide you with better service. We also use cookies (and similar browser data, such as Flash cookies) for fraud prevention and other purposes.</p>\r\n<p>If your web browser is set to refuse cookies from our website, you will not be able to complete a purchase or take advantage of certain features of our website, such as storing items in your Shopping Cart or receiving personalized recommendations. As a result, we strongly encourage you to configure your web browser to accept cookies from our website.</p>\r\n<p>To learn more about the information we collect from you and how we use it, please read our Privacy Notice.</p>\r\n<p class=\"cookieTitle\"><strong> Is enabling cookies safe?</strong></p>\r\n<p>Yes. Cookies are just short pieces of data, and they are unable to perform any operation by themselves.</p>\r\n<p class=\"cookieTitle\"><strong>How do I change this setting so I can shop the site?</strong></p>\r\n<p>The Help portion of the toolbar on most browsers will tell you how to set your browser to accept new cookies, how to have the browser notify you when you receive a new cookie, or how to disable cookies altogether. Additionally, you can modify the settings for similar data used by browser add-ons, such as Flash cookies, by changing the add-on's settings or visiting the website of its manufacturer.</p>\r\n<p>Once you have enabled cookies, please click <a href=\"{{store url=''}}\">here to continue shopping</a>.</p>"
));
$connection->insert($installer->getTable('cms/page_store'), array(
    'page_id'   => $connection->lastInsertId(),
    'store_id'  => 0
));
$installer->endSetup();
