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

// modules xml-declarations
updateLicense('app/etc/modules', 'Mage_*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'xmlModulesCallback');

// design phtml-files
updateLicense(array(
        'app/design/adminhtml/base/default/template',
        'app/design/adminhtml/default/default/template',
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
        'app/design/frontend/default/default/layout',
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

echo "done\n";
exit;
