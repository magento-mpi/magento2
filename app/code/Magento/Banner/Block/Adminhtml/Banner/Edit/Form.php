<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Banner_Block_Adminhtml_Banner_Edit_Form
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Banner_Block_Adminhtml_Banner_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $banner = Mage::registry('current_banner');

        if ($banner->getId()) {
            $form->addField('banner_id', 'hidden', array(
                'name' => 'banner_id',
            ));
            $form->setValues($banner->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
