<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile attribute edit renderer
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Price;

class Recurring
    extends \Magento\Adminhtml\Block\Catalog\Form\Renderer\Fieldset\Element
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Element output getter
     *
     * @return string
     */
    public function getElementHtml()
    {
        $result = new \StdClass;
        $result->output = '';
        $this->_eventManager->dispatch('catalog_product_edit_form_render_recurring', array(
            'result' => $result,
            'product_element' => $this->_element,
            'product'   => $this->_coreRegistry->registry('current_product'),
        ));
        return $result->output;
    }
}
