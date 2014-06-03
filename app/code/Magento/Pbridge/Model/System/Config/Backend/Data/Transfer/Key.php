<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config data transfer key field backend model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model\System\Config\Backend\Data\Transfer;

class Key extends \Magento\Framework\App\Config\Value
{
    /**
     * Checks data transfer key length
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        /**
         * Maximum allowed length is hardcoded because currently we use only CIPHER_RIJNDAEL_256
         * @see \Magento\Pci\Model\Encryption::_getCrypt
         * @throws \Magento\Framework\Model\Exception
         */
        if (strlen($this->getValue()) > 32) {
            // strlen() intentionally, to count bytes rather than characters
            throw new \Magento\Framework\Model\Exception(
                __('Maximum data transfer key length is 32. Please correct your settings.')
            );
        }

        return $this;
    }
}
