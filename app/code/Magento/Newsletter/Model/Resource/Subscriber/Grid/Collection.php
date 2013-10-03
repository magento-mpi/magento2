<?php
/**
 * Newsletter subscriber grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Resource\Subscriber\Grid;

class Collection
    extends \Magento\Newsletter\Model\Resource\Subscriber\Collection
{
    /**
     * Sets flag for customer info loading on load
     *
     * @return \Magento\Newsletter\Model\Resource\Subscriber\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->showCustomerInfo(true)
            ->addSubscriberTypeField()
            ->showStoreInfo();
        return $this;
    }
}
