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
 * Create staging settings tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('enterprise_staging')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','staging_website_ids','staging_entity_set_id','type')",
                    'class'     => 'save'
            ))
        );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('enterprise_staging')->__('Create Staging Settings')));

        $websiteCollection = Mage::getModel('core/website')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Website::CACHE_TAG));
        $websiteCollection->addFieldToFilter('is_staging',array('neq'=>1));

        $fieldset->addField('staging_website_ids', 'select', array(
            'label' => Mage::helper('enterprise_staging')->__('Staging Source Website'),
            'title' => Mage::helper('enterprise_staging')->__('Staging Source Website'),
            'name'  => 'websites[]',
            'value' => '',
            'values'=> $websiteCollection->toOptionArray()
        ));

        $fieldset->addField('staging_entity_set_id', 'select', array(
            'label' => Mage::helper('enterprise_staging')->__('Staging Entity Set'),
            'title' => Mage::helper('enterprise_staging')->__('Staging Entity Set'),
            'name'  => 'set',
            'value' => '',
            'values'=> Mage::getResourceModel('enterprise_staging/dataset_collection')
                ->load()
                ->toOptionArray()
        ));

        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('enterprise_staging')->__('Staging Type'),
            'title' => Mage::helper('enterprise_staging')->__('Staging Type'),
            'name'  => 'type',
            'value' => '',
            'values'=> Enterprise_Staging_Model_Staging_Config::getOptionArray('type')
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/edit', array(
            '_current'  => true,
            'websites'   => '{{websites}}',
            'set'       => '{{set}}',
            'type'      => '{{type}}'
        ));
    }
}