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
 * @category   Mage
 * @package    Mage_CatalogExcel
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


abstract class Mage_CatalogExcel_Model_Mysql4_Abstract
{
	protected $_skuAttribute;
	
	public function getConnection()
	{
		return Mage::getSingleton('core/resource')->getConnection('catalog_write');
	}
	
	public function getSelect()
	{
		return $this->getConnection()->select();
	}
	
	public function getTable($table)
	{
		return Mage::getSingleton('core/resource')->getTableName($table);
	}
	
	public function getSkuAttribute($field='attribute_id')
	{
		if (!$this->_skuAttribute) {
			$this->_skuAttribute = Mage::getModel('eav/entity_setup', 'eav_setup')->getAttribute('catalog_product', 'sku');
		}
		return isset($this->_skuAttribute[$field]) ? $this->_skuAttribute[$field] : null;
	}
}