<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

define("EXTENSION",'csv');
$CONFIG['allow_extensions'] = array('php','xml','phtml','csv');
$CONFIG['paths'] = array(
    'locale' => 'app/locale/',
    'mage' => 'app/code/Mage/'
);

$CONFIG['translates'] = array(
    'Mage_AdminNotification' => array(
        'app/code/Mage/AdminNotification/',
    ),
    'Mage_Backup' => array(
        'app/code/Mage/Backup/',
    ),
    'Mage_Bundle' => array(
        'app/code/Mage/Bundle/',
        'app/design/frontend/base/default/template/bundle/',
        'app/design/frontend/base/default/layout/bundle.xml',
        'app/design/adminhtml/default/default/template/bundle/',
    ),
    'Magento_Catalog' => array(
        'app/code/Magento/Catalog/',
        'app/design/frontend/base/default/template/catalog/',
        'app/design/frontend/base/default/layout/catalog.xml',
        'app/design/frontend/default/modern/template/catalog/',
        'app/design/frontend/default/modern/layout/catalog.xml',
    ),
    'Magento_CatalogInventory' => array(
        'app/code/Magento/CatalogInventory/',
    ),
    'Magento_CatalogRule' => array(
        'app/code/Magento/CatalogRule/',
    ),
    'Magento_CatalogSearch' => array(
        'app/code/Magento/CatalogSearch/',
        'app/design/frontend/base/default/template/catalogsearch/',
        'app/design/frontend/base/default/layout/catalogsearch.xml',
        'app/design/frontend/default/modern/template/catalogsearch/',
        'app/design/frontend/default/modern/layout/catalogsearch.xml',
    ),
    'Magento_Centinel' => array(
        'app/code/Magento/Centinel/',
        'app/design/frontend/base/default/template/centinel/',
        'app/design/frontend/base/default/layout/centinel.xml',
        'app/design/adminhtml/default/default/layout/centinel.xml',
        'app/design/adminhtml/default/default/template/centinel/',
    ),
    'Mage_Checkout' => array(
        'app/code/Mage/Checkout/',
        'app/design/frontend/base/default/template/checkout/',
        'app/design/frontend/base/default/layout/checkout.xml',
        'app/design/frontend/default/modern/template/checkout/',
        'app/design/frontend/default/modern/layout/checkout.xml',
    ),
    'Mage_Chronopay' => array(
        'app/code/Mage/Chronopay/',
        'app/design/frontend/base/default/template/chronopay/',
        'app/design/frontend/base/default/layout/chronopay.xml',
    ),
    'Mage_Cms' => array(
        'app/code/Mage/Cms/',
        'app/design/frontend/base/default/template/cms/',
        'app/design/frontend/base/default/layout/cms.xml'
    ),
    'Mage_Compiler' => array(
        'app/code/Mage/Compiler/',
        'app/design/adminhtml/default/default/template/compiler/',
        'app/design/adminhtml/default/default/layout/compiler.xml',
    ),
    'Mage_Connect' => array(
        'app/code/Mage/Connect/',
        'app/design/adminhtml/default/default/template/connect/',
        'app/design/adminhtml/default/default/layout/connect.xml',
    ),
    'Mage_Contacts' => array(
        'app/code/Mage/Contacts/',
        'app/design/frontend/base/default/template/contacts/',
        'app/design/frontend/base/default/layout/contacts.xml',
        'app/design/frontend/default/modern/layout/contacts.xml',
    ),
    'Magento_Core' => array(
        'app/code/Magento/Core/',
        'app/code/Magento/Core/view/frontend',
        'app/code/Magento/Core/view/adminhtml',
        'app/code/Magento/Core/view/frontend/layout.xml',
        'lib/Zend/Validate/',
    ),
    'Mage_Cron' => array(
        'app/code/Mage/Cron/',
    ),
    'Mage_Customer' => array(
        'app/code/Mage/Customer/',
        'app/design/frontend/base/default/template/customer/',
        'app/design/frontend/base/default/layout/customer.xml',
        'app/design/frontend/default/modern/layout/customer.xml',
    ),
    'Mage_Cybermut' => array(
        'app/code/Mage/Cybermut/',
        'app/design/frontend/base/default/template/cybermut/',
        'app/design/frontend/base/default/layout/cybermut.xml',
    ),
    'Mage_Cybersource' => array(
        'app/code/Mage/Cybersource/',
        'app/design/frontend/base/default/template/cybersource/',
    ),
    'Mage_Directory' => array(
        'app/code/Mage/Directory/',
        'app/design/frontend/base/default/template/directory/',
        'app/design/frontend/base/default/layout/directory.xml'
    ),
    'Mage_Downloadable' => array(
        'app/code/Mage/Downloadable/',
        'app/design/frontend/base/default/template/downloadable/',
        'app/design/frontend/base/default/layout/downloadable.xml',
        'app/design/adminhtml/default/default/template/downloadable/',
    ),
    'Mage_Eav' => array(
        'app/code/Mage/Eav/',
    ),
    'Mage_Eway' => array(
        'app/code/Mage/Eway/',
        'app/design/frontend/base/default/template/eway/',
        'app/design/frontend/base/default/layout/eway.xml',
        'app/design/adminhtml/default/default/template/eway/',
    ),
    'Mage_Flo2Cash' => array(
        'app/code/Mage/Flo2Cash/',
        'app/design/frontend/base/default/template/flo2cash/',
        'app/design/adminhtml/default/default/template/flo2cash/',
    ),
    'Mage_GiftMessage' => array(
        'app/code/Mage/GiftMessage/',
        'app/design/frontend/base/default/template/giftmessage/'
    ),
    'Magento_GoogleAnalytics' => array(
        'app/code/Magento/GoogleAnalytics/',
        'app/design/frontend/base/default/layout/googleanalytics.xml'
    ),
    'Mage_GoogleBase' => array(
        'app/code/Mage/GoogleBase/',
        'app/design/adminhtml/default/default/template/googlebase/',
    ),
    'Mage_GoogleCheckout' => array(
        'app/code/Mage/GoogleCheckout/',
        'app/design/frontend/base/default/layout/googlecheckout.xml'
    ),
    'Mage_GoogleOptimizer' => array(
        'app/code/Mage/GoogleOptimizer/',
        'app/design/frontend/base/default/layout/googleoptimizer.xml',
        'app/design/adminhtml/default/default/layout/googleoptimizer.xml',
        'app/design/adminhtml/default/default/template/googleoptimizer/',
    ),
    'Mage_GoogleShopping' => array(
        'app/code/Mage/GoogleShopping/',
        'app/design/adminhtml/default/default/template/googleshopping/',
    ),
    'Mage_Ideal' => array(
        'app/code/Mage/Ideal/',
        'app/design/frontend/base/default/template/ideal/',
        'app/design/frontend/base/default/layout/ideal.xml',
        'app/design/adminhtml/default/default/template/ideal/',
    ),
    'Mage_Index' => array(
        'app/code/Mage/Index/',
        'app/design/adminhtml/default/default/layout/index.xml',
        'app/design/adminhtml/default/default/template/index/',
    ),
    'Magento_Install' => array(
        'app/code/Magento/Install/',
        'app/design/install/default/default/layout/',
        'app/design/install/default/default/template/',
    ),
    'Mage_Log' => array(
        'app/code/Mage/Log/',
    ),
    'Mage_Media' => array(
        'app/code/Mage/Media/',
    ),
    'Mage_Newsletter' => array(
        'app/code/Mage/Newsletter/',
        'app/design/frontend/base/default/template/newsletter/',
        'app/design/frontend/base/default/layout/newsletter.xml',
        'app/design/frontend/default/modern/template/newsletter/',
        'app/design/frontend/default/modern/layout/newsletter.xml',
    ),
    'Mage_Ogone' => array(
        'app/code/Mage/Ogone/',
        'app/design/frontend/base/default/layout/ogone.xml',
        'app/design/frontend/base/default/template/ogone/',
    ),
    'Mage_Page' => array(
        'app/code/Mage/Page/',
        'app/design/frontend/base/default/template/page/',
        'app/design/frontend/base/default/layout/page.xml',
        'app/design/frontend/default/modern/template/page/',
        'app/design/frontend/default/modern/layout/page.xml',
    ),
    'Mage_PageCache' => array(
        'app/code/Mage/PageCache/',
        'app/design/frontend/base/default/template/pagecache/',
        'app/design/frontend/base/default/layout/pagecache.xml',
        'app/design/adminhtml/default/default/template/pagecache/',
        'app/design/adminhtml/default/default/layout/pagecache.xml'
    ),
    'Mage_Captcha' => array(
        'app/code/Mage/Captcha/',
        'app/design/frontend/base/default/template/captcha/',
        'app/design/frontend/base/default/layout/captcha.xml',
        'app/design/adminhtml/default/default/template/captcha/',
        'app/design/adminhtml/default/default/layout/captcha.xml'
    ),
    'Mage_Paybox' => array(
        'app/code/Mage/Paybox/',
        'app/design/frontend/base/default/template/paybox/',
        'app/design/frontend/base/default/layout/paybox.xml',
        'app/design/adminhtml/default/default/template/paybox/',
    ),
    'Mage_Paygate' => array(
        'app/code/Mage/Paygate/',
    ),
    'Mage_Payment' => array(
        'app/code/Mage/Payment/',
        'app/design/frontend/base/default/template/payment/'
    ),
    'Mage_Paypal' => array(
        'app/code/Mage/Paypal/',
        'app/design/frontend/base/default/template/paypal/',
        'app/design/frontend/base/default/layout/paypal.xml',
    ),
    'Mage_PaypalUk' => array(
        'app/code/Mage/Paypal/',
        'app/design/frontend/base/default/layout/paypaluk.xml',
    ),
    'Mage_Persistent' => array(
        'app/code/Mage/Persistent/',
        'app/design/frontend/base/default/layout/persistent.xml',
        'app/design/frontend/base/default/template/persistent/'
    ),
    'Mage_Poll' => array(
        'app/code/Mage/Poll/',
        'app/design/frontend/base/default/template/poll/',
        'app/design/frontend/base/default/layout/poll.xml',
    ),
    'Mage_ProductAlert' => array(
        'app/code/Mage/ProductAlert/',
        'app/design/frontend/base/default/template/email/productalert/',
        'app/design/frontend/base/default/template/productalert/',
        'app/design/frontend/base/default/layout/productalert.xml',
    ),
    'Mage_Protx' => array(
        'app/code/Mage/Protx/',
        'app/design/frontend/base/default/template/protx/',
        'app/design/frontend/base/default/layout/protx.xml',
    ),
    'Mage_Rating' => array(
        'app/code/Mage/Rating/',
        'app/design/frontend/base/default/template/rating/',
    ),
    'Mage_Reports' => array(
        'app/code/Mage/Reports/',
        'app/design/frontend/base/default/template/reports/',
        'app/design/frontend/base/default/layout/reports.xml',
    ),
    'Mage_Review' => array(
        'app/code/Mage/Review/',
        'app/design/frontend/base/default/template/review/',
        'app/design/frontend/base/default/layout/review.xml',
        'app/design/frontend/default/modern/layout/review.xml',
    ),
    'Mage_Rss' => array(
        'app/code/Mage/Rss/',
        'app/design/frontend/base/default/template/rss/',
        'app/design/frontend/base/default/layout/rss.xml',
        'app/design/frontend/default/modern/layout/rss.xml',
    ),
    'Mage_Rule' => array(
        'app/code/Mage/Rule/',
    ),
    'Mage_Sales' => array(
        'app/code/Mage/Sales/',
        'app/design/frontend/base/default/template/email/order/',
        'app/design/frontend/base/default/template/sales/',
        'app/design/frontend/base/default/layout/sales.xml',
        'app/design/frontend/default/modern/layout/sales.xml',
    ),
    'Mage_SalesRule' => array(
        'app/code/Mage/SalesRule/',
    ),
    'Mage_Sendfriend' => array(
        'app/code/Mage/Sendfriend/',
        'app/design/frontend/base/default/template/sendfriend/',
        'app/design/frontend/base/default/layout/sendfriend.xml',
        'app/design/frontend/default/modern/layout/sendfriend.xml',
    ),
    'Mage_Shipping' => array(
        'app/code/Mage/Shipping/',
        'app/design/frontend/base/default/template/shipping/',
        'app/design/frontend/base/default/layout/shipping.xml',
    ),
    'Mage_Sitemap' => array(
        'app/code/Mage/Sitemap/',
    ),
    'Mage_Strikeiron' => array(
        'app/code/Mage/Strikeiron/',
    ),
    'Mage_Tag' => array(
        'app/code/Mage/Tag/',
        'app/design/frontend/base/default/template/tag/',
        'app/design/frontend/base/default/layout/tag.xml',
        'app/design/frontend/default/modern/layout/tag.xml',
    ),
    'Mage_Tax' => array(
        'app/code/Mage/Tax/',
    ),
    'Mage_Usa' => array(
        'app/code/Mage/Usa/',
    ),
    'Mage_Weee' => array(
        'app/code/Mage/Weee/',
        'app/design/frontend/base/default/layout/weee.xml',
    ),
    'Mage_Wishlist' => array(
        'app/code/Mage/Wishlist/',
        'app/design/frontend/base/default/template/wishlist/',
        'app/design/frontend/base/default/layout/wishlist.xml',
        'app/design/frontend/default/modern/layout/wishlist.xml',
    ),
    'Mage_Widget' => array(
        'app/code/Mage/Widget/',
        'app/design/adminhtml/default/default/layout/widget.xml',
        'app/design/adminhtml/default/default/template/widget/',
    ),
    'translate' => array(
        'app/design/frontend/base/default/template/callouts/',
    ),
    'Enterprise_AdminGws' => array(
        'app/code/Enterprise/AdminGws/',
        'app/design/adminhtml/default/default/layout/enterprise/admingws.xml',
        'app/design/adminhtml/default/default/template/enterprise/admingws/',
    ),
    'Enterprise_Banner' => array(
        'app/code/Enterprise/Banner/',
        'app/design/adminhtml/default/default/layout/enterprise/banner.xml',
        'app/design/adminhtml/default/default/template/enterprise/banner/',
        'app/design/frontend/enterprise/default/template/banner/'
    ),
    'Enterprise_CatalogEvent' => array(
        'app/code/Enterprise/CatalogEvent/',
        'app/design/adminhtml/default/default/layout/enterprise/catalogevent.xml',
        'app/design/adminhtml/default/default/template/enterprise/catalogevent/',
        'app/design/frontend/enterprise/default/layout/catalogevent.xml',
        'app/design/frontend/enterprise/default/template/catalogevent/'
    ),
    'Enterprise_CatalogPermissions' => array(
        'app/code/Enterprise/CatalogPermissions/',
        'app/design/adminhtml/default/default/layout/enterprise/catalogpermissions.xml',
        'app/design/adminhtml/default/default/template/enterprise/catalogpermissions',
        'app/design/frontend/enterprise/default/layout/catalogpermissions.xml'
    ),
    'Enterprise_Checkout' => array(
        'app/code/Enterprise/Checkout/',
        'app/design/adminhtml/default/default/layout/enterprise/checkout.xml',
        'app/design/adminhtml/default/default/template/enterprise/checkout/',
        'app/design/frontend/enterprise/default/layout/checkout.xml',
        'app/design/frontend/enterprise/default/template/checkout/'
    ),
    'Enterprise_Cms' => array(
        'app/code/Enterprise/Cms/',
        'app/design/adminhtml/default/default/layout/enterprise/cms.xml',
        'app/design/adminhtml/default/default/template/enterprise/cms/',
        'app/design/frontend/enterprise/default/layout/cms.xml',
        'app/design/frontend/enterprise/default/template/cms/'
    ),
    'Enterprise_Customer' => array(
        'app/code/Enterprise/Customer/',
        'app/design/adminhtml/default/default/layout/enterprise/customer.xml',
        'app/design/adminhtml/default/default/template/enterprise/customerbalance/',
        'app/design/frontend/enterprise/default/layout/customer.xml',
        'app/design/frontend/enterprise/default/template/customer/'
    ),
    'Enterprise_CustomerBalance' => array(
        'app/code/Enterprise/CustomerBalance/',
        'app/design/adminhtml/default/default/layout/enterprise/customerbalance.xml',
        'app/design/adminhtml/default/default/template/enterprise/customerbalance/',
        'app/design/frontend/enterprise/default/layout/customerbalance.xml',
        'app/design/frontend/enterprise/default/template/customerbalance/'
    ),
    'Enterprise_CustomerSegment' => array(
        'app/code/Enterprise/CustomerSegment/',
        'app/design/adminhtml/default/default/layout/enterprise/customersegment.xml',
    ),
    'Enterprise_Eav' => array(
        'app/code/Enterprise/Eav/',
    ),
    'Enterprise_Enterprise' => array(
        'app/code/Enterprise/Enterprise/',
    ),
    'Enterprise_GiftCard' => array(
        'app/code/Enterprise/GiftCard/',
        'app/design/adminhtml/default/default/layout/enterprise/giftcard.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftcard/',
        'app/design/frontend/enterprise/default/layout/giftcard.xml',
        'app/design/frontend/enterprise/default/template/giftcard/'
    ),
    'Enterprise_GiftCardAccount' => array(
        'app/code/Enterprise/GiftCardAccount/',
        'app/design/adminhtml/default/default/layout/enterprise/giftcardaccount.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftcardaccount/',
        'app/design/frontend/enterprise/default/layout/giftcardaccount.xml',
        'app/design/frontend/enterprise/default/template/giftcardaccount/'
    ),
    'Enterprise_GiftRegistry' => array(
        'app/code/Enterprise/GiftRegistry/',
        'app/design/adminhtml/default/default/layout/enterprise/giftregistry.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftregistry/',
        'app/design/frontend/enterprise/default/layout/giftregistry.xml',
        'app/design/frontend/enterprise/default/template/giftregistry/'
    ),
    'Enterprise_GiftWrapping' => array(
        'app/code/Enterprise/GiftWrapping/',
        'app/design/adminhtml/default/default/layout/enterprise/giftwrapping.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftwrapping/',
        'app/design/frontend/enterprise/default/layout/giftwrapping.xml',
        'app/design/frontend/enterprise/default/template/giftwrapping/'
    ),
    'Enterprise_ImportExport' => array(
        'app/code/Enterprise/ImportExport/',
        'app/design/adminhtml/default/default/layout/enterprise/importexport.xml',
        'app/design/adminhtml/default/default/template/enterprise/importexport/',
    ),
    'Enterprise_Invitation' => array(
        'app/code/Enterprise/Invitation/',
        'app/design/adminhtml/default/default/layout/enterprise/invitation.xml',
        'app/design/adminhtml/default/default/template/enterprise/invitation/',
        'app/design/frontend/enterprise/default/layout/invitation.xml',
        'app/design/frontend/enterprise/default/template/invitation/'
    ),
    'Enterprise_License' => array(
        'app/code/Enterprise/License/',
        'app/design/adminhtml/default/default/layout/enterprise/license.xml',
        'app/design/adminhtml/default/default/template/enterprise/license/',
    ),
    'Enterprise_Logging' => array(
        'app/code/Enterprise/Logging/',
        'app/design/adminhtml/default/default/layout/enterprise/logging.xml',
        'app/design/adminhtml/default/default/template/enterprise/logging/',
    ),
    'Enterprise_PageCache' => array(
        'app/code/Enterprise/PageCache/'
    ),
    'Enterprise_Pbridge' => array(
        'app/code/Enterprise/Pbridge/',
        'app/design/adminhtml/default/default/layout/enterprise/pbridge.xml',
        'app/design/adminhtml/default/default/template/enterprise/pbridge/',
        'app/design/frontend/enterprise/default/layout/pbridge.xml',
        'app/design/frontend/enterprise/default/template/pbridge/'
    ),
    'Enterprise_Pci' => array(
        'app/code/Enterprise/Pci/',
        'app/design/adminhtml/default/default/layout/enterprise/pci.xml',
    ),
    'Enterprise_Persistent' => array(
        'app/code/Enterprise/Persistent/',
        'app/design/frontend/enterprise/default/template/persistent/'
    ),
    'Enterprise_PricePermissions' => array(
        'app/code/Enterprise/PricePermissions/',
    ),
    'Enterprise_PromotionPermissions' => array(
        'app/code/Enterprise/PromotionPermissions/',
    ),
    'Enterprise_Reminder' => array(
        'app/code/Enterprise/Reminder/',
        'app/design/adminhtml/default/default/layout/enterprise/reminder.xml'
    ),
    'Enterprise_Reward' => array(
        'app/code/Enterprise/Reward/',
        'app/design/adminhtml/default/default/layout/enterprise/reward.xml',
        'app/design/adminhtml/default/default/template/enterprise/reward/',
        'app/design/frontend/enterprise/default/layout/reward.xml',
        'app/design/frontend/enterprise/default/template/reward/'
    ),
    'Enterprise_Rma' => array(
        'app/code/Enterprise/Rma/',
        'app/design/adminhtml/default/default/layout/enterprise/rma.xml',
        'app/design/adminhtml/default/default/template/enterprise/rma/',
        'app/design/frontend/enterprise/default/layout/rma.xml',
        'app/design/frontend/enterprise/default/template/rma/'
    ),
    'Enterprise_SalesArchive' => array(
        'app/code/Enterprise/SalesArchive/',
        'app/design/adminhtml/default/default/layout/enterprise/salesarchive.xml'
    ),
    'Enterprise_Search' => array(
        'app/code/Enterprise/Search/'
    ),
    'Enterprise_TargetRule' => array(
        'app/code/Enterprise/TargetRule/',
        'app/design/adminhtml/default/default/layout/enterprise/targetrule.xml',
        'app/design/adminhtml/default/default/template/enterprise/targetrule/',
        'app/design/frontend/enterprise/default/layout/targetrule.xml',
        'app/design/frontend/enterprise/default/template/targetrule/'
    ),
    'Enterprise_WebsiteRestriction' => array(
        'app/code/Enterprise/WebsiteRestriction/',
        'app/design/frontend/enterprise/default/layout/websiterestriction.xml'
    ),
    'Enterprise_Wishlist' => array(
        'app/code/Enterprise/Wishlist/',
        'app/design/adminhtml/default/default/layout/enterprise/wishlist.xml',
        'app/design/adminhtml/default/default/template/enterprise/wishlist/',
        'app/design/frontend/enterprise/default/layout/enterprise_wishlist.xml',
        'app/design/frontend/enterprise/default/template/wishlist/'
    ),
    'Magento_Adminhtml' => array(
        'app/code/Mage/Admin/',
        'app/code/Magento/Adminhtml/',
        'app/design/adminhtml/default/default/layout/',
        'app/design/adminhtml/default/default/template/',
        '!app/design/adminhtml/default/default/layout/enterprise/', // ! = exclude
        '!app/design/adminhtml/default/default/template/enterprise/', // ! = exclude
    ),
    'Mage_Api' => array(
        'app/code/Mage/Api/',
        'app/design/adminhtml/default/default/template/api/',
        '!app/design/adminhtml/default/default/template/enterprise/', // ! = exclude
    ),
    'Mage_Webapi' => array(
        'app/code/Mage/Webapi/',
        'app/design/adminhtml/default/default/template/webapi/',
        'app/design/adminhtml/default/default/layout/webapi.xml',
    ),
    'Mage_Oauth' => array(
        'app/code/Mage/Oauth/',
        'app/design/adminhtml/default/default/template/oauth/',
        'app/design/adminhtml/default/default/layout/oauth.xml',
        'app/design/frontend/base/default/template/oauth/',
        'app/design/frontend/base/default/layout/oauth.xml',
        'app/design/frontend/enterprise/default/template/oauth/',
        'app/design/frontend/enterprise/default/layout/oauth.xml',
    ),
    'Mage_ImportExport' => array(
        'app/code/Mage/Mage_ImportExport/',
        'app/design/adminhtml/default/default/template/importexport',
        'app/design/adminhtml/default/default/layout/importexport.xml',
    ),
    'Enterprise_Tag' => array(
        'app/code/Enterprise/Tag/',
    ),
);

