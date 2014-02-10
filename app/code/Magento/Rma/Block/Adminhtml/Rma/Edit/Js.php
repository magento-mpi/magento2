<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Edit;

class Js extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
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
        return $this->getUrl('adminhtml/*/loadAttributes', array(
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
        return $this->getUrl('adminhtml/*/loadSplitLine', array(
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
        return $this->getUrl('adminhtml/*/showShippingMethods', array(
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
        return $this->getUrl('adminhtml/*/psl', array(
            'id' => $this->_coreRegistry->registry('current_rma')->getId()
        ));
    }
}
