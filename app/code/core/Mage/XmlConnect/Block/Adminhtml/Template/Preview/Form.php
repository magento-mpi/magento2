<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin form widget
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Template_Preview_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Preparing from for revision page
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Template_Preview_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id'        => 'preview_form',
                'action'    => $this->getUrl('*/*/drop', array('_current' => true)),
                'method'    => 'post'
        ));

        if ($data = $this->getTemplateFormData()) {
            $mapper = array('preview_store_id' => 'store_id');

            foreach ($data as $key => $value) {
                if (array_key_exists($key, $mapper)) {
                    $name = $mapper[$key];
                } else {
                    $name = $key;
                }
                $form->addField($key, 'hidden', array('name' => $name));
            }
            $form->setValues($data);
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

