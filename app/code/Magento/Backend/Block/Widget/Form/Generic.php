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
 */
namespace Magento\Backend\Block\Widget\Form;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Generic extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($context, $data);
    }
}
