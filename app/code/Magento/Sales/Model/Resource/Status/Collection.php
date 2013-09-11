<?php
/**
 * Oder statuses grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Status;

class Collection extends \Magento\Sales\Model\Resource\Order\Status\Collection
{
    /**
     * Join order states table
     *
     * @return \Magento\Sales\Model\Resource\Status\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinStates();
        return $this;
    }
}
