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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product rule mysql4 resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Moshe Gurvich (moshe@varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Rule extends Mage_Rule_Model_Mysql4_Rule
{

    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
        $this->_ruleTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_rule');
        
        $this->_ruleIdField = 'product_rule_id';
        $this->_ruleTableFields = array('product_rule_id', 'name', 'description', 'is_active', 'start_at', 'expire_at', 'customer_registered', 'customer_new_buyer',  'sort_order', 'conditions_serialized', 'actions_serialized');
    }
}