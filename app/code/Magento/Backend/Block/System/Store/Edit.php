<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Store;

/**
 * Adminhtml store edit
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
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

    /**
     * Init class
     *
     * @return void
     */
    protected function _construct()
    {
        switch ($this->_coreRegistry->registry('store_type')) {
            case 'website':
                $this->_objectId = 'website_id';
                $saveLabel   = __('Save Web Site');
                $deleteLabel = __('Delete Web Site');
                $deleteUrl   = $this->getUrl(
                    '*/*/deleteWebsite',
                    array('item_id' => $this->_coreRegistry->registry('store_data')->getId())
                );
                break;
            case 'group':
                $this->_objectId = 'group_id';
                $saveLabel   = __('Save Store');
                $deleteLabel = __('Delete Store');
                $deleteUrl   = $this->getUrl(
                    '*/*/deleteGroup',
                    array('item_id' => $this->_coreRegistry->registry('store_data')->getId())
                );
                break;
            case 'store':
                $this->_objectId = 'store_id';
                $saveLabel   = __('Save Store View');
                $deleteLabel = __('Delete Store View');
                $deleteUrl   = $this->getUrl(
                    '*/*/deleteStore',
                    array('item_id' => $this->_coreRegistry->registry('store_data')->getId())
                );
                break;
            default:
                $saveLabel = '';
                $deleteLabel = '';
                $deleteUrl = '';
        }
        $this->_blockGroup = 'Magento_Backend';
        $this->_controller = 'system_store';

        parent::_construct();

        $this->_updateButton('save', 'label', $saveLabel);
        $this->_updateButton('delete', 'label', $deleteLabel);
        $this->_updateButton('delete', 'onclick', 'setLocation(\''.$deleteUrl.'\');');

        if (!$this->_coreRegistry->registry('store_data')) {
            return;
        }

        if (!$this->_coreRegistry->registry('store_data')->isCanDelete()) {
            $this->_removeButton('delete');
        }
        if ($this->_coreRegistry->registry('store_data')->isReadOnly()) {
            $this->_removeButton('save')->_removeButton('reset');
        }
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        switch ($this->_coreRegistry->registry('store_type')) {
            case 'website':
                $editLabel = __('Edit Web Site');
                $addLabel  = __('New Web Site');
                break;
            case 'group':
                $editLabel = __('Edit Store');
                $addLabel  = __('New Store');
                break;
            case 'store':
                $editLabel = __('Edit Store View');
                $addLabel  = __('New Store View');
                break;
        }

        return $this->_coreRegistry->registry('store_action') == 'add' ? $addLabel : $editLabel;
    }

    /**
     * Build child form class form name based on value of store_type in registry
     *
     * @return string
     */
    protected function _buildFormClassName()
    {
        return parent::_buildFormClassName() . '\\' . ucwords($this->_coreRegistry->registry('store_type'));
    }
}
