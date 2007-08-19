<?php
/**
 * Adminhtml invoice edit
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'invoice_id';
        $this->_controller = 'sales_invoice';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('save');
        $this->_removeButton('reset');

        $this->_addButton('credit_memo', array(
            'label' => __('Create Credit Memo'),
            'onclick'   => 'window.location.href=\'' . $this->getCreateMemoUrl() . '\'',
            'class' => 'add',
        ));

    }

    public function getHeaderText()
    {
        return __('Edit Invoice #') . " '" . Mage::registry('sales_invoice')->getIncrementId() . "'";
    }

}
