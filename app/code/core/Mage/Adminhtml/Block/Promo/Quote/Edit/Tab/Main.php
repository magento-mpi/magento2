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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main
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
        return Mage::helper('Mage_SalesRule_Helper_Data')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_SalesRule_Helper_Data')->__('Rule Information');
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
        $model = Mage::registry('current_promo_quote_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('product_ids', 'hidden', array(
            'name' => 'product_ids',
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Rule Name'),
            'title' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Description'),
            'title' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Description'),
            'style' => 'width: 98%; height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('Mage_SalesRule_Helper_Data')->__('Status'),
            'title'     => Mage::helper('Mage_SalesRule_Helper_Data')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Active'),
                '0' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Inactive'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }


        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'      => 'website_ids[]',
                'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Websites'),
                'title'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Websites'),
                'required'  => true,
                'values'    => Mage::getSingleton('Mage_Adminhtml_Model_System_Config_Source_Website')->toOptionArray(),
            ));
        }
        else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name'      => 'website_ids[]',
                'value'     => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $model->setWebsiteIds(Mage::app()->getStore(true)->getWebsiteId());
        }

        $customerGroups = Mage::getResourceModel('Mage_Customer_Model_Resource_Group_Collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value'=>0, 'label'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('NOT LOGGED IN')));
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('Mage_SalesRule_Helper_Data')->__('Customer Groups'),
            'title'     => Mage::helper('Mage_SalesRule_Helper_Data')->__('Customer Groups'),
            'required'  => true,
            'values'    => $customerGroups,
        ));

        $couponTypeFiled = $fieldset->addField('coupon_type', 'select', array(
            'name'       => 'coupon_type',
            'label'      => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon'),
            'required'   => true,
            'options'    => Mage::getModel('Mage_SalesRule_Model_Rule')->getCouponTypes(),
        ));

        $couponCodeFiled = $fieldset->addField('coupon_code', 'text', array(
            'name' => 'coupon_code',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon Code'),
            'required' => true,
        ));

        $usesPerCouponFiled = $fieldset->addField('uses_per_coupon', 'text', array(
            'name' => 'uses_per_coupon',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Uses per Coupon'),
        ));

        $fieldset->addField('uses_per_customer', 'text', array(
            'name' => 'uses_per_customer',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Uses per Customer'),
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('Mage_SalesRule_Helper_Data')->__('From Date'),
            'title'  => Mage::helper('Mage_SalesRule_Helper_Data')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('Mage_SalesRule_Helper_Data')->__('To Date'),
            'title'  => Mage::helper('Mage_SalesRule_Helper_Data')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Priority'),
        ));

        $fieldset->addField('is_rss', 'select', array(
            'label'     => Mage::helper('Mage_SalesRule_Helper_Data')->__('Public In RSS Feed'),
            'title'     => Mage::helper('Mage_SalesRule_Helper_Data')->__('Public In RSS Feed'),
            'name'      => 'is_rss',
            'options'   => array(
                '1' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Yes'),
                '0' => Mage::helper('Mage_SalesRule_Helper_Data')->__('No'),
            ),
        ));

        if(!$model->getId()){
            //set the default value for is_rss feed to yes for new promotion
            $model->setIsRss(1);
        }

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        //$form->setUseContainer(true);

        $this->setForm($form);

        // field dependencies
        $this->setChild('form_after', $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Widget_Form_Element_Dependence')
            ->addFieldMap($couponTypeFiled->getHtmlId(), $couponTypeFiled->getName())
            ->addFieldMap($couponCodeFiled->getHtmlId(), $couponCodeFiled->getName())
            ->addFieldMap($usesPerCouponFiled->getHtmlId(), $usesPerCouponFiled->getName())
            ->addFieldDependence(
                $couponCodeFiled->getName(),
                $couponTypeFiled->getName(),
                Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
            ->addFieldDependence(
                $usesPerCouponFiled->getName(),
                $couponTypeFiled->getName(),
                Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
        );

        Mage::dispatchEvent('adminhtml_promo_quote_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
