<?php
/**
 * Currency edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Currency_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('currency_edit_form');
        $this->setTitle(__('Currency Information'));
    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'currency_edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}