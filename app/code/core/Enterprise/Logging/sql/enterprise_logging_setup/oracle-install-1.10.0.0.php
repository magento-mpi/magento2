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
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->query("
    create or replace function inet_ntoa( ip_addr in integer ) return string
    as
    begin
        return
            mod( trunc(ip_addr/256/256/256), 256 ) || '.' ||
            mod( trunc(ip_addr/256/256), 256 ) || '.' ||
            mod( trunc(ip_addr/256), 256 ) || '.' ||
            mod( ip_addr , 256 );
    end;
");

$installFile = dirname(__FILE__) . DS . 'install-1.10.0.0.php';
if (file_exists($installFile)) {
    include $installFile;
}
