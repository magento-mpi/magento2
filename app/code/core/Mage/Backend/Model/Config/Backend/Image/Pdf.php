<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config image field backend model for Zend PDF generator
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Image_Pdf extends Mage_Backend_Model_Config_Backend_Image
{
    protected function _getAllowedExtensions()
    {
        return array('tif', 'tiff', 'png', 'jpg', 'jpe', 'jpeg');
    }
}
