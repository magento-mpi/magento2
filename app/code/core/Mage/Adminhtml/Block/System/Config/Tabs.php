<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('system_config_tabs');
        $this->setDestElementId('system_config_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Configuration'));
        $this->setTemplate('system/config/tabs.phtml');
    }

    protected function _sortSections($a, $b)
    {
        //echo $a->label.'['.$a->sort_order.'] vs '.$b->label . '['.$b->sort_order.'] = ' . (string)($a->sort_order < $b->sort_order ? -1 : ($a->sort_order > $b->sort_order ? 1 : 0))  . " \n<br>";
        return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);

    }

    public function initTabs()
    {
        $current = $this->getRequest()->getParam('section');


        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode = $this->getRequest()->getParam('store');


        $url = Mage::getModel('adminhtml/url');

        $configFields = Mage::getSingleton('adminhtml/config');
        $sections = $configFields->getSections($current);

        $sections = (array)$sections;

        usort($sections, array($this, '_sortSections'));

        foreach ($sections as $section) {

            $hasChildren = $configFields->hasChildren($section, $websiteCode, $storeCode);

            //$code = $section->getPath();
            $code = $section->getName();
            $sectionAllowed = $this->checkSectionPermissions($code);
            if ((empty($current) && $sectionAllowed)) {

                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }

            if (($section->getName()==$current && !$hasChildren)) {
                //redirec
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
                $defaultTab = $current;
                $this->addTab($code, array(
                    'label'     => $label,
                    'url'       => $url->getUrl('*/*/*', array('_current'=>true, 'section'=>$code)),
                ));
            }

            if ($code == $current) {
                $this->setActiveTab($code);
            }
        }

        Mage::helper('adminhtml')->addPageHelpUrl($current.'/');

        return $this;
    }

    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');

        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */

        $url = Mage::getModel('adminhtml/url');

        $options = array();
        $options['default'] = array(
            'label'    => Mage::helper('adminhtml')->__('Default config'),
            'url'      => $url->getUrl('*/*/*', array('section'=>$section)),
            'selected' => !$curWebsite && !$curStore,
            'style'    => 'background:#CCC; font-weight:bold;',
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

    public function getStoreButtonsHtml()
    {
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');

        $html = '';

        if (!$curWebsite && !$curStore) {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('adminhtml')->__('New Website'),
                'onclick'   => "location.href='".$this->getUrl('*/system_website/new')."'",
                'class'     => 'add',
            ))->toHtml();
        } elseif (!$curStore) {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Edit Website'),
                'onclick'   => "location.href='".$this->getUrl('*/system_website/edit', array('website'=>$curWebsite))."'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('adminhtml')->__('New Store View'),
                'onclick'   => "location.href='".$this->getUrl('*/system_store/new', array('website'=>$curWebsite))."'",
                'class'     => 'add',
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Delete Website'),
                'onclick'   => "location.href='".$this->getUrl('*/system_website/delete', array('website'=>$curWebsite))."'",
                'class'     => 'delete',
            ))->toHtml();
        } else {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Edit Store View'),
                'onclick'   => "location.href='".$this->getUrl('*/system_store/edit', array('store'=>$curStore))."'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Delete Store View'),
                'onclick'   => "location.href='".$this->getUrl('*/system_store/delete', array('store'=>$curStore))."'",
                'class'     => 'delete',
            ))->toHtml();
        }

        return $html;
    }

    public function checkSectionPermissions($code=null)
    {
        static $permissions;

        if (!$code or trim($code) == "") {
            return false;
        }

        if (!$permissions) {
            $permissions = Mage::getSingleton('admin/session');
        }

        $showTab = false;
        if ( $permissions->isAllowed('system/config/'.$code) ) {
	        $showTab = true;
	    }
        return $showTab;
    }


}
