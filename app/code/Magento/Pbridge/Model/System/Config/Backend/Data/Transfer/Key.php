<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config data transfer key field backend model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model\System\Config\Backend\Data\Transfer;

class Key extends \Magento\Core\Model\Config\Value
{
    /**
     * Checks data transfer key length
     *
     * @return \Magento\Pbridge\Model\System\Config\Backend\Data\Transfer\Key
     */
    protected function _beforeSave()
    {
        /**
         * Maximum allowed length is hardcoded because currently we use only CIPHER_RIJNDAEL_256
         * @see \Magento\Pci\Model\Encryption::_getCrypt
         * @throws \Magento\Core\Exception
         */
        if (strlen($this->getValue()) > 32) { // strlen() intentionally, to count bytes rather than characters
            throw new \Magento\Core\Exception(
                __('Maximum data transfer key length is 32. Please correct your settings.'));
        }

        return $this;
    }
}
