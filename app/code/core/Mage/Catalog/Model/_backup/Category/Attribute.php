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
 * Category attribute
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Category_Attribute extends Varien_Object
{
    public function __construct($data = array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('catalog/category_attribute');
    }

    public function load($attributeId)
    {
        $this->setData($this->getResource()->load($attributeId));
        return $this;
    }
    
    public function loadByCode($attributeCode)
    {
        $this->setData($this->getResource()->loadByCode($attributeCode));
        return $this;
    }
    
    public function getId()
    {
        return $this->getAttributeId();
    }
    
    public function getCode()
    {
        return $this->getAttributeCode();
    }
    
    public function isRequired()
    {
        return $this->getRequired();
    }

    public function isMultiple()
    {
        return $this->getMultiple();
    }
    
    public function getTableName()
    {
        return Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_value');
    }
    
    public function getTableAlias()
    {
        return $this->getAttributeCode();
    }
    
    public function getSelectTable()
    {
        return $this->getTableName() . ' as ' . $this->getTableAlias();
    }
    
    public function getTableColumns()
    {
        $columns = array(
            new Zend_Db_Expr($this->getTableAlias().".attribute_value AS " . $this->getCode()),
        );
        return $columns;
    }
    
    public function getSaver()
    {
        $saverName = $this->getDataSaver();
        if (empty($saverName)) {
            $saverName = 'default';
        }
        
        if ($saver = Mage::getConfig()->getNode('global/catalog/category/attribute/savers/'.$saverName)) {
            $model = Mage::getModel($saver->getClassName())->setAttribute($this);
            // TODO: check instanceof
            return $model;
        }
        
        throw new Exception('Attribute saver "'.$saverName.'" not found');
    }
    
    public function getFormFieldName()
    {
        return 'attribute['.$this->getId().']';
    }
}
