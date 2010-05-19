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

// design phtml-files
updateLicense(array(
        'app/design/frontend/pro/default/template',
        'app/design/install/default/pro/template',
    ), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_PRO, 'themeCallback', true, true, true
);

// skins
updateLicense(array(
        'skin/frontend/pro',
        'skin/install/default/pro'
    ), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_PRO, array('design', 'pro_default'), true, true, true
);

// skins for adminhtml
updateLicense(array(
        'skin/adminhtml/default/pro'
    ), array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_PRO, array('design', 'default_pro'), true, true, true
);

// layouts
updateLicense(array(
        'app/design/frontend/pro/default/layout',
        'app/design/frontend/pro/default/etc',
    ), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_PRO, 'themeCallback', true, true, true
);

// errors/
updateLicense('errors/pro', '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_PRO, array('Mage', 'Errors'), true, true, true);
updateLicense('errors/pro', '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_PRO, array('Mage', 'Errors'), true, true, true);
updateLicense('errors/pro', '*.css', REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_PRO, array('Mage', 'Errors'), true, true, true);

echo "done\n";
exit;
