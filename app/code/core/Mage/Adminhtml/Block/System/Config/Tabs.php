<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System configuration tabs block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Tabs extends Mage_Adminhtml_Block_Widget
{

    /**
     * Tabs
     *
     * @var array
     */
    protected $_tabs;

    protected $_template = 'system/config/tabs.phtml';

    protected function _construct()
    {
        $this->setId('system_config_tabs');
        $this->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Configuration'));
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $a
     * @param unknown_type $b
     * @return int
     */
    protected function _sort($a, $b)
    {
        return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);
    }

    /**
     * Enter description here...
     *
     */
    public function initTabs()
    {
        $current = $this->getRequest()->getParam('section');
        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode = $this->getRequest()->getParam('store');

        $url = Mage::getModel('Mage_Adminhtml_Model_Url');

        $configFields = Mage::getSingleton('Mage_Adminhtml_Model_Config');
        $sections = $configFields->getSections($current);
        $tabs     = (array)$configFields->getTabs()->children();


        $sections = (array)$sections;

        usort($sections, array($this, '_sort'));
        usort($tabs, array($this, '_sort'));

        foreach ($tabs as $tab) {
            $helperName = $configFields->getAttributeModule($tab);
            $label = Mage::helper($helperName)->__((string)$tab->label);

            $this->addTab($tab->getName(), array(
                'label' => $label,
                'class' => (string) $tab->class
            ));
        }


        foreach ($sections as $section) {
            Mage::dispatchEvent('adminhtml_block_system_config_init_tab_sections_before', array('section' => $section));
            $hasChildren = $configFields->hasChildren($section, $websiteCode, $storeCode);

            $code = $section->getName();
            $sectionAllowed = $this->checkSectionPermissions($section->resource);
            if ((empty($current) && $sectionAllowed)) {

                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }

            $helperName = $configFields->getAttributeModule($section);

            $label = Mage::helper($helperName)->__((string)$section->label);

            if ($code == $current) {
                if (!$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store')) {
                    $this->_addBreadcrumb($label);
                } else {
                    $this->_addBreadcrumb($label, '', $url->getUrl('*/*/*', array('section'=>$code)));
                }
            }
            if ( $sectionAllowed && $hasChildren) {
                $this->addSection($code, (string)$section->tab, array(
                    'class'     => (string)$section->class,
                    'label'     => $label,
                    'url'       => $url->getUrl('*/*/*', array('_current'=>true, 'section'=>$code)),
                ));
            }

            if ($code == $current) {
                $this->setActiveTab($section->tab);
                $this->setActiveSection($code);
            }
        }

        /*
         * Set last sections
         */
        foreach ($this->getTabs() as $tab) {
            $sections = $tab->getSections();
            if ($sections) {
                $sections->getLastItem()->setIsLast(true);
            }
        }

        Mage::helper('Mage_Adminhtml_Helper_Data')->addPageHelpUrl($current.'/');

        return $this;
    }

    public function addTab($code, $config)
    {
        $tab = new Varien_Object($config);
        $tab->setId($code);
        $this->_tabs[$code] = $tab;
        return $this;
    }

    /**
     * Retrive tab
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

    public function addSection($code, $tabCode, $config)
    {
        if($tab = $this->getTab($tabCode)) {
            if(!$tab->getSections()) {
                $tab->setSections(new Varien_Data_Collection());
            }
            $section = new Varien_Object($config);
            $section->setId($code);
            $tab->getSections()->addItem($section);
        }
        return $this;
    }

    public function getTabs()
    {
        return $this->_tabs;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');

        $storeModel = Mage::getSingleton('Mage_Core_Model_System_Store');
        /* @var $storeModel Mage_Core_Model_System_Store */

        $url = Mage::getModel('Mage_Adminhtml_Model_Url');

        $options = array();
        $options['default'] = array(
            'label'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Default Config'),
            'url'      => $url->getUrl('*/*/*', array('section'=>$section)),
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
                            'url'      => $url->getUrl('*/*/*', array('section'=>$section, 'website'=>$website->getCode())),
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
                        'url'      => $url->getUrl('*/*/*', array('section'=>$section, 'website'=>$website->getCode(), 'store'=>$store->getCode())),
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
     * Enter description here...
     *
     * @return string
     */
    public function getStoreButtonsHtml()
    {
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');

        $html = '';

        if (!$curWebsite && !$curStore) {
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Website'),
                'onclick'   => "location.href='".$this->getUrl('*/system_website/new')."'",
                'class'     => 'add',
            ))->toHtml();
        } elseif (!$curStore) {
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Edit Website'),
                'onclick'   => "location.href='".$this->getUrl('*/system_website/edit', array('website'=>$curWebsite))."'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('New Store View'),
                'onclick'   => "location.href='".$this->getUrl('*/system_store/new', array('website'=>$curWebsite))."'",
                'class'     => 'add',
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete Website'),
                'onclick'   => "location.href='".$this->getUrl('*/system_website/delete', array('website'=>$curWebsite))."'",
                'class'     => 'delete',
            ))->toHtml();
        } else {
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Edit Store View'),
                'onclick'   => "location.href='".$this->getUrl('*/system_store/edit', array('store'=>$curStore))."'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete Store View'),
                'onclick'   => "location.href='".$this->getUrl('*/system_store/delete', array('store'=>$curStore))."'",
                'class'     => 'delete',
            ))->toHtml();
        }

        return $html;
    }

    /**
     * Enter description here...
     *
     * @param string $aclResourceId
     * @return boolean
     */
    public function checkSectionPermissions($aclResourceId=null)
    {
        static $permissions;

        if (!$aclResourceId or trim($aclResourceId) == "") {
            return false;
        }

        if (!$permissions) {
            $permissions = Mage::getSingleton('Mage_Core_Model_Authorization');
        }

        $showTab = false;
        if ( $permissions->isAllowed($aclResourceId) ) {
            $showTab = true;
        }
        return $showTab;
    }

}
