<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product type api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve product type list
     *
     * @return array
     */
    public function items()
    {
        $result = array();

        foreach (Mage_Catalog_Model_Product_Type::getOptionArray() as $type=>$label) {
            $result[] = array(
                'type'  => $type,
                'label' => $label
            );
        }

        return $result;
    }
} // Class Mage_Catalog_Model_Product_Type_Api End
