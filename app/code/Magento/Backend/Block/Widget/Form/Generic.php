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
namespace Magento\Backend\Block\Widget\Form;

class Generic extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Data\Form\Factory
     */
    protected $_formFactory;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }
}
