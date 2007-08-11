<?php
/**
 * Adminhtml customers list block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'customer';
        $this->_headerText = __('Manage Customers');
        $this->_addButtonLabel = __('Add New Customer');
        parent::__construct();
    }

}
