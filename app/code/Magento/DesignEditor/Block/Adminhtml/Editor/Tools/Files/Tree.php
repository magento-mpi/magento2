<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block class for rendering design editor tree of files
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files;

class Tree
    extends \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Tree
{
    /**
     * @var \Magento\Theme\Helper\Storage
     */
    protected $_storageHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Theme\Helper\Storage $storageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Theme\Helper\Storage $storageHelper,
        array $data = array()
    ) {
        $this->_storageHelper = $storageHelper;
        parent::__construct($context, $storageHelper, $data);
    }

    /**
     * Override root node name of tree specific to design editor.
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return __('CSS Editor ') . __($this->_storageHelper->getStorageTypeName());
    }
}
