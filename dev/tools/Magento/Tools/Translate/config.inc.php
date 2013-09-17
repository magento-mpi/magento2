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
    'mage' => 'app/code/Magento/'
);

$CONFIG['translates'] = array(
    'Magento_AdminNotification' => array(
        'app/code/Magento/AdminNotification/',
    ),
    'Magento_Backup' => array(
        'app/code/Magento/Backup/',
    ),
    'Magento_Bundle' => array(
        'app/code/Magento/Bundle/',
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
    'Magento_Checkout' => array(
        'app/code/Magento/Checkout/',
        'app/design/frontend/base/default/template/checkout/',
        'app/design/frontend/base/default/layout/checkout.xml',
        'app/design/frontend/default/modern/template/checkout/',
        'app/design/frontend/default/modern/layout/checkout.xml',
    ),
    'Magento_Chronopay' => array(
        'app/code/Magento/Chronopay/',
        'app/design/frontend/base/default/template/chronopay/',
        'app/design/frontend/base/default/layout/chronopay.xml',
    ),
    'Magento_Cms' => array(
        'app/code/Magento/Cms/',
        'app/design/frontend/base/default/template/cms/',
        'app/design/frontend/base/default/layout/cms.xml'
    ),
    'Magento_Compiler' => array(
        'app/code/Magento/Compiler/',
        'app/design/adminhtml/default/default/template/compiler/',
        'app/design/adminhtml/default/default/layout/compiler.xml',
    ),
    'Magento_Connect' => array(
        'app/code/Magento/Connect/',
        'app/design/adminhtml/default/default/template/connect/',
        'app/design/adminhtml/default/default/layout/connect.xml',
    ),
    'Magento_Contacts' => array(
        'app/code/Magento/Contacts/',
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
    'Magento_Cron' => array(
        'app/code/Magento/Cron/',
    ),
    'Magento_Customer' => array(
        'app/code/Magento/Customer/',
        'app/design/frontend/base/default/template/customer/',
        'app/design/frontend/base/default/layout/customer.xml',
        'app/design/frontend/default/modern/layout/customer.xml',
    ),
    'Magento_Cybermut' => array(
        'app/code/Magento/Cybermut/',
        'app/design/frontend/base/default/template/cybermut/',
        'app/design/frontend/base/default/layout/cybermut.xml',
    ),
    'Magento_Cybersource' => array(
        'app/code/Magento/Cybersource/',
        'app/design/frontend/base/default/template/cybersource/',
    ),
    'Magento_Directory' => array(
        'app/code/Magento/Directory/',
        'app/design/frontend/base/default/template/directory/',
        'app/design/frontend/base/default/layout/directory.xml'
    ),
    'Magento_Downloadable' => array(
        'app/code/Magento/Downloadable/',
        'app/design/frontend/base/default/template/downloadable/',
        'app/design/frontend/base/default/layout/downloadable.xml',
        'app/design/adminhtml/default/default/template/downloadable/',
    ),
    'Magento_Eav' => array(
        'app/code/Magento/Eav/',
    ),
    'Magento_Eway' => array(
        'app/code/Magento/Eway/',
        'app/design/frontend/base/default/template/eway/',
        'app/design/frontend/base/default/layout/eway.xml',
        'app/design/adminhtml/default/default/template/eway/',
    ),
    'Magento_Flo2Cash' => array(
        'app/code/Magento/Flo2Cash/',
        'app/design/frontend/base/default/template/flo2cash/',
        'app/design/adminhtml/default/default/template/flo2cash/',
    ),
    'Magento_GiftMessage' => array(
        'app/code/Magento/GiftMessage/',
        'app/design/frontend/base/default/template/giftmessage/'
    ),
    'Magento_GoogleAnalytics' => array(
        'app/code/Magento/GoogleAnalytics/',
        'app/design/frontend/base/default/layout/googleanalytics.xml'
    ),
    'Magento_GoogleBase' => array(
        'app/code/Magento/GoogleBase/',
        'app/design/adminhtml/default/default/template/googlebase/',
    ),
    'Magento_GoogleCheckout' => array(
        'app/code/Magento/GoogleCheckout/',
        'app/design/frontend/base/default/layout/googlecheckout.xml'
    ),
    'Magento_GoogleOptimizer' => array(
        'app/code/Magento/GoogleOptimizer/',
        'app/design/frontend/base/default/layout/googleoptimizer.xml',
        'app/design/adminhtml/default/default/layout/googleoptimizer.xml',
        'app/design/adminhtml/default/default/template/googleoptimizer/',
    ),
    'Magento_GoogleShopping' => array(
        'app/code/Magento/GoogleShopping/',
        'app/design/adminhtml/default/default/template/googleshopping/',
    ),
    'Magento_Ideal' => array(
        'app/code/Magento/Ideal/',
        'app/design/frontend/base/default/template/ideal/',
        'app/design/frontend/base/default/layout/ideal.xml',
        'app/design/adminhtml/default/default/template/ideal/',
    ),
    'Magento_Index' => array(
        'app/code/Magento/Index/',
        'app/design/adminhtml/default/default/layout/index.xml',
        'app/design/adminhtml/default/default/template/index/',
    ),
    'Magento_Install' => array(
        'app/code/Magento/Install/',
        'app/design/install/default/default/layout/',
        'app/design/install/default/default/template/',
    ),
    'Magento_Log' => array(
        'app/code/Magento/Log/',
    ),
    'Magento_Media' => array(
        'app/code/Magento/Media/',
    ),
    'Magento_Newsletter' => array(
        'app/code/Magento/Newsletter/',
        'app/design/frontend/base/default/template/newsletter/',
        'app/design/frontend/base/default/layout/newsletter.xml',
        'app/design/frontend/default/modern/template/newsletter/',
        'app/design/frontend/default/modern/layout/newsletter.xml',
    ),
    'Magento_Ogone' => array(
        'app/code/Magento/Ogone/',
        'app/design/frontend/base/default/layout/ogone.xml',
        'app/design/frontend/base/default/template/ogone/',
    ),
    'Magento_Page' => array(
        'app/code/Magento/Page/',
        'app/design/frontend/base/default/template/page/',
        'app/design/frontend/base/default/layout/page.xml',
        'app/design/frontend/default/modern/template/page/',
        'app/design/frontend/default/modern/layout/page.xml',
    ),
    'Magento_PageCache' => array(
        'app/code/Magento/PageCache/',
        'app/design/frontend/base/default/template/pagecache/',
        'app/design/frontend/base/default/layout/pagecache.xml',
        'app/design/adminhtml/default/default/template/pagecache/',
        'app/design/adminhtml/default/default/layout/pagecache.xml'
    ),
    'Magento_Captcha' => array(
        'app/code/Magento/Captcha/',
        'app/design/frontend/base/default/template/captcha/',
        'app/design/frontend/base/default/layout/captcha.xml',
        'app/design/adminhtml/default/default/template/captcha/',
        'app/design/adminhtml/default/default/layout/captcha.xml'
    ),
    'Magento_Paybox' => array(
        'app/code/Magento/Paybox/',
        'app/design/frontend/base/default/template/paybox/',
        'app/design/frontend/base/default/layout/paybox.xml',
        'app/design/adminhtml/default/default/template/paybox/',
    ),
    'Magento_Paygate' => array(
        'app/code/Magento/Paygate/',
    ),
    'Magento_Payment' => array(
        'app/code/Magento/Payment/',
        'app/design/frontend/base/default/template/payment/'
    ),
    'Magento_Paypal' => array(
        'app/code/Magento/Paypal/',
        'app/design/frontend/base/default/template/paypal/',
        'app/design/frontend/base/default/layout/paypal.xml',
    ),
    'Magento_PaypalUk' => array(
        'app/code/Magento/Paypal/',
        'app/design/frontend/base/default/layout/paypaluk.xml',
    ),
    'Magento_Persistent' => array(
        'app/code/Magento/Persistent/',
        'app/design/frontend/base/default/layout/persistent.xml',
        'app/design/frontend/base/default/template/persistent/'
    ),
    'Magento_ProductAlert' => array(
        'app/code/Magento/ProductAlert/',
        'app/design/frontend/base/default/template/email/productalert/',
        'app/design/frontend/base/default/template/productalert/',
        'app/design/frontend/base/default/layout/productalert.xml',
    ),
    'Magento_Protx' => array(
        'app/code/Magento/Protx/',
        'app/design/frontend/base/default/template/protx/',
        'app/design/frontend/base/default/layout/protx.xml',
    ),
    'Magento_Rating' => array(
        'app/code/Magento/Rating/',
        'app/design/frontend/base/default/template/rating/',
    ),
    'Magento_Reports' => array(
        'app/code/Magento/Reports/',
        'app/design/frontend/base/default/template/reports/',
        'app/design/frontend/base/default/layout/reports.xml',
    ),
    'Magento_Review' => array(
        'app/code/Magento/Review/',
        'app/design/frontend/base/default/template/review/',
        'app/design/frontend/base/default/layout/review.xml',
        'app/design/frontend/default/modern/layout/review.xml',
    ),
    'Magento_Rss' => array(
        'app/code/Magento/Rss/',
        'app/design/frontend/base/default/template/rss/',
        'app/design/frontend/base/default/layout/rss.xml',
        'app/design/frontend/default/modern/layout/rss.xml',
    ),
    'Magento_Rule' => array(
        'app/code/Magento/Rule/',
    ),
    'Magento_Sales' => array(
        'app/code/Magento/Sales/',
        'app/design/frontend/base/default/template/email/order/',
        'app/design/frontend/base/default/template/sales/',
        'app/design/frontend/base/default/layout/sales.xml',
        'app/design/frontend/default/modern/layout/sales.xml',
    ),
    'Magento_SalesRule' => array(
        'app/code/Magento/SalesRule/',
    ),
    'Magento_Sendfriend' => array(
        'app/code/Magento/Sendfriend/',
        'app/design/frontend/base/default/template/sendfriend/',
        'app/design/frontend/base/default/layout/sendfriend.xml',
        'app/design/frontend/default/modern/layout/sendfriend.xml',
    ),
    'Magento_Shipping' => array(
        'app/code/Magento/Shipping/',
        'app/design/frontend/base/default/template/shipping/',
        'app/design/frontend/base/default/layout/shipping.xml',
    ),
    'Magento_Sitemap' => array(
        'app/code/Magento/Sitemap/',
    ),
    'Magento_Strikeiron' => array(
        'app/code/Magento/Strikeiron/',
    ),
    'Magento_Tax' => array(
        'app/code/Magento/Tax/',
    ),
    'Magento_Usa' => array(
        'app/code/Magento/Usa/',
    ),
    'Magento_Weee' => array(
        'app/code/Magento/Weee/',
        'app/design/frontend/base/default/layout/weee.xml',
    ),
    'Magento_Wishlist' => array(
        'app/code/Magento/Wishlist/',
        'app/design/frontend/base/default/template/wishlist/',
        'app/design/frontend/base/default/layout/wishlist.xml',
        'app/design/frontend/default/modern/layout/wishlist.xml',
    ),
    'Magento_Widget' => array(
        'app/code/Magento/Widget/',
        'app/design/adminhtml/default/default/layout/widget.xml',
        'app/design/adminhtml/default/default/template/widget/',
    ),
    'translate' => array(
        'app/design/frontend/base/default/template/callouts/',
    ),
    'Magento_AdminGws' => array(
        'app/code/Magento/AdminGws/',
        'app/design/adminhtml/default/default/layout/enterprise/admingws.xml',
        'app/design/adminhtml/default/default/template/enterprise/admingws/',
    ),
    'Magento_Banner' => array(
        'app/code/Magento/Banner/',
        'app/design/adminhtml/default/default/layout/enterprise/banner.xml',
        'app/design/adminhtml/default/default/template/enterprise/banner/',
        'app/design/frontend/enterprise/default/template/banner/'
    ),
    'Magento_CatalogEvent' => array(
        'app/code/Magento/CatalogEvent/',
        'app/design/adminhtml/default/default/layout/enterprise/catalogevent.xml',
        'app/design/adminhtml/default/default/template/enterprise/catalogevent/',
        'app/design/frontend/enterprise/default/layout/catalogevent.xml',
        'app/design/frontend/enterprise/default/template/catalogevent/'
    ),
    'Magento_CatalogPermissions' => array(
        'app/code/Magento/CatalogPermissions/',
        'app/design/adminhtml/default/default/layout/enterprise/catalogpermissions.xml',
        'app/design/adminhtml/default/default/template/enterprise/catalogpermissions',
        'app/design/frontend/enterprise/default/layout/catalogpermissions.xml'
    ),
    'Magento_AdvancedCheckout' => array(
        'app/code/Magento/AdvancedCheckout/',
        'app/design/adminhtml/default/default/layout/enterprise/checkout.xml',
        'app/design/adminhtml/default/default/template/enterprise/checkout/',
        'app/design/frontend/enterprise/default/layout/checkout.xml',
        'app/design/frontend/enterprise/default/template/checkout/'
    ),
    'Magento_VersionsCms' => array(
        'app/code/Magento/VersionsCms/',
        'app/design/adminhtml/default/default/layout/enterprise/cms.xml',
        'app/design/adminhtml/default/default/template/enterprise/cms/',
        'app/design/frontend/enterprise/default/layout/cms.xml',
        'app/design/frontend/enterprise/default/template/cms/'
    ),
    'Magento_CustomerCustomAttributes' => array(
        'app/code/Magento/CustomerCustomAttributes/',
        'app/design/adminhtml/default/default/layout/enterprise/customer.xml',
        'app/design/adminhtml/default/default/template/enterprise/customerbalance/',
        'app/design/frontend/enterprise/default/layout/customer.xml',
        'app/design/frontend/enterprise/default/template/customer/'
    ),
    'Magento_CustomerBalance' => array(
        'app/code/Magento/CustomerBalance/',
        'app/design/adminhtml/default/default/layout/enterprise/customerbalance.xml',
        'app/design/adminhtml/default/default/template/enterprise/customerbalance/',
        'app/design/frontend/enterprise/default/layout/customerbalance.xml',
        'app/design/frontend/enterprise/default/template/customerbalance/'
    ),
    'Magento_CustomerSegment' => array(
        'app/code/Magento/CustomerSegment/',
        'app/design/adminhtml/default/default/layout/enterprise/customersegment.xml',
    ),
    'Magento_CustomAttribute' => array(
        'app/code/Magento/CustomAttribute/',
    ),
    'Magento_Enterprise' => array(
        'app/code/Magento/Enterprise/',
    ),
    'Magento_GiftCard' => array(
        'app/code/Magento/GiftCard/',
        'app/design/adminhtml/default/default/layout/enterprise/giftcard.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftcard/',
        'app/design/frontend/enterprise/default/layout/giftcard.xml',
        'app/design/frontend/enterprise/default/template/giftcard/'
    ),
    'Magento_GiftCardAccount' => array(
        'app/code/Magento/GiftCardAccount/',
        'app/design/adminhtml/default/default/layout/enterprise/giftcardaccount.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftcardaccount/',
        'app/design/frontend/enterprise/default/layout/giftcardaccount.xml',
        'app/design/frontend/enterprise/default/template/giftcardaccount/'
    ),
    'Magento_GiftRegistry' => array(
        'app/code/Magento/GiftRegistry/',
        'app/design/adminhtml/default/default/layout/enterprise/giftregistry.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftregistry/',
        'app/design/frontend/enterprise/default/layout/giftregistry.xml',
        'app/design/frontend/enterprise/default/template/giftregistry/'
    ),
    'Magento_GiftWrapping' => array(
        'app/code/Magento/GiftWrapping/',
        'app/design/adminhtml/default/default/layout/enterprise/giftwrapping.xml',
        'app/design/adminhtml/default/default/template/enterprise/giftwrapping/',
        'app/design/frontend/enterprise/default/layout/giftwrapping.xml',
        'app/design/frontend/enterprise/default/template/giftwrapping/'
    ),
    'Magento_ScheduledImportExport' => array(
        'app/code/Magento/ScheduledImportExport/',
        'app/design/adminhtml/default/default/layout/enterprise/importexport.xml',
        'app/design/adminhtml/default/default/template/enterprise/importexport/',
    ),
    'Magento_Invitation' => array(
        'app/code/Magento/Invitation/',
        'app/design/adminhtml/default/default/layout/enterprise/invitation.xml',
        'app/design/adminhtml/default/default/template/enterprise/invitation/',
        'app/design/frontend/enterprise/default/layout/invitation.xml',
        'app/design/frontend/enterprise/default/template/invitation/'
    ),
    'Magento_License' => array(
        'app/code/Magento/License/',
        'app/design/adminhtml/default/default/layout/enterprise/license.xml',
        'app/design/adminhtml/default/default/template/enterprise/license/',
    ),
    'Magento_Logging' => array(
        'app/code/Magento/Logging/',
        'app/design/adminhtml/default/default/layout/enterprise/logging.xml',
        'app/design/adminhtml/default/default/template/enterprise/logging/',
    ),
    'Magento_FullPageCache' => array(
        'app/code/Magento/FullPageCache/'
    ),
    'Magento_Pbridge' => array(
        'app/code/Magento/Pbridge/',
        'app/design/adminhtml/default/default/layout/enterprise/pbridge.xml',
        'app/design/adminhtml/default/default/template/enterprise/pbridge/',
        'app/design/frontend/enterprise/default/layout/pbridge.xml',
        'app/design/frontend/enterprise/default/template/pbridge/'
    ),
    'Magento_Pci' => array(
        'app/code/Magento/Pci/',
        'app/design/adminhtml/default/default/layout/enterprise/pci.xml',
    ),
    'Magento_PersistentHistory' => array(
        'app/code/Magento/PersistentHistory/',
        'app/design/frontend/enterprise/default/template/persistent/'
    ),
    'Magento_PricePermissions' => array(
        'app/code/Magento/PricePermissions/',
    ),
    'Magento_PromotionPermissions' => array(
        'app/code/Magento/PromotionPermissions/',
    ),
    'Magento_Reminder' => array(
        'app/code/Magento/Reminder/',
        'app/design/adminhtml/default/default/layout/enterprise/reminder.xml'
    ),
    'Magento_Reward' => array(
        'app/code/Magento/Reward/',
        'app/design/adminhtml/default/default/layout/enterprise/reward.xml',
        'app/design/adminhtml/default/default/template/enterprise/reward/',
        'app/design/frontend/enterprise/default/layout/reward.xml',
        'app/design/frontend/enterprise/default/template/reward/'
    ),
    'Magento_Rma' => array(
        'app/code/Magento/Rma/',
        'app/design/adminhtml/default/default/layout/enterprise/rma.xml',
        'app/design/adminhtml/default/default/template/enterprise/rma/',
        'app/design/frontend/enterprise/default/layout/rma.xml',
        'app/design/frontend/enterprise/default/template/rma/'
    ),
    'Magento_SalesArchive' => array(
        'app/code/Magento/SalesArchive/',
        'app/design/adminhtml/default/default/layout/enterprise/salesarchive.xml'
    ),
    'Magento_Search' => array(
        'app/code/Magento/Search/'
    ),
    'Magento_TargetRule' => array(
        'app/code/Magento/TargetRule/',
        'app/design/adminhtml/default/default/layout/enterprise/targetrule.xml',
        'app/design/adminhtml/default/default/template/enterprise/targetrule/',
        'app/design/frontend/enterprise/default/layout/targetrule.xml',
        'app/design/frontend/enterprise/default/template/targetrule/'
    ),
    'Magento_WebsiteRestriction' => array(
        'app/code/Magento/WebsiteRestriction/',
        'app/design/frontend/enterprise/default/layout/websiterestriction.xml'
    ),
    'Magento_MultipleWishlist' => array(
        'app/code/Magento/MultipleWishlist/',
        'app/design/adminhtml/default/default/layout/enterprise/wishlist.xml',
        'app/design/adminhtml/default/default/template/enterprise/wishlist/',
        'app/design/frontend/enterprise/default/layout/magento_multiplewishlist.xml',
        'app/design/frontend/enterprise/default/template/wishlist/'
    ),
    'Magento_Adminhtml' => array(
        'app/code/Magento/Admin/',
        'app/code/Magento/Adminhtml/',
        'app/design/adminhtml/default/default/layout/',
        'app/design/adminhtml/default/default/template/',
        '!app/design/adminhtml/default/default/layout/enterprise/', // ! = exclude
        '!app/design/adminhtml/default/default/template/enterprise/', // ! = exclude
    ),
    'Magento_Webapi' => array(
        'app/code/Magento/Webapi/',
        'app/design/adminhtml/default/default/template/webapi/',
        'app/design/adminhtml/default/default/layout/webapi.xml',
    ),
    'Magento_Oauth' => array(
        'app/code/Magento/Oauth/',
        'app/design/adminhtml/default/default/template/oauth/',
        'app/design/adminhtml/default/default/layout/oauth.xml',
        'app/design/frontend/base/default/template/oauth/',
        'app/design/frontend/base/default/layout/oauth.xml',
        'app/design/frontend/enterprise/default/template/oauth/',
        'app/design/frontend/enterprise/default/layout/oauth.xml',
    ),
    'Magento_ImportExport' => array(
        'app/code/Magento/ImportExport/',
        'app/design/adminhtml/default/default/template/importexport',
        'app/design/adminhtml/default/default/layout/importexport.xml',
    ),
);

$CONFIG['helpers']  = array(
    'adminhtml'         => 'Magento_Adminhtml',
    'adminnotification' => 'Magento_AdminNotification',
    'webapi'            => 'Magento_Webapi',
    'oauth'             => 'Magento_Oauth',
    'importexport'      => 'Magento_ImportExport',
    'backup'            => 'Magento_Backup',
    'bundle'            => 'Magento_Bundle',
    'catalog'           => 'Magento_Catalog',
    'cataloginventory'  => 'Magento_CatalogInventory',
    'catalogrule'       => 'Magento_CatalogRule',
    'catalogsearch'     => 'Magento_CatalogSearch',
    'centinel'          => 'Magento_Centinel',
    'checkout'          => 'Magento_Checkout',
    'chronopay'         => 'Magento_Chronopay',
    'cms'               => 'Magento_Cms',
    'compiler'          => 'Magento_Compiler',
    'connect'           => 'Magento_Connect',
    'contacts'          => 'Magento_Contacts',
    'core'              => 'Magento_Core',
    'cron'              => 'Magento_Cron',
    'customer'          => 'Magento_Customer',
    'cybermut'          => 'Magento_Cybermut',
    'cybersource'       => 'Magento_Cybersource',
    'directory'         => 'Magento_Directory',
    'downloadable'      => 'Magento_Downloadable',
    'eav'               => 'Magento_Eav',
    'eway'              => 'Magento_Eway',
    'flo2cash'          => 'Magento_Flo2Cash',
    'giftmessage'       => 'Magento_GiftMessage',
    'googleanalytics'   => 'Magento_GoogleAnalytics',
    'googlebase'        => 'Magento_GoogleBase',
    'googlecheckout'    => 'Magento_GoogleCheckout',
    'googleoptimizer'   => 'Magento_GoogleOptimizer',
    'googleshopping'    => 'Magento_GoogleShopping',
    'ideal'             => 'Magento_Ideal',
    'index'             => 'Magento_Index',
    'install'           => 'Magento_Install',
    'log'               => 'Magento_Log',
    'media'             => 'Magento_Media',
    'newsletter'        => 'Magento_Newsletter',
    'ogone'             => 'Magento_Ogone',
    'page'              => 'Magento_Page',
    'pagecache'         => 'Magento_PageCache',
    'captcha'           => 'Magento_Captcha',
    'paybox'            => 'Magento_Paybox',
    'paygate'           => 'Magento_Paygate',
    'payment'           => 'Magento_Payment',
    'paypal'            => 'Magento_Paypal',
    'paypaluk'          => 'Magento_PaypalUk',
    'persistent'        => 'Magento_Persistent',
    'productalert'      => 'Magento_ProductAlert',
    'protx'             => 'Magento_Protx',
    'rating'            => 'Magento_Rating',
    'reports'           => 'Magento_Reports',
    'review'            => 'Magento_Review',
    'rss'               => 'Magento_Rss',
    'rule'              => 'Magento_Rule',
    'sales'             => 'Magento_Sales',
    'salesrule'         => 'Magento_SalesRule',
    'sendfriend'        => 'Magento_Sendfriend',
    'shipping'          => 'Magento_Shipping',
    'sitemap'           => 'Magento_Sitemap',
    'strikeiron'        => 'Magento_Strikeiron',
    'tax'               => 'Magento_Tax',
    'usa'               => 'Magento_Usa',
    'weee'              => 'Magento_Weee',
    'wishlist'          => 'Magento_Wishlist',
    'widget'            => 'Magento_Widget',
    'magento_admingws'           => 'Magento_AdminGws',
    'magento_banner'             => 'Magento_Banner',
    'magento_catalogevent'       => 'Magento_CatalogEvent',
    'magento_catalogpermissions' => 'Magento_CatalogPermissions',
    'magento_advancedcheckout'           => 'Magento_AdvancedCheckout',
    'magento_versionscms'                => 'Magento_VersionsCms',
    'magento_customercustomattributes'           => 'Magento_CustomerCustomAttributes',
    'magento_customerbalance'    => 'Magento_CustomerBalance',
    'magento_customersegment'    => 'Magento_CustomerSegment',
    'magento_customattribute'                => 'Magento_CustomAttribute',
    'magento_enterprise'         => 'Magento_Enterprise',
    'magento_giftcard'           => 'Magento_GiftCard',
    'magento_giftcardaccount'    => 'Magento_GiftCardAccount',
    'magento_giftregistry'       => 'Magento_GiftRegistry',
    'magento_giftwrapping'       => 'Magento_GiftWrapping',
    'magento_scheduledimportexport'       => 'Magento_ScheduledImportExport',
    'magento_invitation'         => 'Magento_Invitation',
    'magento_license'            => 'Magento_License',
    'magento_logging'            => 'Magento_Logging',
    'magento_fullpagecache'          => 'Magento_FullPageCache',
    'magento_pbridge'            => 'Magento_Pbridge',
    'magento_pci'                => 'Magento_Pci',
    'magento_persistenthistory'         => 'Magento_PersistentHistory',
    'magento_pricepermissions'   => 'Magento_PricePermissions',
    'magento_promotionpermissions' => 'Magento_PromotionPermissions',
    'magento_reminder'           => 'Magento_Reminder',
    'magento_reward'             => 'Magento_Reward',
    'magento_rma'                => 'Magento_Rma',
    'magento_salesarchive'       => 'Magento_SalesArchive',
    'magento_search'             => 'Magento_Search',
    'magento_targetrule'         => 'Magento_TargetRule',
    'magento_websiterestriction' => 'Magento_WebsiteRestriction',
    'magento_multiplewishlist'           => 'Magento_MultipleWishlist',
);

