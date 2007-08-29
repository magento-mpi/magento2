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
 * Category attributes set
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Category_Attribute_Set
{
    protected $_setTable;
    protected $_inSetTable;
    
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    
    public function __construct() 
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');

        $this->_setTable    = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_set');
        $this->_inSetTable  = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_in_set');
    }
    
    public function load($setId)
    {
        return $this->_read->fetchRow("SELECT * FROM $this->_setTable WHERE attribute_set_id=:id", array('id'=>$setId));
    }
    
    public function save(Mage_Catalog_Model_Product_Attribute_Set $set)
    {
        $this->_write->beginTransaction();
        try {
            $data = array(
                'code' => $set->getCode()
            );
            
            if ($set->getId()) {
                $condition = $this->_write->quoteInto('attribute_set_id=?', $set->getId());
                $this->_write->update($this->_setTable, $data, $condition);
            }
            else {
                $this->_write->insert($this->_setTable, $data);
                $set->setSetId($this->_write->lastInsertId());
            }
            
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    public function delete($setId)
    {
        $condition = $this->_write->quoteInto('attribute_set_id=?', $setId);
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_setTable, $condition);
            $this->_write->commit();
        }
        catch (Exception $e)
        {
            $this->_write->rollBack();
            throw $e;
        }
    }
}