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
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
    'Mage_AdminNotification' => array(
        'app/code/core/Mage/AdminNotification/',
    ),
    'Mage_Backup' => array(
        'app/code/core/Mage/Backup/',
    ),
    'Mage_Bundle' => array(
        'app/code/core/Mage/Bundle/',
        'app/design/frontend/default/default/template/bundle/',
        'app/design/frontend/default/default/layout/bundle.xml',
        'app/design/adminhtml/default/default/template/bundle/',
    ),
    'Mage_Catalog' => array(
        'app/code/core/Mage/Catalog/',
        'app/design/frontend/default/default/template/catalog/',
        'app/design/frontend/default/default/layout/catalog.xml',
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
        'app/design/frontend/default/default/layout/catalogsearch.xml',
    ),
    'Mage_Checkout' => array(
        'app/code/core/Mage/Checkout/',
        'app/design/frontend/default/default/template/checkout/',
        'app/design/frontend/default/default/layout/checkout.xml',
    ),
    'Mage_Chronopay' => array(
        'app/code/core/Mage/Chronopay/',
        'app/design/frontend/default/default/template/chronopay/',
        'app/design/frontend/default/default/layout/chronopay.xml',
    ),
    'Mage_Cms' => array(
        'app/code/core/Mage/Cms/',
        'app/design/frontend/default/default/template/cms/',
        'app/design/frontend/default/default/layout/cms.xml',
    ),
    'Mage_Contacts' => array(
        'app/code/core/Mage/Contacts/',
        'app/design/frontend/default/default/template/contacts/',
        'app/design/frontend/default/default/layout/contacts.xml',
    ),
    'Mage_Core' => array(
        'app/code/core/Mage/Core/',
        'app/design/frontend/default/default/template/core/',
        'app/design/frontend/default/default/layout/core.xml',
    ),
    'Mage_Cron' => array(
        'app/code/core/Mage/Cron/',
    ),
    'Mage_Customer' => array(
        'app/code/core/Mage/Customer/',
        'app/design/frontend/default/default/template/customer/',
        'app/design/frontend/default/default/layout/customer.xml',
    ),
    'Mage_Cybermut' => array(
        'app/code/core/Mage/Cybermut/',
        'app/design/frontend/default/default/template/cybermut/',
        'app/design/frontend/default/default/layout/cybermut.xml',
    ),
    'Mage_Cybersource' => array(
        'app/code/core/Mage/Cybersource/',
        'app/design/frontend/default/default/template/cybersource/',
    ),
    'Mage_Dataflow' => array(
        'app/code/core/Mage/Dataflow/',
    ),
    'Mage_Directory' => array(
        'app/code/core/Mage/Directory/',
        'app/design/frontend/default/default/template/directory/',
        'app/design/frontend/default/default/layout/directory.xml',
    ),
    'Mage_Eav' => array(
        'app/code/core/Mage/Eav/',
    ),
    'Mage_Eway' => array(
        'app/code/core/Mage/Eway/',
        'app/design/frontend/default/default/template/eway/',
        'app/design/frontend/default/default/layout/eway.xml',
        'app/design/adminhtml/default/default/template/eway/',
    ),
    'Mage_Flo2Cash' => array(
        'app/code/core/Mage/Flo2Cash/',
        'app/design/frontend/default/default/template/flo2cash/',
        'app/design/adminhtml/default/default/template/flo2cash/',
    ),
    'Mage_GiftMessage' => array(
        'app/code/core/Mage/GiftMessage/',
        'app/design/frontend/default/default/template/giftmessage/',
        'app/design/frontend/default/default/layout/giftmessage.xml',
    ),
    'Mage_GoogleAnalytics' => array(
        'app/code/core/Mage/GoogleAnalytics/',
        'app/design/frontend/default/default/layout/googleanalytics.xml',
    ),
    'Mage_GoogleCheckout' => array(
        'app/code/core/Mage/GoogleCheckout/',
        'app/design/frontend/default/default/layout/googlecheckout.xml',
    ),
    'Mage_GoogleOptimizer' => array(
        'app/code/core/Mage/GoogleOptimizer/',
        'app/design/frontend/default/default/layout/googleoptimizer.xml',
        'app/design/adminhtml/default/default/layout/googleoptimizer.xml',
        'app/design/adminhtml/default/default/template/googleoptimizer/',
    ),
    'Mage_Ideal' => array(
        'app/code/core/Mage/Ideal/',
        'app/design/frontend/default/default/template/ideal/',
        'app/design/frontend/default/default/layout/ideal.xml',
        'app/design/adminhtml/default/default/template/ideal/',
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
        'app/design/frontend/default/default/layout/newsletter.xml',
    ),
    'Mage_Page' => array(
        'app/code/core/Mage/Page/',
        'app/design/frontend/default/default/template/page/',
        'app/design/frontend/default/default/layout/page.xml',
    ),
    'Mage_Paybox' => array(
        'app/code/core/Mage/Paybox/',
        'app/design/frontend/default/default/template/paybox/',
        'app/design/frontend/default/default/layout/paybox.xml',
        'app/design/adminhtml/default/default/template/paybox/',
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
        'app/design/frontend/default/default/template/paypaluk/',
        'app/design/frontend/default/default/layout/paypaluk.xml',
    ),
    'Mage_PaypalUk' => array(
        'app/code/core/Mage/Paypal/',
        'app/design/frontend/default/default/template/paypal/',
        'app/design/frontend/default/default/layout/paypal.xml',
    ),
    'Mage_Poll' => array(
        'app/code/core/Mage/Poll/',
        'app/design/frontend/default/default/template/poll/',
        'app/design/frontend/default/default/layout/poll.xml',
    ),
    'Mage_ProductAlert' => array(
        'app/code/core/Mage/ProductAlert/',
        'app/design/frontend/default/default/template/email/productalert/',
        'app/design/frontend/default/default/template/productalert/',
        'app/design/frontend/default/default/layout/productalert.xml',
    ),
    'Mage_Protx' => array(
        'app/code/core/Mage/Protx/',
        'app/design/frontend/default/default/template/protx/',
        'app/design/frontend/default/default/layout/protx.xml',
    ),
    'Mage_Rating' => array(
        'app/code/core/Mage/Rating/',
        'app/design/frontend/default/default/template/rating/',
    ),
    'Mage_Reports' => array(
        'app/code/core/Mage/Reports/',
        'app/design/frontend/default/default/template/reports/',
        'app/design/frontend/default/default/layout/reports.xml',
    ),
    'Mage_Review' => array(
        'app/code/core/Mage/Review/',
        'app/design/frontend/default/default/template/review/',
        'app/design/frontend/default/default/layout/review.xml',
    ),
    'Mage_Rss' => array(
        'app/code/core/Mage/Rss/',
        'app/design/frontend/default/default/template/rss/',
        'app/design/frontend/default/default/layout/rss.xml',
    ),
    'Mage_Rule' => array(
        'app/code/core/Mage/Rule/',
    ),
    'Mage_Sales' => array(
        'app/code/core/Mage/Sales/',
        'app/design/frontend/default/default/template/email/order/',
        'app/design/frontend/default/default/template/sales/',
        'app/design/frontend/default/default/layout/sales.xml',
    ),
    'Mage_SalesRule' => array(
        'app/code/core/Mage/SalesRule/',
    ),
    'Mage_Sendfriend' => array(
        'app/code/core/Mage/Sendfriend/',
        'app/design/frontend/default/default/template/sendfriend/',
        'app/design/frontend/default/default/layout/sendfriend.xml',
    ),
    'Mage_Shipping' => array(
        'app/code/core/Mage/Shipping/',
        'app/design/frontend/default/default/template/shipping/',
        'app/design/frontend/default/default/layout/shipping.xml',
    ),
    'Mage_Sitemap' => array(
        'app/code/core/Mage/Sitemap/',
    ),
    'Mage_Tag' => array(
        'app/code/core/Mage/Tag/',
        'app/design/frontend/default/default/template/tag/',
        'app/design/frontend/default/default/layout/tag.xml',
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
        'app/design/frontend/default/default/layout/wishlist.xml',
    ),
    'translate' => array(
        'app/design/frontend/default/default/template/callouts/',
    ),

);

