<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Payment\Model\Method\Context;

use \Magento\Payment\Model\Method\AbstractMethod;
use \Magento\Sales\Model\Order\Payment;
use \Magento\Payment\Model\Info;

/**
 * Class AdapterFactory
 * @package Magento\Payment\Model\Method\Context
 */
class AdapterFactory
{
    /**
     * Quote adapter
     */
    const QUOTE = '\Magento\Payment\Model\Method\Context\QuoteAdapter';

    /**
     * Order adapter
     */
    const ORDER = '\Magento\Payment\Model\Method\Context\OrderAdapter';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param AbstractMethod $paymentMethod
     * @return AdapterInterface
     * @throws \Magento\Framework\Model\Exception
     */
    public function create(AbstractMethod $paymentMethod)
    {
        $instance = '';
        $paymentInfo = $paymentMethod->getInfoInstance();
        if ($paymentInfo instanceof Payment) {
            $instance = self::ORDER;
        } elseif ($paymentInfo instanceof Info) {
            $instance = self::QUOTE;
        }

        if (empty($instance)) {
            throw new \Magento\Framework\Model\Exception(
                __('Payment info is not provided')
            );
        }

        return $this->_objectManager->create($instance, ['paymentInfo' => $paymentInfo]);
    }
}
