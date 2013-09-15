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
 * System configuration tabs block
 *
 * @method setTitle(string $title)
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
namespace Magento\Backend\Block\System\Config;

class Tabs extends \Magento\Backend\Block\Widget
{
    /**
     * Tabs
     *
     * @var \Magento\Backend\Model\Config\Structure\Element\Iterator
     */
    protected $_tabs;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'system/config/tabs.phtml';

    /**
     * Currently selected section id
     *
     * @var string
     */
    protected $_currentSectionId;

    /**
     * Current website code
     *
     * @var string
     */
    protected $_websiteCode;

    /**
     * Current store code
     *
     * @var string
     */
    protected $_storeCode;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param \Magento\Backend\Model\Config\Structure $configStructure
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Config\Structure $configStructure,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_tabs = $configStructure->getTabs();

        $this->setId('system_config_tabs');
        $this->setTitle(__('Configuration'));
        $this->_currentSectionId = $this->getRequest()->getParam('section');

        $this->helper('Magento\Backend\Helper\Data')->addPageHelpUrl($this->getRequest()->getParam('section') . '/');
    }

    /**
     * Get all tabs
     *
     * @return \Magento\Backend\Model\Config\Structure\Element\Iterator
     */
    public function getTabs()
    {
        return $this->_tabs;
    }

    /**
     * Retrieve section url by section id
     *
     * @param \Magento\Backend\Model\Config\Structure\Element\Section $section
     * @return string
     */
    public function getSectionUrl(\Magento\Backend\Model\Config\Structure\Element\Section $section)
    {
        return $this->getUrl('*/*/*', array('_current' => true, 'section' => $section->getId()));
    }

    /**
     * Check whether section should be displayed as active
     *
     * @param \Magento\Backend\Model\Config\Structure\Element\Section $section
     * @return bool
     */
    public function isSectionActive(\Magento\Backend\Model\Config\Structure\Element\Section $section)
    {
        return $section->getId() == $this->_currentSectionId;
    }
}

