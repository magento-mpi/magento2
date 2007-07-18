<?php
/**
 * Customer account form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_newsletter');
        $customer = Mage::registry('customer');        
        $isSubscribed = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer)->isSubscribed(true);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Newsletter Information')));
                        
        $fieldset->addField('subscription', 'checkbox',
             array(
                    'label' => __('Subscribe to Newsletter?'),
                    'name'  => 'subscription'                    
             )
        );
        
        $form->getElement('subscription')->setIsChecked($isSubscribed);
        $this->setForm($form);
        return $this;
    }
}
