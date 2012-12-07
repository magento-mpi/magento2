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
 * Catalog product media api V2
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Media_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Media_Api
{
    /**
     * Prepare data to create or update image
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareImageData($data)
    {
        if( !is_object($data) ) {
            return parent::_prepareImageData($data);
        }
        $_imageData = get_object_vars($data);
        if( isset($data->file) && is_object($data->file) ) {
            $_imageData['file'] = get_object_vars($data->file);
        }
        return parent::_prepareImageData($_imageData);
    }
}
