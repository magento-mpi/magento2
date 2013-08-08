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
 * Cache management form page
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Cache_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Initialize cache management form
     *
     * @return Mage_Adminhtml_Block_System_Cache_Form
     */
    public function initForm()
    {
        $form = new Magento_Data_Form();

        $fieldset = $form->addFieldset('cache_enable', array(
            'legend' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Cache Control')
        ));

        $fieldset->addField('all_cache', 'select', array(
            'name'=>'all_cache',
            'label'=>'<strong>'.Mage::helper('Mage_Adminhtml_Helper_Data')->__('All Cache').'</strong>',
            'value'=>1,
            'options'=>array(
                '' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('No change'),
                'refresh' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Refresh'),
                'disable' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Disable'),
                'enable' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Enable'),
            ),
        ));

        foreach (Mage::helper('Mage_Core_Helper_Data')->getCacheTypes() as $type=>$label) {
            $fieldset->addField('enable_'.$type, 'checkbox', array(
                'name'=>'enable['.$type.']',
                'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__($label),
                'value'=>1,
                'checked'=>(int)Mage::app()->useCache($type),
                //'options'=>$options,
            ));
        }

        $this->setForm($form);

        return $this;
    }
}
