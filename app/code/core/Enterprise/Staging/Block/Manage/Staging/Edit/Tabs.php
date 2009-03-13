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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging edit tabs
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_staging_tabs');
        $this->setDestElementId('enterprise_staging_edit_form');
        $this->setTitle(Mage::helper('enterprise_staging')->__('Staging Information'));
    }

    protected function _prepareLayout()
    {
        $staging = $this->getStaging();

        $setId = $staging->getDatasetId();
        if (!$setId) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $this->addTab('general', array(
                'label'     => Mage::helper('enterprise_staging')->__('Staging General Info'),
                'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_edit_tabs_general')->toHtml(),
            ));

            $this->addTab('website', array(
                'label'     => Mage::helper('enterprise_staging')->__('Website'),
                'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_edit_tabs_website')->toHtml(),
            ));

            $this->addTab('item', array(
                'label'     => Mage::helper('enterprise_staging')->__('Items'),
                'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_edit_tabs_item')->toHtml(),
            ));

            $this->addTab('event', array(
                'label'     => Mage::helper('enterprise_staging')->__('Event History'),
                'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_edit_tabs_event')->toHtml(),
            ));

        } else {
            $this->addTab('set', array(
                'label'     => Mage::helper('enterprise_staging')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_edit_tabs_settings')->toHtml(),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive product object from object if not from registry
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlockHtml($groupId)
    {
    	$block = $this->getLayout()->createBlock($this->_attributeTabBlock);
    	$block->setGroup($groupId);
        if ($block) {
            return $block->toHtml();
        } else {
        	return '';
        }
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }
}