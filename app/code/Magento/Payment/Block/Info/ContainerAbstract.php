<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment information container block
 *
 * @category   Magento
 * @package    Magento_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Payment\Block\Info;

abstract class ContainerAbstract extends \Magento\Core\Block\Template
{
    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData = null;

    /**
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Add payment info block to layout
     *
     * @return \Magento\Payment\Block\Info\ContainerAbstract
     */
    protected function _prepareLayout()
    {
        if ($info = $this->getPaymentInfo()) {
            $this->setChild(
                $this->_getInfoBlockName(),
                $this->_paymentData->getInfoBlock($info)
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve info block name
     *
     * @return unknown
     */
    protected function _getInfoBlockName()
    {
        if ($info = $this->getPaymentInfo()) {
            return 'payment.info.'.$info->getMethodInstance()->getCode();
        }
        return false;
    }

    /**
     * Retrieve payment info model
     *
     * @return \Magento\Payment\Model\Info|false
     */
    abstract public function getPaymentInfo();

    /**
     * Declare info block template
     *
     * @param   string $method
     * @param   string $template
     * @return  \Magento\Payment\Block\Info\ContainerAbstract
     */
    public function setInfoTemplate($method='', $template='')
    {
        if ($info = $this->getPaymentInfo()) {
            if ($info->getMethodInstance()->getCode() == $method) {
                $this->getChildBlock($this->_getInfoBlockName())->setTemplate($template);
            }
        }
        return $this;
    }
}
