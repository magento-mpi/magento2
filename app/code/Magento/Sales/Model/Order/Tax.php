<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order;

/**
 *
 * @method \Magento\Sales\Model\Resource\Order\Tax _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Tax getResource()
 * @method int getOrderId()
 * @method \Magento\Sales\Model\Order\Tax setOrderId(int $value)
 * @method string getCode()
 * @method \Magento\Sales\Model\Order\Tax setCode(string $value)
 * @method string getTitle()
 * @method \Magento\Sales\Model\Order\Tax setTitle(string $value)
 * @method float getPercent()
 * @method \Magento\Sales\Model\Order\Tax setPercent(float $value)
 * @method float getAmount()
 * @method \Magento\Sales\Model\Order\Tax setAmount(float $value)
 * @method int getPriority()
 * @method \Magento\Sales\Model\Order\Tax setPriority(int $value)
 * @method int getPosition()
 * @method \Magento\Sales\Model\Order\Tax setPosition(int $value)
 * @method float getBaseAmount()
 * @method \Magento\Sales\Model\Order\Tax setBaseAmount(float $value)
 * @method int getProcess()
 * @method \Magento\Sales\Model\Order\Tax setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method \Magento\Sales\Model\Order\Tax setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method \Magento\Sales\Model\Order\Tax setHidden(int $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Tax');
    }
}
