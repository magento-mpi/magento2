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
 * description
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_CatalogRule_Helper_Data')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_CatalogRule_Helper_Data')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_catalog_rule');

        $form = new Magento_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array(
                'legend' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Update Prices Using the Following Information')
            )
        );

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Apply'),
            'name'      => 'simple_action',
            'options'   => array(
                'by_percent'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('By Percentage of the Original Price'),
                'by_fixed'      => Mage::helper('Mage_CatalogRule_Helper_Data')->__('By Fixed Amount'),
                'to_percent'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('To Percentage of the Original Price'),
                'to_fixed'      => Mage::helper('Mage_CatalogRule_Helper_Data')->__('To Fixed Amount'),
            ),
        ));

        $fieldset->addField('discount_amount', 'text', array(
            'name'      => 'discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Discount Amount'),
        ));

        $fieldset->addField('sub_is_enable', 'select', array(
            'name'      => 'sub_is_enable',
            'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Enable Discount to Subproducts'),
            'title'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Enable Discount to Subproducts'),
            'onchange'  => 'hideShowSubproductOptions(this);',
            'values'    => array(
                0 => Mage::helper('Mage_CatalogRule_Helper_Data')->__('No'),
                1 => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Yes')
            )
        ));

        $fieldset->addField('sub_simple_action', 'select', array(
            'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Apply'),
            'name'      => 'sub_simple_action',
            'options'   => array(
                'by_percent'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('By Percentage of the Original Price'),
                'by_fixed'      => Mage::helper('Mage_CatalogRule_Helper_Data')->__('By Fixed Amount'),
                'to_percent'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('To Percentage of the Original Price'),
                'to_fixed'      => Mage::helper('Mage_CatalogRule_Helper_Data')->__('To Fixed Amount'),
            ),
        ));

        $fieldset->addField('sub_discount_amount', 'text', array(
            'name'      => 'sub_discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Discount Amount'),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'   => array(
                '1' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Yes'),
                '0' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('No'),
            ),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
