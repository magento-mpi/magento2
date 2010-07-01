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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_app');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('app_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->__('Application Information')));

        if ($model->getId()) {
            $fieldset->addField('application_id', 'hidden', array(
                'name' => 'application_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $this->__('Application Name'),
            'title'     => $this->__('Application Name'),
            'required'  => true,
        ));

        if ($model->getId()) {
            $fieldset->addField('code', 'text', array(
                'name'      => 'code',
                'label'     => $this->__('Application Code'),
                'title'     => $this->__('Application Code'),
                'required'  => true,
                'class'     => 'validate-identifier',
                'disabled'  => 'disabled',
            ));
        }

        $fieldset->addField('conf[special][merchantLabel]', 'text', array(
            'name'      => 'conf[special][merchantLabel]',
            'label'     => $this->__('Merchant Label'),
            'title'     => $this->__('Merchant Label'),
            'required'  => false,
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $options = Mage::helper('xmlconnect')->getStoreDeviceValuesForForm();
            $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => $this->__('Store View'),
                'title'     => $this->__('Store View'),
                'required'  => true,
                'values'    => $options,
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

//        $fieldset->addField('device_type', 'note', array(
//            'text'      => $model->getType(),
//            'label'     => $this->__('Device type'),
//        ));

        $form->setValues($model->getFormData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('General');
    }

    /**
     * Returns status flag about this tab can be shown or not
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
     * @return false
     */
    public function isHidden()
    {
        return false;
    }
}
