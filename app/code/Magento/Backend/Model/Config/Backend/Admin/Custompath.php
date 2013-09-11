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
 * Config backend model for "Custom Admin Path" option
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Admin;

class Custompath extends \Magento\Core\Model\Config\Value
{
    /**
     * Check whether redirect should be set
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Custom
     */
    protected function _beforeSave()
    {
        if ($this->getOldValue() != $this->getValue()) {
            \Mage::helper('Magento\Backend\Helper\Data')->clearAreaFrontName();
            \Mage::register('custom_admin_path_redirect', true, true);
        }
        return $this;
    }
}
