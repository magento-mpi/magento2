<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Permissions_Block_Catalog_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected function _prepareLayout()
    {
        $this->setChild('permissions_store_switcher',
            $this->getLayout()->createBlock('enterprise_permissions/store_switcher')
                ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, '_query'=>false, 'store'=>null)))
                ->setTemplate('enterprise/permissions/store/switcher/enhanced.phtml')
        );
        return parent::_prepareLayout();
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('permissions_store_switcher');
    }

    protected function _allowNodesDrag()
    {
        return false;
    }
    
    public function getAddRootButtonHtml()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return parent::getAddRootButtonHtml();
        }
        
        if( Mage::helper('enterprise_permissions')->hasAnyWebsiteScopeAccess()) {
            return parent::getAddRootButtonHtml();
        }
        
        return false;
    }
    
    public function getAddSubButtonHtml()
    {
        if( Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            return parent::getAddSubButtonHtml();
        }
        
        if( Mage::helper('enterprise_permissions')->hasAnyWebsiteScopeAccess()) {
            return parent::getAddSubButtonHtml();
        }
        
        return false;
    }
}
