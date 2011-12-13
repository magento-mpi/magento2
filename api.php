<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
if (version_compare(phpversion(), '5.2.0', '<')) {
    echo 'It looks like you have an invalid PHP version. Magento supports PHP 5.2.0 or newer';
    exit;
}
error_reporting(E_ALL | E_STRICT);

require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo 'Application is not installed yet, please complete install wizard first.';
    exit;
}

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

//enable show errors
#ini_set('display_errors', 1);

Mage::$headersSentThrowsException = false;
Mage::init('admin');

/* @var $server Mage_Api_Model_Server */
$server = Mage::getSingleton('api/server');

// query parameter "type" is set by .htaccess rewrite rule
$adapterAlias = Mage::app()->getRequest()->getParam('type');
$adapterCode = $server->getAdapterCodeByAlias($adapterAlias);

// if no adapters found in aliases - find it by default, by code
if (null === $adapterCode) {
    $adapterCode = $adapterAlias;
}

$server->initialize($adapterCode);
$server->run();

Mage::app()->getResponse()->sendResponse();
