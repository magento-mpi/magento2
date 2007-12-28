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
 * @package    Mage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define("EXTENSION",'csv');
$CONFIG['allow_extensions'] = array('php','xml','phtml','csv');
$CONFIG['paths'] = array(
	 'locale' => 'app/locale/',
	 'mage' => 'app/code/core/Mage/'
);

$CONFIG['translates'] = array(

    'Mage_Adminhtml' => array(
        'app/code/core/Mage/Admin/',
        'app/code/core/Mage/Adminhtml/',
        'app/design/adminhtml/default/default/layout/',
        'app/design/adminhtml/default/default/template/',
    ),

    'Mage_Backup' => array(
        'app/code/core/Mage/Backup/',
    ),

    'Mage_Catalog' => array(
        'app/code/core/Mage/Catalog/',
        'app/design/frontend/default/default/template/catalog/',
    ),

    'Mage_CatalogInventory' => array(
        'app/code/core/Mage/CatalogInventory/',
    ),

    'Mage_CatalogRule' => array(
        'app/code/core/Mage/CatalogRule/',
    ),

    'Mage_CatalogSearch' => array(
        'app/code/core/Mage/CatalogSearch/',
        'app/design/frontend/default/default/template/catalogsearch/',
    ),

    'Mage_Checkout' => array(
        'app/code/core/Mage/Checkout/',
        'app/design/frontend/default/default/template/checkout/',
    ),

    'Mage_Cms' => array(
        'app/code/core/Mage/Cms/',
        'app/design/frontend/default/default/template/cms/',
    ),

    'Mage_Core' => array(
        'app/code/core/Mage/Core/',
        'app/design/frontend/default/default/template/core/',
    ),

    'Mage_Cron' => array(
        'app/code/core/Mage/Cron/',
    ),

    'Mage_Customer' => array(
        'app/code/core/Mage/Customer/',
        'app/design/frontend/default/default/template/customer/',
    ),

    'Mage_CustomerAlert' => array(
        'app/code/core/Mage/CustomerAlert/',
        'app/design/frontend/default/default/template/customeralert/',
    ),

    'Mage_Directory' => array(
        'app/code/core/Mage/Directory/',
        'app/design/frontend/default/default/template/directory/',
    ),

    'Mage_Eav' => array(
        'app/code/core/Mage/Eav/',
    ),

    'Mage_Giftcert' => array(
        'app/code/core/Mage/Giftcert/',
    ),

    'Mage_GiftMessage' => array(
        'app/code/core/Mage/GiftMessage/',
        'app/design/frontend/default/default/template/giftmessage/',
    ),

    'Mage_GoogleAnalytics' => array(
        'app/code/core/Mage/GoogleAnalytics/',
    ),

    'Mage_GoogleCheckout' => array(
        'app/code/core/Mage/GoogleCheckout/',
    ),

    'Mage_Install' => array(
        'app/code/core/Mage/Install/',
        'app/design/install/default/default/layout/',
        'app/design/install/default/default/template/',
    ),

    'Mage_Log' => array(
        'app/code/core/Mage/Log/',
    ),

    'Mage_Newsletter' => array(
        'app/code/core/Mage/Newsletter/',
        'app/design/frontend/default/default/template/newsletter/',
    ),

    'Mage_Page' => array(
        'app/code/core/Mage/Page/',
        'app/design/frontend/default/default/template/page/',
    ),

    'Mage_Paygate' => array(
        'app/code/core/Mage/Paygate/',
    ),

    'Mage_Payment' => array(
        'app/code/core/Mage/Payment/',
        'app/design/frontend/default/default/template/payment/',
    ),

    'Mage_Paypal' => array(
        'app/code/core/Mage/Paypal/',
        'app/design/frontend/default/default/template/paypal/',
    ),

    'Mage_Poll' => array(
        'app/code/core/Mage/Poll/',
        'app/design/frontend/default/default/template/poll/',
    ),

    'Mage_Rating' => array(
        'app/code/core/Mage/Rating/',
        'app/design/frontend/default/default/template/rating/',
    ),

    'Mage_Reports' => array(
        'app/code/core/Mage/Reports/',
    ),

    'Mage_Review' => array(
        'app/code/core/Mage/Review/',
        'app/design/frontend/default/default/template/review/',
    ),

    'Mage_Rule' => array(
        'app/code/core/Mage/Rule/',
    ),

    'Mage_Sales' => array(
        'app/code/core/Mage/Sales/',
        'app/design/frontend/default/default/template/email/order/',
        'app/design/frontend/default/default/template/sales/',
    ),

    'Mage_SalesRule' => array(
        'app/code/core/Mage/SalesRule/',
    ),

    'Mage_SearchLucene' => array(
        'app/code/core/Mage/SearchLucene/',
        'app/design/frontend/default/default/template/searchlucene/',
    ),

    'Mage_Shipping' => array(
        'app/code/core/Mage/Shipping/',
    ),

    'Mage_Tag' => array(
        'app/code/core/Mage/Tag/',
        'app/design/frontend/default/default/template/tag/',
    ),

    'Mage_Tax' => array(
        'app/code/core/Mage/Tax/',
    ),

    'Mage_Usa' => array(
        'app/code/core/Mage/Usa/',
    ),

    'Mage_Wishlist' => array(
        'app/code/core/Mage/Wishlist/',
        'app/design/frontend/default/default/template/wishlist/',
    ),

    'translate' => array(
        'app/design/frontend/default/default/template/callouts/',
    ),

);

$CONFIG['helpers']  = array(
    'adminhtml'         => 'Mage_Adminhtml',
    'backup'            => 'Mage_Backup',
    'catalog'           => 'Mage_Catalog',
    'cataloginventory'  => 'Mage_CatalogInventory',
    'catalogrule'       => 'Mage_CatalogRule',
    'catalogsearch'     => 'Mage_CatalogSearch',
    'checkout'          => 'Mage_Checkout',
    'cms'               => 'Mage_Cms',
    'core'              => 'Mage_Core',
    'customer'          => 'Mage_Customer',
    'customeralert'     => 'Mage_CustomerAlert',
    'directory'         => 'Mage_Directory',
    'eav'               => 'Mage_Eav',
    'giftcert'          => 'Mage_Giftcert',
    'giftmessage'       => 'Mage_GiftMessage',
    'googleanalytics'   => 'Mage_GoogleAnalytics',
    'googlecheckout'    => 'Mage_GoogleCheckout',
    'install'           => 'Mage_Install',
    'newsletter'        => 'Mage_Newsletter',
    'page'              => 'Mage_Page',
    'paygate'           => 'Mage_Paygate',
    'payment'           => 'Mage_Payment',
    'paypal'            => 'Mage_Paypal',
    'poll'              => 'Mage_Poll',
    'rating'            => 'Mage_Rating',
    'reports'           => 'Mage_Reports',
    'review'            => 'Mage_Review',
    'rule'              => 'Mage_Rule',
    'sales'             => 'Mage_Sales',
    'salesrule'         => 'Mage_SalesRule',
    'searchlucene'      => 'Mage_SearchLucene',
    'shipping'          => 'Mage_Shipping',
    'tag'               => 'Mage_Tag',
    'tax'               => 'Mage_Tax',
    'urlrewrite'        => 'Mage_Urlrewrite',
    'usa'               => 'Mage_Usa',
    'wishlist'          => 'Mage_Wishlist',
);