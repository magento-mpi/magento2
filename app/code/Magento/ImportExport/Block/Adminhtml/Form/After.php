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
class Magento_ImportExport_Block_Adminhtml_Form_After extends Magento_Backend_Block_Template
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get current operation
     *
     * @return Magento_Core_Model_Abstract
     */
    public function getOperation()
    {
        return $this->_registry->registry('current_operation');
    }
}
