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
updateLicense(array(
    'app/code/core/Mage',
), '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_OSL, 'codePoolCallback', true, true, true);

// xml-code files
updateLicense(array(
    'app/code/core/Mage',
), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'codePoolCallback', true, true, true);
updateLicense(null, array('app/etc/config.xml'), REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, array('Mage', 'Mage_Core'), true, true, true);

// modules xml-declarations
updateLicense(array(
    'app/etc/modules',
), 'Mage_*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'xmlModulesCallback', true, true, true);

// design phtml-files
updateLicense(array(
        'app/design/adminhtml/default/default/template',
        '!app/design/adminhtml/default/default/template/enterprise', // "!" = skip
        '!app/design/adminhtml/default/default/template/moneybookers', // "!" = skip
        'app/design/frontend/base/default/template',
        '!app/design/frontend/base/default/template/moneybookers', // "!" = skip
        'app/design/frontend/default/modern/template',
        'app/design/frontend/default/iphone/template',
        'app/design/install/default/default/template',
    ), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_AFL, 'themeCallback', true, true, true
);

// design layouts
updateLicense(array(
        'app/design/adminhtml/default/default/layout',
        '!app/design/adminhtml/default/default/layout/enterprise', // "!" = skip
        '!app/design/adminhtml/default/default/layout/moneybookers.xml', // "!" = skip
        'app/design/frontend/base/default/layout',
        '!app/design/frontend/base/default/layout/moneybookers.xml', // "!" = skip
        'app/design/frontend/default/modern/layout',
        'app/design/frontend/default/iphone/layout',
        'app/design/install/default/default/layout',

        'app/design/frontend/base/default/etc',
        'app/design/frontend/default/default/etc',
        'app/design/frontend/default/modern/etc',
        'app/design/frontend/default/blank/etc',
        'app/design/frontend/default/iphone/etc',
    ), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'themeCallback',  true, true, true
);

//The Find
// php-code files
updateLicense(array('app/code/community/Find'), '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_OSL, 'codePoolCallback', true, true, true);
// xml files
updateLicense(array('app/code/community/Find'), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'codePoolCallback', true, true, true);
updateLicense(array('app/etc/modules'), 'Find_Feed.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'xmlModulesCallback', true, true, true);
// design phtml-files
updateLicense(array('app/design/adminhtml/default/find/template'), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_AFL, 'themeCallback', true, true, true);
// design layouts
updateLicense(array('app/design/adminhtml/default/find/layout'), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'themeCallback',  true, true, true);

// phoenix moneybookers
updateLicense('app/code/community/Phoenix/Moneybookers', '*.php', REGEX_PHP, REPLACEMENT_PHP, PHOENIX_OSL, new Callback('codePoolCallback', 'community'), true, true, true);
updateLicense('app/code/community/Phoenix/Moneybookers', '*.xml', REGEX_XML, REPLACEMENT_XML, PHOENIX_OSL, new Callback('codePoolCallback', 'community'), true, true, true);
updateLicense('app/etc/modules', 'Phoenix_Moneybookers.xml', REGEX_XML, REPLACEMENT_XML, PHOENIX_OSL, 'xmlModulesCallback', true, true, true);
updateLicense(array(
    'app/design/adminhtml/default/default/template/moneybookers',
    '!app/design/frontend/base/default/template/moneybookers',
), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, PHOENIX_OSL, 'themeCallback', true, true, true);
updateLicense(array(
    'app/design/adminhtml/default/default/layout',
    'app/design/frontend/base/default/layout'
), 'moneybookers.xml', REGEX_XML, REPLACEMENT_XML, PHOENIX_OSL, 'themeCallback', true, true, true);
updateLicense(array('js/mage/adminhtml'), array('moneybookers.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, PHOENIX_OSL, array('Phoenix', 'Phoenix_Moneybookers'), true, true, true
);

// frontend skins for default theme
updateLicense(array(
        'skin/frontend/default/default',
        'skin/frontend/default/blank',
        'skin/frontend/default/blue',
        'skin/frontend/default/french',
        'skin/frontend/default/german',
        'skin/frontend/default/iphone',
        'skin/frontend/default/modern',
    ), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('design', 'default_default'), true, true, true
);
// frontend skins for base, blank, modern and iphone themes
updateLicense('skin/frontend/base/default', array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('design', 'base_default'), true, true, true
);
updateLicense('skin/frontend/default/blank', array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('design', 'default_blank'), true, true, true
);
updateLicense('skin/frontend/default/modern', array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('design', 'default_modern'), true, true, true
);
updateLicense('skin/frontend/default/iphone', array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('design', 'default_iphone'), true, true, true
);

// errors/
updateLicense(array(
    'errors', '!errors/enterprise'
), '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_OSL, array('Mage', 'Errors'), true, true, true);
updateLicense(array(
    'errors', '!errors/enterprise', '!errors/design.xml'
), array('*.xml', '*.sample'), REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, array('Mage', 'Errors'), true, true, true);
updateLicense(array(
    'errors', '!errors/enterprise', '!errors/pro'
), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_AFL, array('Mage', 'Errors'), true, true, true);
updateLicense(array(
    'errors', '!errors/enterprise', '!errors/pro'
), '*.css', REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('Mage', 'Errors'), true, true, true);

// js/mage and js/varien
updateLicense(array('js/mage', '!js/mage/adminhtml'), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('Mage', 'js'), true, true, true
);
updateLicense(null, array(
    'js/lib/dropdown.js',
    'js/lib/flex.js',
    ), REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('Mage', 'js'), true, true, true
);
updateLicense(array('js/mage/adminhtml', '!js/mage/adminhtml/moneybookers.js'), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('Mage', 'Mage_Adminhtml'), true, true, true
);
updateLicense('js/varien', '*.js',
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('Varien', 'js'), true, true, true
);

//downloader
updateLicense("downloader/Maged", "*.php", REGEX_PHP, REPLACEMENT_PHP, NOTICE_OSL, 'codeDownloaderCallback', true, true, true);
updateLicense("downloader/lib", "*.php", REGEX_PHP, REPLACEMENT_PHP, NOTICE_OSL, 'libCallback', true, true, true);
updateLicense(null, array("downloader/index.php", "downloader/mage.php"), REGEX_PHP, REPLACEMENT_PHP, NOTICE_OSL, 'codeDownloaderCallback', true, true, true);
updateLicense('downloader/skin', array('*.css', '*.js'), REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, 'skinDownloaderCallback', true, true, true);
updateLicense('downloader/template', '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_AFL, 'themeDownloaderCallback', true, true, true);

echo "done\n";
exit;
