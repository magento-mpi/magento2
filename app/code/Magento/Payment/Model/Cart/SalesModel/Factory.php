<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory for creating payment cart sales models
 */
namespace Magento\Payment\Model\Cart\SalesModel;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Wrap sales model with Magento\Payment\Model\Cart\SalesModel\SalesModelInterface
     *
     * @param \Magento\Sales\Model\Order|\Magento\Sales\Model\Quote $salesModel
     * @return \Magento\Payment\Model\Cart\SalesModel\SalesModelInterface
     * @throws \InvalidArgumentException
     */
    public function create($salesModel)
    {
        $arguments = array('salesModel' => $salesModel);
        if ($salesModel instanceof \Magento\Sales\Model\Quote) {
            return $this->_objectManager->create('Magento\Payment\Model\Cart\SalesModel\Quote', $arguments);
        } else if ($salesModel instanceof \Magento\Sales\Model\Order) {
            return $this->_objectManager->create('Magento\Payment\Model\Cart\SalesModel\Order', $arguments);
        }
        throw new \InvalidArgumentException('Sales model has bad type!');
    }
}
