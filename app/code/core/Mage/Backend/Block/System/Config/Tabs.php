<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System configuration tabs block
 *
 * @method setTitle(string $title)
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Block_System_Config_Tabs extends Mage_Backend_Block_Widget
{
    /**
     * Tabs
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator
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
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Varien_Data_Collection_Factory $collectionFactory
     * @param Varien_Object_Factory $objectFactory
     * @param Mage_Backend_Model_Config_Structure $configStructure
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Varien_Data_Collection_Factory $collectionFactory,
        Varien_Object_Factory $objectFactory,
        Mage_Backend_Model_Config_Structure $configStructure,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $data
        );
        $this->_tabs = $configStructure->getTabs();

        $this->setId('system_config_tabs');
        $this->setTitle($this->helper('Mage_Backend_Helper_Data')->__('Configuration'));
        $this->_currentSectionId = $this->getRequest()->getParam('section');

        $this->helper('Mage_Backend_Helper_Data')->addPageHelpUrl($this->getRequest()->getParam('section') . '/');
    }

    /**
     * Get all tabs
     *
     * @return Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getTabs()
    {
        return $this->_tabs;
    }

    /**
     * Retrieve section url by section id
     *
     * @param Mage_Backend_Model_Config_Structure_Element_Section $section
     * @return string
     */
    public function getSectionUrl(Mage_Backend_Model_Config_Structure_Element_Section $section)
    {
        return $this->getUrl('*/*/*', array('_current' => true, 'section' => $section->getId()));
    }

    /**
     * Check whether section should be displayed as active
     *
     * @param Mage_Backend_Model_Config_Structure_Element_Section $section
     * @return bool
     */
    public function isSectionActive(Mage_Backend_Model_Config_Structure_Element_Section $section)
    {
        return $section->getId() == $this->_currentSectionId;
    }
}

