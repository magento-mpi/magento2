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
 * Category attribute saver model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Category_Attribute_Saver 
{
    /**
     * Attribute object
     *
     * @var Mage_Catalog_Model_Product_Attribute
     */
    protected $_attribute;
    
    public function __construct() 
    {
        
    }
    
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    public function getResource()
    {
        return Mage::getResourceSingleton('catalog/category_attribute_saver');
    }
    
    public function save($categoryId, $value)
    {
        $this->getResource()->save($this->_attribute, $categoryId, $value);
        return $this;
    }
}
