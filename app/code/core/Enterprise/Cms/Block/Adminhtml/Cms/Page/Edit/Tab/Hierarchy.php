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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Page Edit Hierarchy Tab Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Hierarchy
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve current page instance
     *
     * @return Mage_Cms_Model_Page
     */
    public function getPage()
    {
        return Mage::registry('cms_page');
    }

    /**
     * Retrieve Hierarchy collection
     *
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Collection
     */
    public function getHierchyCollection()
    {
        if (!$this->hasData('hierarchy_collection')) {
            /* @var $collection Enterprise_Cms_Model_Mysql4_Hierarchy_Collection */
            $collection = Mage::getModel('enterprise_cms/hierarchy')->getCollection()
                ->joinRootNode()
                ->addContainPageFilter($this->getPage());
            $this->setData('hierarchy_collection', $collection);
        }
        return $this->getData('hierarchy_collection');
    }

    /**
     * Retrieve HTML escaped Hierarchy title
     *
     * @param Enterprise_Cms_Model_Hierarchy
     * @return string
     */
    public function getHierarchyTitle($hierarchy)
    {
        if ($hierarchy->getLabel()) {
            return $this->htmlEscape($hierarchy->getLabel());
        }
        return $this->htmlEscape($hierarchy->getPageTitle());
    }

    /**
     * Retrieve Hierarchy edit URL
     *
     * @param Enterprise_Cms_Model_Hierarchy
     * @return string
     */
    public function getHierarchyEditUrl($hierarchy)
    {
        return $this->getUrl('adminhtml/cms_hierarchy/edit', array('tree_id' => $hierarchy->getId()));
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_cms')->__('Hierarchy');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('Hierarchy');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        if (!$this->getPage()->getId()) {
            return false;
        }
        if (!$this->getHierchyCollection()->getItems()) {
            return false;
        }
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
