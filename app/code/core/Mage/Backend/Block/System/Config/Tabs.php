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
     * @var array
     */
    protected $_tabs;

    /**
     * @var Mage_Backend_Model_Config_StructureInterface
     */
    protected $_systemConfig;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_systemConfig = isset($data['systemConfig']) ?
            $data['systemConfig'] :
            Mage::getSingleton('Mage_Backend_Model_Config_Structure_Reader')->getConfiguration();

        parent::__construct($data);
        
        $this->setId('system_config_tabs');
        $this->setTitle($this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('Configuration'));
        $this->setTemplate('Mage_Backend::system/config/tabs.phtml');
    }

    /**
     * Sort sections/tabs
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    protected function _sort($a, $b)
    {
        $aSortOrder = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 0;
        $bSortOrder = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 0;
        return $aSortOrder < $bSortOrder ? -1 : ($aSortOrder > $bSortOrder ? 1 : 0);
    }

    /**
     * Initialize tabs
     *
     * @return Mage_Backend_Block_System_Config_Tabs
     */
    public function initTabs()
    {
        $sections = $this->_systemConfig->getSections();
        $tabs     = $this->_systemConfig->getTabs();

        usort($sections, array($this, '_sort'));
        usort($tabs, array($this, '_sort'));

        $this->_initializeTabs($tabs);
        $current = $this->_initializeSections($sections);

        /** Set last sections  */

        /** @var Varien_Object $tab */
        foreach ($this->getTabs() as $tab) {
            $sections = $tab->getSections();
            if ($sections) {
                $sections->getLastItem()->setIsLast(true);
            }
        }
        $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->addPageHelpUrl($current . '/');

        return $this;
    }

    /**
     * Initialize sections
     *
     * @param $sections
     * @return string
     */
    protected function _initializeSections($sections)
    {
        $current = $this->getRequest()->getParam('section');
        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode = $this->getRequest()->getParam('store');

        /** @var $section array */
        foreach ($sections as $section) {
            $this->_getEventManager()->dispatch('adminhtml_block_system_config_init_tab_sections_before',
                array('section' => $section)
            );

            $code = $section['id'];
            $sectionAllowed = true;
            if (isset($section['resource'])) {
                $sectionAllowed = $this->checkSectionPermissions($section['resource']);
            }
            if ((empty($current) && $sectionAllowed)) {
                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }

            $helperName = $this->_systemConfig->getAttributeModule($section);
            $label = $this->_getHelperFactory()->get($helperName)->__($section['label']);

            if ($code == $current) {
                if (!$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store')) {
                    $this->_addBreadcrumb($label);
                } else {
                    $this->_addBreadcrumb($label, '', $this->getUrl('*/*/*', array('section' => $code)));
                }

                $this->setActiveTab($section['tab']);
                $this->setActiveSection($code);
            }

            $hasChildren = $this->_systemConfig->hasChildren($section, $websiteCode, $storeCode);
            if ($sectionAllowed && $hasChildren) {
                $this->addSection($code, $section['tab'], array(
                    'class' => isset($section['class']) ? $section['class'] : '',
                    'label' => $label,
                    'url' => $this->getUrl('*/*/*', array('_current' => true, 'section' => $code)),
                ));
            }
        }

        return $current;
    }

    /**
     * Initialize tabs
     *
     * @param array $tabs
     * @return void
     */
    protected function _initializeTabs(array $tabs)
    {
        foreach ($tabs as $tab) {
            $helperName = $this->_systemConfig->getAttributeModule($tab);
            $label = $this->_getHelperFactory()->get($helperName)->__($tab['label']);

            $this->addTab($tab['id'], array(
                'label' => $label,
                'class' => isset($tab['class']) ? $tab['class'] : ''
            ));
        }
    }

    /**
     * Add tab to tabs list
     *
     * @param string $code
     * @param array $config
     * @return Mage_Backend_Block_System_Config_Tabs
     */
    public function addTab($code, $config)
    {
        $tab = $this->_getObjectFactory()->getModelInstance('Varien_Object', $config);
        $tab->setId($code);
        $this->_tabs[$code] = $tab;
        return $this;
    }

    /**
     * Retrieve tab
     *
     * @param string $code
     * @return Varien_Object
     */
    public function getTab($code)
    {
        if(isset($this->_tabs[$code])) {
            return $this->_tabs[$code];
        }
        return null;
    }

    /**
     * Add section to tab
     *
     * @param string $code
     * @param string $tabCode
     * @param array $config
     * @return Mage_Backend_Block_System_Config_Tabs
     */
    public function addSection($code, $tabCode, $config)
    {
        if($tab = $this->getTab($tabCode)) {
            if(!$tab->getSections()) {
                $tab->setSections($this->_getObjectFactory()->getModelInstance('Varien_Data_Collection'));
            }
            $section = $this->_getObjectFactory()->getModelInstance('Varien_Object', $config);
            $section->setId($code);
            $tab->getSections()->addItem($section);
        }
        return $this;
    }

    /**
     * Get all tabs
     *
     * @return Varien_Object[]
     */
    public function getTabs()
    {
        return $this->_tabs;
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
            'label'    => $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('Default Config'),
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
                'label'     => $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('New Website'),
                'onclick'   => "location.href='" . $this->getUrl('*/system_website/new') . "'",
                'class'     => 'add',
            ))->toHtml();
        } elseif (!$curStore) {
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('Edit Website'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_website/edit', array('website'=>$curWebsite)) . "'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('New Store View'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_store/new', array('website'=>$curWebsite)) . "'",
                'class'     => 'add',
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('Delete Website'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_website/delete', array('website'=>$curWebsite)) . "'",
                'class'     => 'delete',
            ))->toHtml();
        } else {
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('Edit Store View'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_store/edit', array('store'=>$curStore)) .
                    "'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')->setData(array(
                'label'     => $this->_getHelperFactory()->get('Mage_Backend_Helper_Data')->__('Delete Store View'),
                'onclick'   => "location.href='" .
                    $this->getUrl('*/system_store/delete', array('store'=>$curStore)) . "'",
                'class'     => 'delete',
            ))->toHtml();
        }

        return $html;
    }

    /**
     * Check if specified section can be displayed
     *
     * @param string $aclResourceId
     * @return bool
     */
    public function checkSectionPermissions($aclResourceId)
    {
        if (!$aclResourceId || trim($aclResourceId) == "") {
            return false;
        }
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed($aclResourceId);
    }
}
