<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Model\Order\Status;

/**
 * Order archive model
 *
 */
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
        array_shift($options);
        // Remove '--please select--' option
        return $options;
    }
}
