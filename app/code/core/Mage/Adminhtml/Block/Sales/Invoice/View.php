<?php
/**
 * Adminhtml sales invoice view
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_View extends Mage_Adminhtml_Block_Widget_View_Container
{

    public function __construct()
    {
        $this->_objectId = 'invoice_id';
        $this->_controller = 'sales_invoice';

        parent::__construct();

        // $this->_updateButton('edit', 'label', __('Edit Invoice'));
        $this->_removeButton('edit');
        $this->_addButton('credit_memo', array(
            'label' => __('Create Credit Memo'),
        ));

        $this->setId('sales_invoice_view');
    }

    public function getHeaderText()
    {
        return __('Invoice #') . Mage::registry('sales_entity')->getIncrementId();
    }

}
