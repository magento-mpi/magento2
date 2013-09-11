<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order archive model
 *
 */
namespace Magento\SalesArchive\Model\Order\Status;

class Source extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * Retrieve order statuses as options for select
     *
     * @see \Magento\Sales\Model\Config\Source\Order\Status:toOptionArray()
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options); // Remove '--please select--' option
        return $options;
    }
}
