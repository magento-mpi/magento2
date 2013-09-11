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
 * Edit status form
 */
namespace Magento\Adminhtml\Block\Sales\Order\Status\Edit;

class Form extends \Magento\Adminhtml\Block\Sales\Order\Status\New\Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('new_order_status');
    }

    /**
     * Modify structure of new status form
     *
     * @return \Magento\Adminhtml\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $form->getElement('base_fieldset')->removeField('is_new');
        $form->getElement('base_fieldset')->removeField('status');
        $form->setAction(
            $this->getUrl('*/sales_order_status/save', array('status'=>$this->getRequest()->getParam('status')))
        );
        return $this;
    }
}
