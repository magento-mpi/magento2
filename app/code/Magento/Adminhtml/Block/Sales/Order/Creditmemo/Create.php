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
 * Adminhtml creditmemo create
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Sales\Order\Creditmemo;

class Create extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_creditmemo';
        $this->_mode = 'create';

        parent::_construct();

        $this->_removeButton('delete');
        $this->_removeButton('save');

        /*$this->_addButton('submit_creditmemo', array(
            'label'     => __('Submit Credit Memo'),
            'class'     => 'save submit-button',
            'onclick'   => '$(\'edit_form\').submit()',
            )
        );*/

    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return \Mage::registry('current_creditmemo');
    }

    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getInvoice()) {
            $header = __('New Credit Memo for Invoice #%1', $this->getCreditmemo()->getInvoice()->getIncrementId());
        }
        else {
            $header = __('New Credit Memo for Order #%1', $this->getCreditmemo()->getOrder()->getRealOrderId());
        }

        return $header;
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array('order_id' => $this->getCreditmemo() ? $this->getCreditmemo()->getOrderId() : null)
        );
    }
}
