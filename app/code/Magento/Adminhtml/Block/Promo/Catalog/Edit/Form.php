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
 * description
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Promo_Catalog_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('promo_catalog_form');
        $this->setTitle(__('Rule Information'));
    }

    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
