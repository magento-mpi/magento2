<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Form
    extends Magento_Backend_Block_Widget_Form_Generic
{

    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_customersegment_segment_form');
        $this->setTitle(__('Segment Information'));
    }

    /**
     * Prepare edit form
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Form
     */
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
