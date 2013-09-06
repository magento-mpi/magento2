<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend form widget
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Magento_Backend_Block_Widget_Form_Generic extends Magento_Backend_Block_Widget_Form
{
    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }
}
