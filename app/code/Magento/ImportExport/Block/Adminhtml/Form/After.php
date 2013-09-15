<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block form after
 */
namespace Magento\ImportExport\Block\Adminhtml\Form;

class After extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get current operation
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function getOperation()
    {
        return $this->_registry->registry('current_operation');
    }
}
