<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Adminhtml_Rma_Edit_Js extends Magento_Adminhtml_Block_Template
{

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        if ($this->_coreRegistry->registry('current_rma')) {
            $this->setRmaId($this->_coreRegistry->registry('current_rma')->getId());
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
            'id' => $this->_coreRegistry->registry('current_rma')->getId()
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
            'id' => $this->_coreRegistry->registry('current_rma')->getId()
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
            'id' => $this->_coreRegistry->registry('current_rma')->getId()
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
            'id' => $this->_coreRegistry->registry('current_rma')->getId()
        ));
    }
}
