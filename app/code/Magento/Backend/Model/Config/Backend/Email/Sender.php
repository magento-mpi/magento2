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
 * System config email sender field backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Email;

class Sender extends \Magento\Core\Model\Config\Value
{
    /**
     * Check sender name validity
     *
     * @return \Magento\Backend\Model\Config\Backend\Email\Sender
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            \Mage::throwException(
                __('The sender name "%1" is not valid. Please use only visible characters and spaces.', $value)
            );
        }

        if (strlen($value) > 255) {
            \Mage::throwException(
                __('Maximum sender name length is 255. Please correct your settings.')
            );
        }
        return $this;
    }
}
