<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Tools
 * @package    License
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

require dirname(__FILE__) . '/config.php';

// php-code files
updateLicense('app/code/core/Mage', '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'codePoolCallback');

// xml-code files
updateLicense('app/code/core/Mage', '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'codePoolCallback');
updateLicense(null, array('app/etc/config.xml'), REGEX_XML, REPLACEMENT_XML, NOTICE_EE, array('Mage', 'Mage_Core'), true, true, true);

// modules xml-declarations
updateLicense('app/etc/modules', 'Mage_*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'xmlModulesCallback');

// design phtml-files
updateLicense(array(
        'app/design/adminhtml/default/default/template',
        '!app/design/adminhtml/default/default/template/enterprise', // "!" = skip
        '!app/design/adminhtml/default/default/template/moneybookers', // "!" = skip
        'app/design/frontend/base/default/template',
        '!app/design/frontend/base/default/template/moneybookers', // "!" = skip
        'app/design/frontend/default/default/template',
        'app/design/frontend/default/modern/template',
        'app/design/frontend/default/blank/template',
        'app/design/frontend/default/iphone/template',
        'app/design/install/default/default/template',
    ), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'themeCallback'
);

// layouts and widget.xml
updateLicense(array(
        'app/design/frontend/base/default/layout',
        'app/design/adminhtml/default/default/layout',
        '!app/design/adminhtml/default/default/layout/enterprise', // "!" = skip
        '!app/design/adminhtml/default/default/layout/moneybookers.xml',  // "!" = skip
        'app/design/frontend/default/default/layout',
        '!app/design/frontend/base/default/layout/moneybookers.xml', // "!" = skip
        'app/design/frontend/default/modern/layout',
        'app/design/frontend/default/blank/layout',
        'app/design/frontend/default/iphone/layout',
        'app/design/install/default/default/layout',

        'app/design/frontend/base/default/etc',
        'app/design/frontend/default/default/etc',
        'app/design/frontend/default/modern/etc',
        'app/design/frontend/default/blank/etc',
        'app/design/frontend/default/iphone/etc',
    ), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'themeCallback'
);

// skins
updateLicense(array(
        'skin/adminhtml/default/default',
        'skin/frontend/base/default',
        'skin/frontend/default',
        'skin/install/default',
    ), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, 'skinCallback'
);

// varien lib
updateLicense(array(
    'lib/Varien',
    '!lib/Varien/Db/test.php', // skip
), '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'libCallback', true, true, true);

// misc
updateLicense(null, array(
    'app/Mage.php',
    'cron.php',
    'includes/config.php',
    'index.php',
    'index.php.sample',
    'install.php',
    'js/index.php',
), REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, array('Mage', 'Mage'), true, true, true);

updateLicense('shell', '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, array('Mage', 'Mage_Shell'), true, true, true);

// errors/
updateLicense('errors', '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, array('Mage', 'Errors'), true, true, true);
updateLicense('errors', array('*.xml', '*.sample'), REGEX_XML, REPLACEMENT_XML, NOTICE_EE, array('Mage', 'Errors'), true, true, true);
updateLicense('errors', '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, array('Mage', 'Errors'), true, true, true);
updateLicense('errors', '*.css', REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, array('Mage', 'Errors'), true, true, true);

// js/mage and js/varien
updateLicense(array('js/mage', '!js/mage/adminhtml'), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, array('Mage', 'js'), true, true, true
);
updateLicense(array('js/mage/adminhtml', '!js/mage/adminhtml/moneybookers.js'), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, array('Mage', 'Mage_Adminhtml'), true, true, true
);
updateLicense('js/varien', '*.js',
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, array('Varien', 'js'), true, true, true
);
updateLicense(null, array(
    'js/lib/dropdown.js',
    'js/lib/flex.js',
), REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, array('lib', 'js'), true, true, true);

//downloader
updateLicense("downloader/Maged", "*.php", REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'codeDownloaderCallback');
updateLicense("downloader/lib", "*.php", REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'libCallback');
updateLicense(null, array("downloader/index.php", "downloader/mage.php"), REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'codeDownloaderCallback');
updateLicense('downloader/skin', array('*.css', '*.js'), REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, 'skinDownloaderCallback');
updateLicense('downloader/template', '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'themeDownloaderCallback');

//TheFind
// php-code files
updateLicense(array('app/code/community/Find'), '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'codePoolCallback');
// xml files
updateLicense(array('app/code/community/Find'), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'codePoolCallback');
updateLicense(array('app/etc/modules'), 'Find_Feed.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'xmlModulesCallback');
// design phtml-files
updateLicense(array('app/design/adminhtml/default/find/template'), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'themeCallback');
// design layouts
updateLicense(array('app/design/adminhtml/default/find/layout'), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'themeCallback');


echo "done\n";
exit;
