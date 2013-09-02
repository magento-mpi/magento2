<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product type api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Type_Api extends Magento_Api_Model_Resource_Abstract
{
    /**
     * @var Magento_Catalog_Model_Product_Type
     */
    protected $_productType;

    /**
     * @param Magento_Catalog_Model_Product_Type $productType
     */
    public function __construct(Magento_Catalog_Model_Product_Type $productType)
    {
        $this->_productType = $productType;
    }

    /**
     * Retrieve product type list
     *
     * @return array
     */
    public function items()
    {
        $result = array();
        foreach ($this->_productType->getOptionArray() as $type => $label) {
            $result[] = array(
                'type'  => $type,
                'label' => $label
            );
        }
        return $result;
    }
} // Class Magento_Catalog_Model_Product_Type_Api End
