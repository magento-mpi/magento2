<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    license
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Configuration file used by licence-tool.php script to prepare  Magento Enterprise Edition
 * @var $config array of specified paths and file types with appropriate licenses
 *
 */
$config = array(
    '' => array(
        '_params' => array(
            'recursive' => false
        ),
        'php'   => 'MEL'
    ),
    'app/code/core' => array(
        'xml'   => 'MEL',
        'phtml' => 'MEL',
        'php'   => 'MEL',
        'css'   => 'MEL',
        'js'    => 'MEL',
        '_params' => array(
            'skipped' => array(
                'app/code/core/Zend/Mime.php',
                'app/code/core/Enterprise/CustomerBalance/etc/wsdl.xml',
                'app/code/core/Enterprise/CustomerBalance/etc/wsi.xml',
                'app/code/core/Enterprise/GiftCard/etc/wsdl.xml',
                'app/code/core/Enterprise/GiftCard/etc/wsi.xml',
                'app/code/core/Enterprise/GiftCardAccount/etc/wsdl.xml',
                'app/code/core/Enterprise/GiftCardAccount/etc/wsi.xml',
                'app/code/core/Mage/Api/etc/wsdl.xml',
                'app/code/core/Mage/Api/etc/wsdl2.xml',
                'app/code/core/Mage/Api/etc/wsi.xml',
                'app/code/core/Mage/Catalog/etc/wsdl.xml',
                'app/code/core/Mage/Catalog/etc/wsi.xml',
                'app/code/core/Mage/CatalogInventory/etc/wsdl.xml',
                'app/code/core/Mage/CatalogInventory/etc/wsi.xml',
                'app/code/core/Mage/Checkout/etc/wsdl.xml',
                'app/code/core/Mage/Checkout/etc/wsi.xml',
                'app/code/core/Mage/Core/etc/wsdl.xml',
                'app/code/core/Mage/Core/etc/wsi.xml',
                'app/code/core/Mage/Customer/etc/wsdl.xml',
                'app/code/core/Mage/Customer/etc/wsi.xml',
                'app/code/core/Mage/Directory/etc/wsdl.xml',
                'app/code/core/Mage/Directory/etc/wsi.xml',
                'app/code/core/Mage/Downloadable/etc/wsdl.xml',
                'app/code/core/Mage/Downloadable/etc/wsi.xml',
                'app/code/core/Mage/GiftMessage/etc/wsdl.xml',
                'app/code/core/Mage/GiftMessage/etc/wsi.xml',
                'app/code/core/Mage/GoogleCheckout/etc/wsdl.xml',
                'app/code/core/Mage/GoogleCheckout/etc/wsi.xml',
                'app/code/core/Mage/ImportExport/view/adminhtml/layout.xml',
                'app/code/core/Mage/Sales/etc/wsdl.xml',
                'app/code/core/Mage/Sales/etc/wsi.xml',
                'app/code/core/Mage/Tag/etc/wsdl.xml',
                'app/code/core/Mage/Tag/etc/wsi.xml',
            )
        )
    ),
    'app/design' => array(
        'xml'   => 'MEL',
        'phtml' => 'MEL',
        'css'   => 'MEL',
        'js'    => 'MEL',
        '_params' => array(
            'skipped' => array(
                'app/design/frontend/default/iphone/skin/default/js/dnd.js',
                'app/design/frontend/enterprise/default/skin/default/js/jqzoom/jquery-1.3.1.min.js',
                'app/design/frontend/enterprise/default/skin/default/js/jqzoom/jquery.jqzoom1.0.1.js',
            )
        )
    ),
    'app/etc' => array(
        'xml'   => 'MEL',
        '_params' => array(
            'skipped' => 'app/etc/local.xml'
        )
    ),
    'app' => array(
        'php'   => 'MEL',
        '_params' => array(
            'recursive' => false
        ),
    ),
    'app/code/community/Phoenix' => array(
        'xml'   => 'Phoenix',
        'phtml' => 'Phoenix',
        'php'   => 'Phoenix',
        'css'   => 'Phoenix',
        'js'    => 'Phoenix'
    ),
    'app/code/community/Find' => array(
        'xml'   => 'AFL',
        'phtml' => 'AFL',
        'php'   => 'OSL',
        'css'   => 'AFL',
        'js'    => 'AFL'
    ),
    'dev' => array(
        'xml'   => 'MEL',
        'phtml' => 'MEL',
        'php'   => 'MEL',
        'css'   => 'MEL',
        'js'    => 'MEL',
        '_params' => array(
            'skipped' => array(
                'dev/build',
                'dev/tests/integration/tmp',
                'dev/tests/static/report',
                'dev/tests/static/testsuite/Php',
            )
        )
    ),
    'downloader' => array(
        'xml'   => 'MEL',
        'phtml' => 'MEL',
        'php'   => 'MEL',
        'css'   => 'MEL',
        'js'    => 'MEL',
        '_params' => array(
            'skipped' => 'downloader/js/prototype.js'
        )
    ),
    'lib/Varien' => array(
        'php'   => 'MEL'
    ),
    'lib/Mage' => array(
        'php'   => 'MEL'
    ),
    'lib/Magento' => array(
        'php'   => 'MEL',
        'xml'   => 'MEL'
    ),
    'pub' => array(
        'php' => 'MEL',
        '_params' => array(
            'recursive' => false
        ),
    ),
    'pub/errors' => array(
        'xml'   => 'MEL',
        'phtml' => 'MEL',
        'php'   => 'MEL',
        'css'   => 'MEL',
        'js'    => 'MEL'
    ),
    'pub/js' => array(
        'xml'   => 'MEL',
        'php'   => 'MEL',
        'css'   => 'MEL',
        'js'    => 'MEL',
        '_params' => array(
            'skipped' => array(
                'pub/js/calendar',
                'pub/js/extjs',
                'pub/js/firebug',
                'pub/js/flash',
                'pub/js/jscolor',
                'pub/js/prototype',
                'pub/js/scriptaculous',
                'pub/js/tiny_mce',
                'pub/js/lib/ds-sleight.js',
                'pub/js/lib/ccard.js',
                'pub/js/lib/boxover.js',
                'pub/js/lib/FABridge.js',
                'pub/js/mage/adminhtml/hash.js'
            )
        )
    ),
    'pub/media' => array(
        'xml'   => 'MEL',
        'css'   => 'MEL',
        'js'    => 'MEL'
    )
);
