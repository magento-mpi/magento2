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
 * Category attribute saver resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Category_Attribute_Saver
{
    protected $_write;
    
    public function __construct() 
    {
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
    }
    
    public function save(Mage_Catalog_Model_Category_Attribute $attribute, $categoryId, $value)
    {
        $table = $attribute->getTableName();
        $storeId = Mage::getSingleton('core/store')->getId();
        $attributeId = $attribute->getId();
        
        $condition = $this->_write->quoteInto('category_id=?',$categoryId)
                     . ' AND ' . $this->_write->quoteInto('attribute_id=?',$attributeId)
                     . ' AND ' . $this->_write->quoteInto('store_id=?',$storeId);
        
        try {
            $this->_write->delete($table, $condition);
            
            $data = array(
                'category_id'   => $categoryId,
                'attribute_id'  => $attributeId,
                'store_id'    => $storeId
            );
            
            if (is_array($value)) {
                foreach ($value as $val) {
                    $data['attribute_value'] = $val;
                    $this->_write->insert($table, $data);
                }
            }
            else {
                $data['attribute_value'] = $value;
                $this->_write->insert($table, $data);
            }
        }
        catch (Exception $e){
            throw new Exception('Attribute "'.$attribute->getCode().'" value save error: '.$e->getMessage());
        }
        
        return $this;
    }
}