<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme form tab abstract block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit;

abstract class TabAbstract
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\ObjectManager $objectManager
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\ObjectManager $objectManager,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_objectManager = $objectManager;
    }

    /**
     * Get theme that is edited currently
     *
     * @return \Magento\Core\Model\Theme
     */
    protected function _getCurrentTheme()
    {
        return $this->_coreRegistry->registry('current_theme');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->_getCurrentTheme()->isVirtual() && $this->_getCurrentTheme()->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
