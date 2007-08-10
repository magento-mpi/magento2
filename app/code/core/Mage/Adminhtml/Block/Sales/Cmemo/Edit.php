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

class Mage_Adminhtml_Block_Sales_Cmemo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'invoice_id';
        $this->_controller = 'sales_cmemo';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Invoice'));
        $this->_updateButton('delete', 'label', __('Delete Invoice'));

    }

    public function getHeaderText()
    {
        return __('Edit Invoice #') . " '" . Mage::registry('sales_invoice')->getIncrementId() . "'";
    }

}
