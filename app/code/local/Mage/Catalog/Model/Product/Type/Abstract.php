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
 * Abstract model for product type implementation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Catalog_Model_Product_Type_Abstract
{
    protected $_product;
    protected $_typeId;
    protected $_attributes;

    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function setTypeId($typeId)
    {
        $this->_typeId = $typeId;
        return $this;
    }

    /**
     * Retrieve catalog product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Retrieve product type attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->_attributes) {
            $this->_attributes = $this->getProduct()->getResource()
                ->loadAllAttributes($this->getProduct())
                ->getAttributesByCode();
        }
        return $this->_attributes;
    }

    /**
     * Save type related data
     *
     * @return unknown
     */
    public function save()
    {
        return $this;
    }
}
