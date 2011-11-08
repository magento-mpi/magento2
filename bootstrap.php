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
 * @category    Mage
 * @package     Mage
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* Initialize DEV constants */
require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
date_default_timezone_set('America/Los_Angeles');

define('UNIT_ROOT', DEV_ROOT . '/tests/unit');
define('UNIT_FRAMEWORK', UNIT_ROOT . '/framework');
define('UNIT_TEMP', UNIT_ROOT . '/tmp');

if (file_exists(UNIT_FRAMEWORK . '/config.php')) {
    require_once 'config.php';
} else {
    require_once 'config.php.dist';
}

require_once DEV_APP . '/Mage.php';
require_once UNIT_FRAMEWORK . '/autoloader.php';


//need to initialize test App configuration in bootstrap
//because data providers in test cases are run before setUp() and even before setUpBeforeClass() methods in TestCase.
Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_App')->run();
