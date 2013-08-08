<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment information container block
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Payment_Block_Info_ContainerAbstract extends Magento_Core_Block_Template
{
    /**
     * Add payment info block to layout
     *
     * @return Mage_Payment_Block_Info_ContainerAbstract
     */
    protected function _prepareLayout()
    {
        if ($info = $this->getPaymentInfo()) {
            $this->setChild(
                $this->_getInfoBlockName(),
                Mage::helper('Mage_Payment_Helper_Data')->getInfoBlock($info)
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
     * @return Mage_Payment_Model_Info|false
     */
    abstract public function getPaymentInfo();

    /**
     * Declare info block template
     *
     * @param   string $method
     * @param   string $template
     * @return  Mage_Payment_Block_Info_ContainerAbstract
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
