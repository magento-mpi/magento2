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
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Script which helps to export data from tables in your database to fixture XML.
 * Just need to do some changes in the code and run it:
 *  - your database connection data
 *  - tables to export or SELECT queries
 *  - path to result XML file.
 */

$_rootDir = dirname(__FILE__). DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR. '..'. DIRECTORY_SEPARATOR. '..';
require_once $_rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
set_include_path( get_include_path() . PATH_SEPARATOR . dirname(__FILE__). DIRECTORY_SEPARATOR . '..');
chdir($_rootDir);

$db = Zend_Db::factory('pdo_mysql', array(
    'username' => 'root',
    'password' => '',
    'dbname' => 'trunk',
    'host' => 'localhost'
));
$db->query('SET CHARACTER SET utf8');
$db->query('SET NAMES utf8');

$exporter = Mage_PHPUnit_Db_FixtureConnection::getInstance();
$exporter->exportToXml(
    $db,
    array('core_store', 'core_store_group', 'core_website'),
    array(),
    dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_fixtures' . DIRECTORY_SEPARATOR . 'export.xml'
);
