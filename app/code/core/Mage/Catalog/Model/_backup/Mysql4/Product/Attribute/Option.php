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
 * Product attributes options model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Option
{
    protected $_read;
    protected $_write;
    protected $_optionTable;
    
    public function __construct() 
    {
        $this->_read            = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write           = Mage::getSingleton('core/resource')->getConnection('catalog_write');
        $this->_optionTable     = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_option');
    }

    public function load($optionId)
    {
        return $this->_read->fetchRow("SELECT * FROM $this->_optionTable WHERE option_id=:id", array('id'=>$optionId));
    }    
    
    public function save()
    {
        return $this;
    }
}