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
     * @var Varien_Data_Collection_Factory
     */
    protected $_collectionFactory;

    /**
     * @var Varien_Object_Factory
     */
    protected $_objectFactory;

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
        Varien_Data_Collection_Factory $collectionFactory,
        Varien_Object_Factory $objectFactory,
        Mage_Backend_Model_Config_Structure $configStructure,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );
        $this->_collectionFactory = $collectionFactory;
        $this->_objectFactory = $objectFactory;
        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode = $this->getRequest()->getParam('store');

        $this->_tabs = $configStructure->getTabs($websiteCode, $storeCode);

        $this->setId('system_config_tabs');
        $this->setTitle($this->helper('Mage_Backend_Helper_Data')->__('Configuration'));
    }

    /**
     * Init block before rendering
     *
     * @return Mage_Core_Block_Abstract|void
     */
    protected function _beforeToHtml()
    {
        $this->_currentSectionId = $this->getRequest()->getParam('section');

        if (!$this->_currentSectionId) {
            $this->_tabs->rewind();
            /** @var $tab Mage_Backend_Model_Config_Structure_Element_Tab */
            $tab = $this->_tabs->current();
            $tab->getChildren()->rewind();
            $sectionId = $tab->getChildren()->current()->getId();
            $this->_currentSectionId = $sectionId;
            $this->getRequest()->setParam('section', $sectionId);
        }
        $this->helper('Mage_Backend_Helper_Data')->addPageHelpUrl($this->_currentSectionId . '/');
        return $this;
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

    /**
     * Get store select options
     *
     * @return array
     */
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');

        /* @var $storeModel Mage_Core_Model_System_Store */
        $storeModel = Mage::getSingleton('Mage_Core_Model_System_Store');

        $options = array();
        $options['default'] = array(
            'label'    => $this->helper('Mage_Backend_Helper_Data')->__('Default Config'),
            'url'      => $this->getUrl('*/*/*', array('section' => $section)),
            'selected' => !$curWebsite && !$curStore,
            'style'    => 'background:#ccc; font-weight:bold;',
        );

        foreach ($storeModel->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $websiteShow = true;
                        $options['website_' . $website->getCode()] = array(
                            'label'    => $website->getName(),
                            'url'      => $this->getUrl('*/*/*',
                                array('section' => $section, 'website' => $website->getCode())
                            ),
                            'selected' => !$curStore && $curWebsite == $website->getCode(),
                            'style'    => 'padding-left:16px; background:#DDD; font-weight:bold;',
                        );
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $options['group_' . $group->getId() . '_open'] = array(
                            'is_group'  => true,
                            'is_close'  => false,
                            'label'     => $group->getName(),
                            'style'     => 'padding-left:32px;'
                        );
                    }
                    $options['store_' . $store->getCode()] = array(
                        'label'    => $store->getName(),
                        'url'      => $this->getUrl('*/*/*',
                            array('section'=>$section, 'website' => $website->getCode(), 'store' => $store->getCode())
                        ),
                        'selected' => $curStore == $store->getCode(),
                        'style'    => '',
                    );
                }
                if ($groupShow) {
                    $options['group_' . $group->getId() . '_close'] = array(
                        'is_group'  => true,
                        'is_close'  => true,
                    );
                }
            }
        }

        return $options;
    }

    /**
     * Get store button html code
     *
     * @return string
     */
    public function getStoreButtonsHtml()
    {
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');

        $html = '';

        if (!$curWebsite && !$curStore) {
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->helper('Mage_Backend_Helper_Data')->__('New Website'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_website/new') . "'",
                'class'     => 'add',
            ))->toHtml();
        } elseif (!$curStore) {
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->helper('Mage_Backend_Helper_Data')->__('Edit Website'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_website/edit', array('website'=>$curWebsite)) . "'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->helper('Mage_Backend_Helper_Data')->__('New Store View'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_store/new', array('website'=>$curWebsite)) . "'",
                'class'     => 'add',
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->helper('Mage_Backend_Helper_Data')->__('Delete Website'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_website/delete', array('website'=>$curWebsite)) . "'",
                'class'     => 'delete',
            ))->toHtml();
        } else {
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->helper('Mage_Backend_Helper_Data')->__('Edit Store View'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_store/edit', array('store'=>$curStore)) .
                    "'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->helper('Mage_Backend_Helper_Data')->__('Delete Store View'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_store/delete', array('store'=>$curStore)) . "'",
                'class'     => 'delete',
            ))->toHtml();
        }

        return $html;
    }
}

