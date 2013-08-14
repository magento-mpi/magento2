<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cache management form page
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_System_Cache_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Initialize cache management form
     *
     * @return Magento_Adminhtml_Block_System_Cache_Form
     */
    public function initForm()
    {
        $form = new Magento_Data_Form();

        $fieldset = $form->addFieldset('cache_enable', array(
            'legend' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Cache Control')
        ));

        $fieldset->addField('all_cache', 'select', array(
            'name'=>'all_cache',
            'label'=>'<strong>'.Mage::helper('Magento_Adminhtml_Helper_Data')->__('All Cache').'</strong>',
            'value'=>1,
            'options'=>array(
                '' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('No change'),
                'refresh' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Refresh'),
                'disable' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Disable'),
                'enable' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Enable'),
            ),
        ));

        foreach (Mage::helper('Magento_Core_Helper_Data')->getCacheTypes() as $type=>$label) {
            $fieldset->addField('enable_'.$type, 'checkbox', array(
                'name'=>'enable['.$type.']',
                'label'=>Mage::helper('Magento_Adminhtml_Helper_Data')->__($label),
                'value'=>1,
                'checked'=>(int)Mage::app()->useCache($type),
                //'options'=>$options,
            ));
        }

        $this->setForm($form);

        return $this;
    }
}
