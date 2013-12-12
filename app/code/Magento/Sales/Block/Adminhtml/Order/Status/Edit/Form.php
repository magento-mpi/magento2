<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit status form
 */
namespace Magento\Sales\Block\Adminhtml\Order\Status\Edit;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Status\NewStatus\Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('new_order_status');
    }

    /**
     * Modify structure of new status form
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $form->getElement('base_fieldset')->removeField('is_new');
        $form->getElement('base_fieldset')->removeField('status');
        $form->setAction(
            $this->getUrl('sales/order_status/save', array('status'=>$this->getRequest()->getParam('status')))
        );
        return $this;
    }
}
