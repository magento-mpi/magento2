<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders statuses option array
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Grid;

class StatusesArray implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Return option array
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Status\Collection')
            ->toOptionHash();
        return $statuses;
    }
}
