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
 * Catalog product media api V2
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Media\Api;

class V2 extends \Magento\Catalog\Model\Product\Attribute\Media\Api
{
    /**
     * Prepare data to create or update image
     *
     * @param \stdClass $data
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
