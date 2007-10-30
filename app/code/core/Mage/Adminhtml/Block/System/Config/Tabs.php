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
            
        $url = Mage::getModel('core/url');

        foreach ($sections as $section) {
            $code = $section->getPath();
            $sectionAllowed = $this->checkSectionPermissions($code);

            if (empty($current) && $sectionAllowed) {
                $current = $code;
                $this->getRequest()->setParam('section', $current);
            }
            $label = __($section->getFrontendLabel());
            if ($code == $current) {
                if (!$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store')) {
                    $this->_addBreadcrumb($label);
                } else {
                    $this->_addBreadcrumb($label, '', $url->getUrl('*/*/*', array('section'=>$code)));
                }
            }
            if ( $sectionAllowed ) {
                $this->addTab($code, array(
                    'label'     => $label,
                    'url'       => $url->getUrl('*/*/*', array('_current'=>true, 'section'=>$code)),
                ));
            }

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

        $url = Mage::getModel('core/url');

        $options = array();
        $options['default'] = array(
            'label'    => __('Default config'),
            'url'      => $url->getUrl('*/*/*', array('section'=>$section)),
            'selected' => !$curWebsite && !$curStore,
            'style'    => 'background:#CCC; font-weight:bold;',
        );
        
        foreach ($websitesConfig->children() as $wCode=>$wConfig) {
        	if ($wConfig->descend('system/website/id')==0) {
        		continue;
        	}
            $options['website_'.$wCode] = array(
                'label'    => (string)$wConfig->descend('system/website/name'),
                'url'      => $url->getUrl('*/*/*', array('section'=>$section, 'website'=>$wCode)),
                'selected' => !$curStore && $curWebsite==$wCode,
                'style'    => 'padding-left:16px; background:#DDD; font-weight:bold;',
            );
            $websiteStores = $wConfig->descend('system/stores');
            if ($websiteStores) {
                foreach ($websiteStores->children() as $sCode=>$sId) {
                    $options['store_'.$sCode] = array(
                        'label'    => (string)$storesConfig->descend($sCode.'/system/store/name'),
                        'url'      => $url->getUrl('*/*/*', array('section'=>$section, 'website'=>$wCode, 'store'=>$sCode)),
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

        switch ($code) {
        	case "general":
        	    if ( $permissions->isAllowed('system/config/general') ) {
        	        $showTab = true;
        	    }
        	    break;
        	case "web":
        	    if ( $permissions->isAllowed('system/config/web') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "design":
        	    if ( $permissions->isAllowed('system/config/design') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "customer":
        	    if ( $permissions->isAllowed('system/config/customers') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "sales":
        	    if ( $permissions->isAllowed('system/config/sales') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "newsletter":
        	    if ( $permissions->isAllowed('system/config/newsletter') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "web_track":
        	    if ( $permissions->isAllowed('system/config/tracking') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "catalog":
        	    if ( $permissions->isAllowed('system/config/catalog') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "wishlist":
        	    if ( $permissions->isAllowed('system/config/wishlist') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "shipping":
        	    if ( $permissions->isAllowed('system/config/shipping') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "carriers":
        	    if ( $permissions->isAllowed('system/config/shipping_methods') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "payment":
        	    if ( $permissions->isAllowed('system/config/payment_methods') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "system":
        	    if ( $permissions->isAllowed('system/config/system') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "advanced":
        	    if ( $permissions->isAllowed('system/config/advanced') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "trans_email":
        	    if ( $permissions->isAllowed('system/config/store_email_addresses') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "paypal":
        	    if ( $permissions->isAllowed('system/config/paypal') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "dev":
        	    if ( $permissions->isAllowed('system/config/developer') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "currency":
        	    if ( $permissions->isAllowed('system/config/currency') ) {
        	        $showTab = true;
        	    }
        	    break;

        	case "allow":
        	    if ( $permissions->isAllowed('system/config/currency') ) {
        	        $showTab = true;
        	    }
        	    break;
        	    
        	case "sendfriend":
        	    if ( $permissions->isAllowed('system/config/sendfriend') ) {
        	        $showTab = true;
        	    }
        	    break;
        	    
        	default:
        		break;
        }
        return $showTab;
    }
}
