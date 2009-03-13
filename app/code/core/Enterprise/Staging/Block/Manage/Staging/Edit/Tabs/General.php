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
 * Staging general info tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        
        $fieldset = $form->addFieldset('general_fieldset', array('legend'=>Mage::helper('enterprise_staging')->__('Main Info')));

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('enterprise_staging')->__('Staging name'),
            'title'     => Mage::helper('enterprise_staging')->__('Staging name'),
            'name'      => 'name'
        ));
        
        /*
        $fieldset->addField('code', 'text', array(
            'label'     => Mage::helper('enterprise_staging')->__('Staging code'),
            'title'     => Mage::helper('enterprise_staging')->__('Staging code'),
            'name'      => 'code'
        ));

        $fieldset = $form->addFieldset('visibility_fieldset', array('legend'=>Mage::helper('enterprise_staging')->__('Visibility Info')));

        $fieldset->addField('visibility', 'select', array(
            'label'     => Mage::helper('enterprise_staging')->__('Frontend Visibility'),
            'title'     => Mage::helper('enterprise_staging')->__('Frontend Visibility'),
            'name'      => 'visibility',
            'value'     => Enterprise_Staging_Model_Config::VISIBILITY_WHILE_MASTER_LOGIN,
            'options'   => Enterprise_Staging_Model_Config::getOptionArray()
        ));
        
        $fieldset = $form->addFieldset('authorization', array('legend'=>Mage::helper('enterprise_staging')->__('Staging Frontend Authorization Info')));
        
        $fieldset->addField('master_login', 'text',
            array(
                'label' => Mage::helper('enterprise_staging')->__('Master Login'),
                'class' => 'input-text required-entry validate-login',
                'name'  => 'master_login',
                'required' => true
            )
        );
        
        $fieldset->addField('master_password', 'text',
            array(
                'label' => Mage::helper('enterprise_staging')->__('Master Password'),
                'class' => 'input-text required-entry validate-password',
                'name'  => 'master_password',
                'required' => true
            )
        );
        */
        
        $values = $this->getStaging()->getData();

        Mage::dispatchEvent('enterprise_staging_edit_prepare_form', array('form'=>$form));

        $form->addValues($values);
        $form->setFieldNameSuffix('staging');

        $this->setForm($form);
    }
    
    /**
     * Retrieve currently edited staging object
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
}