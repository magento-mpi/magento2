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
     * @var \Magento\Registry
     */
    protected $_registry;

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
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get current operation
     *
     * @return \Magento\Model\AbstractModel
     */
    public function getOperation()
    {
        return $this->_registry->registry('current_operation');
    }
}
