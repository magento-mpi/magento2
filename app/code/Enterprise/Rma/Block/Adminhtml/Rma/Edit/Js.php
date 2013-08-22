<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Js extends Magento_Adminhtml_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        if (Mage::registry('current_rma')) {
            $this->setRmaId(Mage::registry('current_rma')->getId());
        }
    }

    /**
     * Get url for Details AJAX Action
     *
     * @return string
     */
    public function getLoadAttributesUrl()
    {
        return $this->getUrl('*/*/loadAttributes', array(
            'id' => Mage::registry('current_rma')->getId()
        ));
    }

    /**
     * Get url for Split Line AJAX Action
     *
     * @return string
     */
    public function getLoadSplitLineUrl()
    {
        return $this->getUrl('*/*/loadSplitLine', array(
            'id' => Mage::registry('current_rma')->getId()
        ));
    }

    /**
     * Get url for Shipping Methods Action
     *
     * @return string
     */
    public function getLoadShippingMethodsUrl()
    {
        return $this->getUrl('*/*/showShippingMethods', array(
            'id' => Mage::registry('current_rma')->getId()
        ));
    }

    /**
     * Get url for Psl Action
     *
     * @return string
     */
    public function getLoadPslUrl()
    {
        return $this->getUrl('*/*/psl', array(
            'id' => Mage::registry('current_rma')->getId()
        ));
    }
}
