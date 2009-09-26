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
updateLicense('app/code/core/Enterprise', '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'coreCodePoolCallback', true, true, true);

// xml-code files
updateLicense('app/code/core/Enterprise', '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'coreCodePoolCallback', true, true, true);

// modules xml-declarations
updateLicense('app/etc/modules', 'Enterprise_*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'xmlModulesCallback', true, true, true);

// design phtml-files
updateLicense(array(
        'app/design/adminhtml/default/default/template/enterprise',
        'app/design/frontend/enterprise/default/template',
        'app/design/frontend/enterprise/blank/template'
    ), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'themeCallback', true, true, true
);

// skins
updateLicense(array(
        'skin/frontend/enterprise',
        '!skin/frontend/enterprise/blank/js/jqzoom', // ! = skip
        '!skin/frontend/enterprise/default/js/jqzoom', // ! = skip
        'skin/install/default/enterprise'
    ), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, array('design', 'enterprise_default'), true, true, true
);

// layouts
updateLicense(array(
        'app/design/adminhtml/default/default/layout/enterprise',
        'app/design/frontend/enterprise/default/layout',
        'app/design/frontend/enterprise/blank/layout',
    ), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'themeCallback', true, true, true
);

// additional javascript
echo updateLicense('js/enterprise/adminhtml', '*.js', REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE,
    array('design', 'default_default'), true, true, true
);

exit;
