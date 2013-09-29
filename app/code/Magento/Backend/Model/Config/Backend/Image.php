<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config image field backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend;

class Image extends \Magento\Backend\Model\Config\Backend\File
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('jpg', 'jpeg', 'gif', 'png');
    }
}
