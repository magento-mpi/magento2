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
     * Add payment info block to layout
     *
     * @return Magento_Payment_Block_Info_ContainerAbstract
     */
    protected function _prepareLayout()
    {
        if ($info = $this->getPaymentInfo()) {
            $this->setChild(
                $this->_getInfoBlockName(),
                Mage::helper('Magento_Payment_Helper_Data')->getInfoBlock($info)
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
