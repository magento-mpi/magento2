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
updateLicense('app/code/core/Enterprise', '*.php', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'coreCodePoolCallback');

// xml-code files
updateLicense('app/code/core/Enterprise', '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'coreCodePoolCallback');

// modules xml-declarations
updateLicense('app/etc/modules', 'Enterprise_*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'xmlModulesCallback');

// design phtml-files
updateLicense(array(
        'app/design/adminhtml/default/default/template/enterprise',
        'app/design/frontend/enterprise/default/template'
    ), '*.phtml', REGEX_PHP, REPLACEMENT_PHP, NOTICE_EE, 'eePhtmlCallback'
);

// frontend skins
updateLicense('skin/frontend/enterprise/default', array('*.css', '*.js'),
    REGEX_SKIN, REPLACEMENT_SKIN, NOTICE_EE, array('design', 'enterprise_default')
);

// layouts
echo updateLicense(array(
        'app/design/adminhtml/default/default/layout/enterprise',
        'app/design/frontend/enterprise/default/layout'
    ), '*.xml', REGEX_XML, REPLACEMENT_XML, NOTICE_EE, 'eeLayoutCallback'
);

/**
 * category/package callback for EE phtml-files
 *
 * @param string $filename
 * @return array
 */
function eePhtmlCallback($filename)
{
    if (false !== strpos($filename, 'app/design/frontend/enterprise/default/template')) {
        return array('design', 'enterprise_default');
    }
    return array('design', 'default_default');
}

/**
 * category/package callback for EE layout-files
 *
 * @param string $filename
 * @return array
 */
function eeLayoutCallback($filename)
{
    if (false !== strpos($filename, 'app/design/adminhtml/default/default/layout/enterprise')) {
        return array('design', 'default_default');
    }
    return array('design', 'enterprise_default');
}
