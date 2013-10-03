<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms Page Tree Edit Form Container Block
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize Form Container
     *
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
