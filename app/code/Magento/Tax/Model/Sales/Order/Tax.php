<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Sales\Order;

/**
 * @method \Magento\Tax\Model\Resource\Sales\Order\Tax _getResource()
 * @method \Magento\Tax\Model\Resource\Sales\Order\Tax getResource()
 * @method int getOrderId()
 * @method \Magento\Tax\Model\Sales\Order\Tax setOrderId(int $value)
 * @method string getCode()
 * @method \Magento\Tax\Model\Sales\Order\Tax setCode(string $value)
 * @method string getTitle()
 * @method \Magento\Tax\Model\Sales\Order\Tax setTitle(string $value)
 * @method float getPercent()
 * @method \Magento\Tax\Model\Sales\Order\Tax setPercent(float $value)
 * @method float getAmount()
 * @method \Magento\Tax\Model\Sales\Order\Tax setAmount(float $value)
 * @method int getPriority()
 * @method \Magento\Tax\Model\Sales\Order\Tax setPriority(int $value)
 * @method int getPosition()
 * @method \Magento\Tax\Model\Sales\Order\Tax setPosition(int $value)
 * @method float getBaseAmount()
 * @method \Magento\Tax\Model\Sales\Order\Tax setBaseAmount(float $value)
 * @method int getProcess()
 * @method \Magento\Tax\Model\Sales\Order\Tax setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method \Magento\Tax\Model\Sales\Order\Tax setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method \Magento\Tax\Model\Sales\Order\Tax setHidden(int $value)
 */
class Tax extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Tax\Model\Resource\Sales\Order\Tax');
    }
}
