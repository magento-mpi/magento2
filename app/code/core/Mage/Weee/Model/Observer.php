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
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Weee_Model_Observer extends Mage_Core_Model_Abstract
{
    public function setWeeeRendererInForm(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form

        $form = $observer->getEvent()->getForm();
        $product = $observer->getEvent()->getProduct();

        $attributes = Mage::getModel('weee/tax')->getWeeeAttributeCodes();
        foreach ($attributes as $code) {
            if ($weeeTax = $form->getElement($code)) {
                $weeeTax->setRenderer(
                    Mage::app()->getLayout()->createBlock('weee/renderer_weee_tax')
                );
            }
        }
    }

    public function updateExcludedFieldList(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $attributes = Mage::getModel('weee/tax')->getWeeeAttributeCodes();
        foreach ($attributes as $code) {
            $list[] = $code;
        }
        $block->setFormExcludedFieldList($list);
    }

    public function prepareCatalogIndexSelect(Varien_Event_Observer $observer)
    {
        switch(Mage::helper('weee')->getListPriceDisplayType()) {
            case 2:
            case 3:
                return;
        }
        // catalogindex_prepare_price_select

        $select = $observer->getEvent()->getSelect();
        $table = $observer->getEvent()->getTable();
        $storeId = $observer->getEvent()->getStoreId();

        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        $response = $observer->getEvent()->getResponseObject();

        $additionalCalculations = $response->getAdditionalCalculations();

        $attributes = Mage::getModel('weee/tax')->getWeeeAttributeCodes();
        foreach ($attributes as $attribute) {
            $tableAlias = "weee_{$attribute}_table";
            $additionalCalculations[] = "+(IFNULL({$tableAlias}.value, 0))";
        }
        $response->setAdditionalCalculations($additionalCalculations);

        $rateRequest = Mage::getModel('tax/calculation')->getRateRequest();
        $attributes = array();
        $attributes = Mage::getModel('weee/tax')->getWeeeTaxAttributeCodes();
        foreach ($attributes as $attribute) {
            $attributeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', $attribute);
    
            $tableAlias = "weee_{$attribute}_table";
            $on = array();
            $on[] = "{$tableAlias}.attribute_id = '{$attributeId}'";
            $on[] = "({$tableAlias}.website_id in ('{$websiteId}', 0))";
    
            $country = $rateRequest->getCountryId();
            $on[] = "({$tableAlias}.country = '{$country}')";

            $region = $rateRequest->getRegionId();
            $on[] = "({$tableAlias}.state in ('{$region}', '*'))";

            $attributeSelect = $this->_getSelect();
            $attributeSelect->from(array($tableAlias=>Mage::getModel('weee/tax')->getResource()->getTable('weee/tax')));


            foreach ($on as $one) {
                $attributeSelect->where($one);
            }
            $attributeSelect->limit(1);

            $order = array($tableAlias.'.state DESC', $tableAlias.'.website_id DESC');

            $attributeSelect->order($order);
            $select->joinLeft(array($tableAlias=>$attributeSelect), $table.'.entity_id = '.$tableAlias.'.entity_id', array());
        }

    }

    protected function _getSelect()
    {
        return Mage::getModel('weee/tax')->getResource()->getReadConnection()->select();
    }

    public function addWeeeTaxAttributeType(Varien_Event_Observer $observer)
    {
        // adminhtml_product_attribute_types

        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = array(
            'value' => 'weee',
            'label' => Mage::helper('weee')->__('Fixed product tax'),
            'hide_fields' => array(
                'is_unique',
                'is_required',
                'frontend_class',
                'is_configurable',

                '_scope',
                '_default_value',
                '_front_fieldset',
            )
        );

        $response->setTypes($types);
    }

    public function assignBackendModelToAttribute(Varien_Event_Observer $observer)
    {
        $backendModel = 'weee/attribute_backend_weee_tax';
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'weee') {
            $object->setBackendModel($backendModel);
        }
    }
}