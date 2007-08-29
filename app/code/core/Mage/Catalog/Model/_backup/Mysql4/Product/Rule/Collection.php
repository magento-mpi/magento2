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
 * Product rules collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Moshe Gurvich (moshe@varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Rule_Collection extends Mage_Rule_Model_Mysql4_Rule_Collection
{

    /**
     * Initialize resource collection variables
     *
     */
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        
        $ruleTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_rule');
        $this->_sqlSelect->from($ruleTable)->order('sort_order');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_rule'));
    }
    
    /**
     * Retrieve environment for the rules in collection
     *
     * @return Mage_Catalog_Model_Mysql4_Product_Rule_Collection
     */
    public function getEnv()
    {
        if (!$this->_env) {
            $this->_env = Mage::getModel('catalog/product_rule_environment');
            $this->_env->collect();
        }
        return $this->_env;
    }
    
    /**
     * Set filter for the collection based on the environment
     *
     * @return Mage_Catalog_Model_Mysql4_Product_Rule_Collection
     */
    public function setActiveFilter()
    {
        parent::setActiveFilter();
        
        $e = $this->getEnv()->getData();
        
        if (!isset($e['customer_registered'])
            || !is_numeric($reg = $e['customer_registered']) && ($reg<0 || $reg>1)) {
            $reg = 2;
        }
        $this->_sqlSelect->where("customer_registered=?", $reg);
        
        if (!isset($e['customer_new_buyer'])
            || !is_numeric($new = $e['customer_new_buyer']) && ($new<0 || $new>1)) {
            $new = 2;
        }        
        $this->_sqlSelect->where("customer_new_buyer=?", $new);
        
        return $this;
    }
}