<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block form after
 */
class Enterprise_ImportExport_Block_Adminhtml_Form_After extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Mage_Core_Model_Registry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }
}
