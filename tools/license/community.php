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
updateLicense('app/code/core/Mage', '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_OSL, 'coreCodePoolCallback', true, true, true);

// xml-code files
updateLicense('app/code/core/Mage', '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'coreCodePoolCallback', true, true, true);

// modules xml-declarations
updateLicense('app/etc/modules', 'Mage_*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'xmlModulesCallback', true, true, true);

// design phtml-files
updateLicense(array(
        'app/design/adminhtml/default/default/template',
        '!app/design/adminhtml/default/default/template/enterprise', // "!" = skip
        'app/design/frontend/base/default/template',
        'app/design/frontend/default/default/template',
        'app/design/frontend/default/blank/template',
        'app/design/frontend/default/modern/template',
        'app/design/frontend/default/iphone/template',
        'app/design/install/default/default/template',
    ), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_AFL, 'themeCallback', true, true, true
);

// design layouts
updateLicense(array(
        'app/design/adminhtml/default/default/layout',
        '!app/design/adminhtml/default/default/layout/enterprise', // "!" = skip
        'app/design/frontend/base/default/layout',
        'app/design/frontend/default/default/layout',
        'app/design/frontend/default/blank/layout',
        'app/design/frontend/default/modern/layout',
        'app/design/frontend/default/iphone/layout',
        'app/design/install/default/default/layout',
    ), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_AFL, 'themeCallback',  true, true, true
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
echo updateLicense('skin/frontend/default/iphone', array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_AFL, array('design', 'default_iphone'), true, true, true
);

exit;
