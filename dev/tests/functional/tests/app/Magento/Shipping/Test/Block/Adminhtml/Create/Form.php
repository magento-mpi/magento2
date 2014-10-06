<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml\Create;

use Magento\Shipping\Test\Block\Adminhtml\Order\Tracking;
use Magento\Sales\Test\Block\Adminhtml\Order\AbstractForm;

/**
 * Class Form
 * Shipment form
 */
class Form extends AbstractForm
{
    /**
     * Tracking block css selector
     *
     * @var string
     */
    protected $tracking = '#tracking_numbers_table';

    /**
     * Fil data
     *
     * @param array $data
     * @return void
     */
    public function fillData(array $data)
    {
        if (isset($data['tracking'])) {
            $tracking = $this->prepareData($data['tracking']);
            if (!empty($tracking)) {
                $this->getTrackingBlock()->fill($tracking);
            }
        }
        $data = $this->dataMapping($this->prepareData($data));
        $this->_fill($data);
    }

    /**
     * Get tracking block
     *
     * @return Tracking
     */
    protected function getTrackingBlock()
    {
        return $this->blockFactory->create(
            'Magento\Shipping\Test\Block\Adminhtml\Order\Tracking',
            ['element' => $this->_rootElement->find($this->tracking)]
        );
    }
}
