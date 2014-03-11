<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy;

/**
 * Cms Page Tree Edit Form Container Block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\VersionsCms\Helper\Data $cmsData,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Form Container
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'node_id';
        $this->_blockGroup = 'Magento_VersionsCms';
        $this->_controller = 'adminhtml_cms_hierarchy';

        parent::_construct();

        $this->_updateButton('save', 'onclick', 'hierarchyNodes.save()');
        $this->_removeButton('back');
        $this->_addButton('delete', array(
            'label'     => __('Delete Current Hierarchy'),
            'class'     => 'delete',
            'onclick'   => 'deleteCurrentHierarchy()',
        ), -1, 1);

        if (!$this->_storeManager->hasSingleStore()) {
            $this->_addButton('delete_multiple', array(
                'label'     => $this->_cmsData->getDeleteMultipleHierarchiesText(),
                'class'     => 'delete',
                'onclick'   => "openHierarchyDialog('delete')",
            ), -1, 7);
            $this->_addButton('copy', array(
                'label'     => __('Copy'),
                'class'     => 'add',
                'onclick'   => "openHierarchyDialog('copy')",
            ), -1, 14);
        }
    }

    /**
     * Retrieve text for header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Pages Hierarchy');
    }
}
