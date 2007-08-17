<?php
/**
 * admin customer left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('system_config_tabs');
        $this->setDestElementId('system_config_form');
        $this->setTitle(__('Configuration'));
        $this->setTemplate('system/config/tabs.phtml');
    }
    
    public function initTabs()
    {
        $current = $this->getRequest()->getParam('section');

        $sections = Mage::getResourceModel('core/config_field_collection')
            ->addFieldToFilter('level', 1)
            ->setOrder('sort_order', 'asc')
            ->loadData();
        
        foreach ($sections as $section) {
            $code = $section->getPath();
            if (empty($current)) {
                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }
            $label = __($section->getFrontendLabel());
            if ($code == $current) {
                if (!$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store')) {
                    $this->_addBreadcrumb($label);
                } else {
                    $this->_addBreadcrumb($label, '', Mage::getUrl('*/*/*', array('section'=>$code)));
                }
            }

            $this->addTab($code, array(
                'label'     => $label,
                'url'       => Mage::getUrl('*/*/*', array('_current'=>true, 'section'=>$code)),
            ));
            if ($code == $current) {
                $this->setActiveTab($code);
            }
        }
        return $this;
    }
    
    
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');
        
        $curWebsite = $this->getRequest()->getParam('website');
        $curStore = $this->getRequest()->getParam('store');
        
        $websitesConfig = Mage::getConfig()->getNode('websites');
        $storesConfig = Mage::getConfig()->getNode('stores');

        $options = array();
        $options['default'] = array(
            'label'    => __('Default config'),
            'url'      => Mage::getUrl('*/*/*', array('section'=>$section)),
            'selected' => !$curWebsite && !$curStore,
            'style'    => 'background:#CCC; font-weight:bold;',
        );
        
        foreach ($websitesConfig->children() as $wCode=>$wConfig) {
        	if ($wConfig->descend('system/website/id')==0) {
        		continue;
        	}
            $options['website_'.$wCode] = array(
                'label'    => (string)$wConfig->descend('system/website/name'),
                'url'      => Mage::getUrl('*/*/*', array('section'=>$section, 'website'=>$wCode)),
                'selected' => !$curStore && $curWebsite==$wCode,
                'style'    => 'padding-left:16px; background:#DDD; font-weight:bold;',
            );
            $websiteStores = $wConfig->descend('system/stores');
            if ($websiteStores) {
                foreach ($websiteStores->children() as $sCode=>$sId) {
                    $options['store_'.$sCode] = array(
                        'label'    => (string)$storesConfig->descend($sCode.'/system/store/name'),
                        'url'      => Mage::getUrl('*/*/*', array('section'=>$section, 'website'=>$wCode, 'store'=>$sCode)),
                        'selected' => $curStore==$sCode,
                        'style'    => 'padding-left:32px;',
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
                'label'     => __('New Website'),
                'onclick'   => "location.href='".Mage::getUrl('*/system_website/new')."'",
                'class'     => 'add',
            ))->toHtml();
        } elseif (!$curStore) {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => __('Edit Website'),
                'onclick'   => "location.href='".Mage::getUrl('*/system_website/edit', array('website'=>$curWebsite))."'",
            ))->toHtml();
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => __('New Store'),
                'onclick'   => "location.href='".Mage::getUrl('*/system_store/new', array('website'=>$curWebsite))."'",
                'class'     => 'add',
            ))->toHtml();
        } else {
            $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => __('Edit Store'),
                'onclick'   => "location.href='".Mage::getUrl('*/system_store/edit', array('store'=>$curStore))."'",
            ))->toHtml();
        }
        
        return $html;
    }
}
