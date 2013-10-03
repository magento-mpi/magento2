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
 */
namespace Magento\Backend\Model\Config\Backend\Email;

class Sender extends \Magento\Core\Model\Config\Value
{
    /**
     * Check sender name validity
     *
     * @return \Magento\Backend\Model\Config\Backend\Email\Sender
     * @throws \Magento\Core\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            throw new \Magento\Core\Exception(
                __('The sender name "%1" is not valid. Please use only visible characters and spaces.', $value)
            );
        }

        if (strlen($value) > 255) {
            throw new \Magento\Core\Exception(
                __('Maximum sender name length is 255. Please correct your settings.')
            );
        }
        return $this;
    }
}
