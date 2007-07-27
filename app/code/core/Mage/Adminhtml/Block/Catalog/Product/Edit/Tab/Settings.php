<?php
/**
 * Create product settings tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form 
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('settings', array('legend'=>__('Create Product Settings')));
		
		$entityType = Mage::registry('product')->getResource()->getConfig();

		$fieldset->addField('attribute_set_id', 'select', array(
            'label' => __('Attribute Set'),
            'title' => __('Attribute Set'),
            'name'  => 'set',
            'value' => $entityType->getDefaultAttributeSetId(),
            'values'=> Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityType->getId())
                ->load()
                ->toOptionArray()
		));
		
		$fieldset->addField('attribute_type', 'select', array(
            'label' => __('Product Type'),
            'title' => __('Product Type'),
            'name'  => 'type',
            'value' => ''
		));
		
		$this->setForm($form);
	}
}
