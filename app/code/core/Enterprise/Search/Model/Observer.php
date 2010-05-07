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
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * Enterprise search model observer
 *
 * Dynamic add fields to attribute edit form
 * (see Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main)
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Observer
{
    /**
     * Add search weight field to attribute edit form (only for quick search)
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Search_Model_Observer
     */
    public function eavAttributeEditFormInit(Varien_Event_Observer $observer)
    {
        $engine = Mage::getStoreConfig('catalog/search/engine');
        if ($engine == 'enterprise_search/engine') {
            $form      = $observer->getEvent()->getForm();
            $attribute = $observer->getEvent()->getAttribute();
            $fieldset  = $form->getElement('front_fieldset');

            $fieldset->addField('search_weight', 'select', array(
                'name'        => 'search_weight',
                'label'       => Mage::helper('catalog')->__('Search Weight'),
                'values'      => Enterprise_Search_Model_Weight::getOptions(),
            ), 'is_searchable');

            /**
             * Disable default search fields
             */
            $attributeCode = $attribute->getAttributeCode();
            $searchModel = Mage::getModel('enterprise_search/adapter_phpextension');
            $defaultSearchTextFields = $searchModel->getSearchTextFields();
            if (in_array($attributeCode, $defaultSearchTextFields)) {
                $form->getElement('is_searchable')->setDisabled(1);
            }
        }

        return $this;
    }
}
