<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize Integration edit page. Set management buttons
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_integration';
        $this->_blockGroup = 'Magento_Integration';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Integration'));
        $this->_updateButton('delete', 'label', __('Delete Integration'));
    }

    /**
     * Get header text for banenr edit page
     *
     */
    /*
    public function getHeaderText()
    {
        if ($this->_registry->registry('current_integration')->getId()) {
            return $this->escapeHtml($this->_registry->registry('current_integration')->getName());
        } else {
            return __('New Integration');
        }
    }
    */

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
