<?php
/**
 * Admin CMS page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_System_Currency_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'currency_code';
        $this->_controller = 'system_currency';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Currency'));
        //$this->_updateButton('delete', 'label', __('Delete Page'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('currency')->getId()) {
            return __('Edit "%s"', Mage::registry('currency')->getCurrencyName());
        }
        return __('New Currency');
    }

}
