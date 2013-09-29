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
 * Config backend model for "Use secret key in Urls" option
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Admin;

class Usesecretkey extends \Magento\Core\Model\Config\Value
{
    protected function _afterSave()
    {
        \Mage::getSingleton('Magento\Backend\Model\Url')->renewSecretUrls();
        return $this;
    }
}
