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
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Main banner properties edit form
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Properties extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Set form id prefix, add customer segment binding, set values if banner is editing
     *
     * @return Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Properties
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('banner_properties_');

        $model = Mage::registry('current_banner');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('enterprise_banner')->__('Banner Properties'))
        );

        if ($model->getBannerId()) {
            $fieldset->addField('banner_id', 'hidden', array(
                'name' => 'banner_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('enterprise_banner')->__('Banner Name'),
            'name'      => 'name',
            'required'  => true
        ));

        $fieldset->addField('is_enabled', 'select', array(
            'label'     => Mage::helper('enterprise_banner')->__('Active'),
            'name'      => 'is_enabled',
            'required'  => true,
            'options'   => array(
                Enterprise_Banner_Model_Banner::STATUS_ENABLED  => Mage::helper('enterprise_banner')->__('Yes'),
                Enterprise_Banner_Model_Banner::STATUS_DISABLED => Mage::helper('enterprise_banner')->__('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_enabled', Enterprise_Banner_Model_Banner::STATUS_ENABLED);
        }

        // this field is not used in banners. Just an interface improvement
        $customerSegmentIsAll = $fieldset->addField('customer_segment_is_all', 'select', array(
            'label'     => Mage::helper('enterprise_banner')->__('Customer Segments'),
            'options'   => array(
                    '1' => Mage::helper('enterprise_banner')->__('Any'),
                    '0' => Mage::helper('enterprise_banner')->__('Specified'),
                ),
            'note'      => Mage::helper('enterprise_banner')->__('Applies to Any of the Specified Customer Segments')
        ));

        $fieldset->addField('customer_segment_ids', 'multiselect', array(
            'name'      => 'customer_segment_ids',
            'values'    => Mage::getResourceSingleton('enterprise_customersegment/segment_collection')->toOptionArray(),
            'can_be_empty' => true,
            'after_element_html' => '<script type="text/javascript">//<![CDATA[
function bannerCustomerSegmentIsAll() {
    var isAll = $(\'banner_properties_customer_segment_is_all\').value == \'1\';
    var multiselectElement = $(\'banner_properties_customer_segment_ids\');
    multiselectElement.disabled = isAll;
    if (isAll) {
        multiselectElement.hide();
    } else {
        multiselectElement.show();
    }
}
Event.observe(\'banner_properties_customer_segment_is_all\', \'change\', bannerCustomerSegmentIsAll);
bannerCustomerSegmentIsAll();
//]]></script>'
        ));

        $form->setValues($model->getData());
        $customerSegmentIsAll->setValue($model->getCustomerSegmentIds() ? '0' : '1');
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_banner')->__('Banner Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
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
}
