<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config image field backend model for Zend PDF generator
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Image;

class Pdf extends \Magento\Backend\Model\Config\Backend\Image
{
    /**
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return array('tif', 'tiff', 'png', 'jpg', 'jpe', 'jpeg');
    }
}
