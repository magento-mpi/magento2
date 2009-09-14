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
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Main banner properties edit form
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Properties
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_properties');

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
            'title'     => Mage::helper('enterprise_banner')->__('Banner Name'),
            'name'      => 'name',
            'required'  => true
        ));

        $fieldset->addField('is_enabled', 'select', array(
            'label'     => Mage::helper('enterprise_banner')->__('Active'),
            'title'     => Mage::helper('enterprise_banner')->__('Active'),
            'name'      => 'is_enabled',
            'required'  => true,
            'options'   => array(
                Enterprise_Banner_Model_Banner::STATUS_ENABLED =>
                    Mage::helper('enterprise_banner')->__('Yes'),
                Enterprise_Banner_Model_Banner::STATUS_DISABLED =>
                    Mage::helper('enterprise_banner')->__('No'),
            ),
        ));


        $form->setValues($model->getData());
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
        return Mage::helper('enterprise_banner')->__('Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_banner')->__('Properties');
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
