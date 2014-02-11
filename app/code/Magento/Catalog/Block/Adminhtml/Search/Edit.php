<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Adminhtml\Search;

/**
 * Admin tag edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $coreRegistry;

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
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magento_Catalog';
        $this->_controller = 'adminhtml_search';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Search'));
        $this->_updateButton('delete', 'label', __('Delete Search'));
    }

    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('current_catalog_search')->getId()) {
            $queryText = $this->escapeHtml($this->coreRegistry->registry('current_catalog_search')->getQueryText());
            return __("Edit Search '%1'", $queryText);
        } else {
            return __('New Search');
        }
    }
}
