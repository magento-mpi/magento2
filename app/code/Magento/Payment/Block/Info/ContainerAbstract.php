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
abstract class Magento_Payment_Block_Info_ContainerAbstract extends Magento_Core_Block_Template
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
     * @return Magento_Payment_Block_Info_ContainerAbstract
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
     * @return Magento_Payment_Model_Info|false
     */
    abstract public function getPaymentInfo();

    /**
     * Declare info block template
     *
     * @param   string $method
     * @param   string $template
     * @return  Magento_Payment_Block_Info_ContainerAbstract
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
