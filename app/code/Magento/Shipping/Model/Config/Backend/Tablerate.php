<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for shipping table rates CSV importing
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Shipping\Model\Config\Backend;

class Tablerate extends \Magento\Core\Model\Config\Value
{
    public function _afterSave()
    {
        \Mage::getResourceModel('\Magento\Shipping\Model\Resource\Carrier\Tablerate')->uploadAndImport($this);
    }
}
