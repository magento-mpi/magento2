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
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Main_Filter extends Magento_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();

        $collection = Mage::getModel('Magento_Eav_Model_Entity_Attribute_Set')
            ->getResourceCollection()
            ->load()
            ->toOptionArray();

        $form->addField('set_switcher', 'select',
            array(
                'name' => 'set_switcher',
                'required' => true,
                'class' => 'left-col-block',
                'no_span' => true,
                'values' => $collection,
                'onchange' => 'this.form.submit()',
            )
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);
    }
}