$CONFIG['helpers']  = array(
    'adminhtml'         => 'Mage_Adminhtml',
    'adminnotification' => 'Mage_AdminNotification',
    'backup'            => 'Mage_Backup',
    'bundle'            => 'Mage_Bundle',
    'catalog'           => 'Mage_Catalog',
    'cataloginventory'  => 'Mage_CatalogInventory',
    'catalogrule'       => 'Mage_CatalogRule',
    'catalogsearch'     => 'Mage_CatalogSearch',
    'checkout'          => 'Mage_Checkout',
    'chronopay'         => 'Mage_Chronopay',
    'cms'               => 'Mage_Cms',
    'contacts'          => 'Mage_Contacts',
    'core'              => 'Mage_Core',
    'cron'              => 'Mage_Cron',
    'customer'          => 'Mage_Customer',
    'cybermut'          => 'Mage_Cybermut',
    'cybersource'       => 'Mage_Cybersource',
    'dataflow'          => 'Mage_Dataflow',
    'directory'         => 'Mage_Directory',
    'eav'               => 'Mage_Eav',
    'eway'              => 'Mage_Eway',
    'flo2cash'          => 'Mage_Flo2Cash',
    'giftmessage'       => 'Mage_GiftMessage',
    'googleanalytics'   => 'Mage_GoogleAnalytics',
    'googlecheckout'    => 'Mage_GoogleCheckout',
    'googleoptimizer'   => 'Mage_GoogleOptimizer',
    'ideal'             => 'Mage_Ideal',
    'install'           => 'Mage_Install',
    'log'               => 'Mage_Log',
    'media'             => 'Mage_Media',
    'newsletter'        => 'Mage_Newsletter',
    'oscommerce'        => 'Mage_Oscommerce',
    'page'              => 'Mage_Page',
    'paybox'            => 'Mage_Paybox',
    'paygate'           => 'Mage_Paygate',
    'payment'           => 'Mage_Payment',
    'paypal'            => 'Mage_Paypal',
    'paypaluk'          => 'Mage_PaypalUk',
    'poll'              => 'Mage_Poll',
    'productalert'      => 'Mage_ProductAlert',
    'protx'             => 'Mage_Protx',
    'rating'            => 'Mage_Rating',
    'reports'           => 'Mage_Reports',
    'review'            => 'Mage_Review',
    'rss'               => 'Mage_Rss',
    'rule'              => 'Mage_Rule',
    'sales'             => 'Mage_Sales',
    'salesrule'         => 'Mage_SalesRule',
    'sendfriend'        => 'Mage_Sendfriend',
    'shipping'          => 'Mage_Shipping',
    'sitemap'           => 'Mage_Sitemap',
    'tag'               => 'Mage_Tag',
    'tax'               => 'Mage_Tax',
    'usa'               => 'Mage_Usa',
    'wishlist'          => 'Mage_Wishlist',
);