$CONFIG['helpers']  = array(
    'adminhtml'         => 'Magento_Adminhtml',
    'adminnotification' => 'Mage_AdminNotification',
    'api'               => 'Mage_Api',
    'webapi'              => 'Mage_Webapi',
    'oauth'             => 'Mage_Oauth',
    'importexport'      => 'Mage_ImportExport',
    'backup'            => 'Mage_Backup',
    'bundle'            => 'Mage_Bundle',
    'catalog'           => 'Magento_Catalog',
    'cataloginventory'  => 'Magento_CatalogInventory',
    'catalogrule'       => 'Magento_CatalogRule',
    'catalogsearch'     => 'Magento_CatalogSearch',
    'centinel'          => 'Magento_Centinel',
    'checkout'          => 'Mage_Checkout',
    'chronopay'         => 'Mage_Chronopay',
    'cms'               => 'Mage_Cms',
    'compiler'          => 'Mage_Compiler',
    'connect'           => 'Mage_Connect',
    'contacts'          => 'Mage_Contacts',
    'core'              => 'Magento_Core',
    'cron'              => 'Mage_Cron',
    'customer'          => 'Mage_Customer',
    'cybermut'          => 'Mage_Cybermut',
    'cybersource'       => 'Mage_Cybersource',
    'directory'         => 'Mage_Directory',
    'downloadable'      => 'Mage_Downloadable',
    'eav'               => 'Mage_Eav',
    'eway'              => 'Mage_Eway',
    'flo2cash'          => 'Mage_Flo2Cash',
    'giftmessage'       => 'Mage_GiftMessage',
    'googleanalytics'   => 'Magento_GoogleAnalytics',
    'googlebase'        => 'Mage_GoogleBase',
    'googlecheckout'    => 'Mage_GoogleCheckout',
    'googleoptimizer'   => 'Mage_GoogleOptimizer',
    'googleshopping'    => 'Mage_GoogleShopping',
    'ideal'             => 'Mage_Ideal',
    'index'             => 'Mage_Index',
    'install'           => 'Magento_Install',
    'log'               => 'Mage_Log',
    'media'             => 'Mage_Media',
    'newsletter'        => 'Mage_Newsletter',
    'ogone'             => 'Mage_Ogone',
    'page'              => 'Mage_Page',
    'pagecache'         => 'Mage_PageCache',
    'captcha'           => 'Mage_Captcha',
    'paybox'            => 'Mage_Paybox',
    'paygate'           => 'Mage_Paygate',
    'payment'           => 'Mage_Payment',
    'paypal'            => 'Mage_Paypal',
    'paypaluk'          => 'Mage_PaypalUk',
    'persistent'        => 'Mage_Persistent',
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
    'strikeiron'        => 'Mage_Strikeiron',
    'tag'               => 'Mage_Tag',
    'tax'               => 'Mage_Tax',
    'usa'               => 'Mage_Usa',
    'weee'              => 'Mage_Weee',
    'wishlist'          => 'Mage_Wishlist',
    'widget'            => 'Mage_Widget',
    'enterprise_admingws'           => 'Enterprise_AdminGws',
    'enterprise_banner'             => 'Enterprise_Banner',
    'enterprise_catalogevent'       => 'Enterprise_CatalogEvent',
    'enterprise_catalogpermissions' => 'Enterprise_CatalogPermissions',
    'enterprise_checkout'           => 'Enterprise_Checkout',
    'enterprise_cms'                => 'Enterprise_Cms',
    'enterprise_customer'           => 'Enterprise_Customer',
    'enterprise_customerbalance'    => 'Enterprise_CustomerBalance',
    'enterprise_customersegment'    => 'Enterprise_CustomerSegment',
    'enterprise_eav'                => 'Enterprise_Eav',
    'enterprise_enterprise'         => 'Enterprise_Enterprise',
    'enterprise_giftcard'           => 'Enterprise_GiftCard',
    'enterprise_giftcardaccount'    => 'Enterprise_GiftCardAccount',
    'enterprise_giftregistry'       => 'Enterprise_GiftRegistry',
    'enterprise_giftwrapping'       => 'Enterprise_GiftWrapping',
    'enterprise_importexport'       => 'Enterprise_ImportExport',
    'enterprise_invitation'         => 'Enterprise_Invitation',
    'enterprise_license'            => 'Enterprise_License',
    'enterprise_logging'            => 'Enterprise_Logging',
    'enterprise_pagecache'          => 'Enterprise_PageCache',
    'enterprise_pbridge'            => 'Enterprise_Pbridge',
    'enterprise_pci'                => 'Enterprise_Pci',
    'enterprise_persistent'         => 'Enterprise_Persistent',
    'enterprise_pricepermissions'   => 'Enterprise_PricePermissions',
    'enterprise_promotionpermissions' => 'Enterprise_PromotionPermissions',
    'enterprise_reminder'           => 'Enterprise_Reminder',
    'enterprise_reward'             => 'Enterprise_Reward',
    'enterprise_rma'                => 'Enterprise_Rma',
    'enterprise_salesarchive'       => 'Enterprise_SalesArchive',
    'enterprise_search'             => 'Enterprise_Search',
    'enterprise_targetrule'         => 'Enterprise_TargetRule',
    'enterprise_websiterestriction' => 'Enterprise_WebsiteRestriction',
    'enterprise_wishlist'           => 'Enterprise_Wishlist',
);

