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

class Enterprise_Permissions_Model_Validate_Block extends Enterprise_Permissions_Model_Validate_Abstract
{
    public function filterCustomerGrid($collection, $request)
    {
        $collection->addAttributeToFilter('website_id', array('IN' => Mage::helper('enterprise_permissions')->getRelevantWebsites()));
    }

    public function filterCustomerOnlineGrid($collection, $request)
    {
        $collection->addFieldToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
    }

    public function filterCatalogProductGrid($collection, $request)
    {
        $collection->addStoreFilter($request->getParam('store'));
    }

    public function filterCatalogProductTagGrid($collection, $request)
    {
        $collection->addStoreFilter($request->getParam('store'));
    }

    public function filterCatalogProductReviewGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['visible_in']) ) {
        	$collection->setStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }
    }

    public function filterReviewGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['visible_in']) ) {
        	$collection->setStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }
    }

    public function filterSalesOrderGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            $storeId = $request->getParam('store') ? $request->getParam('store') : array('IN' => $allowedStores);
            $collection->addAttributeToFilter('store_id', $storeId);
        }
    }

    public function filterSalesInvoiceGrid($collection, $request)
    {
        $collection->addAttributeToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
    }

    public function filterSalesShipmentGrid($collection, $request)
    {
        $collection->addAttributeToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
    }

    public function filterSalesCreditmemoGrid($collection, $request)
    {
        $collection->addAttributeToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
    }

    public function filterUrlrewriteGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            $storeId = $request->getParam('store') ? $request->getParam('store') : array('IN' => $allowedStores);
            $collection->addFieldToFilter('store_id', $storeId);
        }
    }

    public function filterCatalogSearchGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
            $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();
            $storeId = $request->getParam('store') ? $request->getParam('store') : array('IN' => $allowedStores);
            $collection->addFieldToFilter('store_id', $storeId);
        }
    }

    public function filterNewsletterSubscriberGrid($collection, $request)
    {
        $collection->addFieldToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
    }

    public function filterReportCommonGrid($collection, $request)
    {
        if (!$request->getParam('store') && !$request->getParam('website') && !$request->getParam('group') ) {
            $collection->setStoreIds(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }
    }

    public function filterReportShopcartProductGrid($collection, $request)
    {
        if (!$request->getParam('store') && !$request->getParam('website') && !$request->getParam('group') ) {
    	   $collection->addWebsiteFilter(Mage::helper('enterprise_permissions')->getAllowedWebsites());
        }
    }

    public function filterReportShopcartAbandonedGrid($collection, $request)
    {
        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addFieldToFilter('website_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedWebsites()));
        }
    }

    public function filterReportTagCustomerGrid($collection, $request)
    {
        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addFieldToFilter('website_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedWebsites()));
        }
    }

    public function filterReportTagProductGrid($collection, $request)
    {
        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addWebsiteFilter(Mage::helper('enterprise_permissions')->getAllowedWebsites());
        }
    }

    public function filterReportTagPopularGrid($collection, $request)
    {
        if( !$request->getParam('website') && !$request->getParam('store') && !$request->getParam('group') ) {
        	$collection->addStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }
    }

    public function filterReportSearchGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
        	$collection->addFieldToFilter('store_id', array('IN' => Mage::helper('enterprise_permissions')->getAllowedStoreViews()));
        }
    }

    public function filterCmsCommonGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
        	$collection->addStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }
    }

    public function filterPollGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['visible_in']) ) {
        	$collection->addStoresFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }
    }

    public function filterSystemDesignGrid($collection, $request, $filterValues)
    {
        if( !is_array($filterValues) || !isset($filterValues['store_id']) ) {
        	$collection->addStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
        }
    }

    public function filterRatingGrid($collection, $request)
    {
        $collection->setStoreFilter(Mage::helper('enterprise_permissions')->getAllowedStoreViews());
    }

    public function filterCustomerBalanceGrid($collection, $request)
    {
        $collection->addWebsitesFilter(Mage::helper('enterprise_permissions')->getAllowedWebsites());
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form $form
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function validateFormField($form, $request, $layout)
    {
        /** @var $elements Varien_Data_Form_Element_Collection */
        $fieldsets = $form->getElements();

        foreach( $fieldsets as $_fieldset ) {
            /** @var $_fieldset Varien_Data_Form_Element_Fieldset */
            foreach ($_fieldset->getElements() as $_element) {
                /** @var $_element Varien_Data_Form_Element_Fieldset */
                if( $_element->getRenderer() instanceof Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element ) {
                    $_element->setRenderer($layout->createBlock('enterprise_permissions/catalog_form_renderer_fieldset_element'));
                }
            }
        }
    }
}