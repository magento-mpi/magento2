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
        $this->setTemplate('adminhtml/customer/tab/newsletter.phtml');
    }
    
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_newsletter');
        $customer = Mage::registry('customer');        
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        Mage::register('subscriber', $subscriber);
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Newsletter Information')));
                        
        $fieldset->addField('subscription', 'checkbox',
             array(
                    'label' => __('Subscribe to Newsletter?'),
                    'name'  => 'subscription'                    
             )
        );
        
        $form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());
        
        if($changedDate = $this->getStatusChangedDate()) {
        	 $fieldset->addField('change_status_date', 'label',
	             array(
	                    'label' => $subscriber->isSubscribed() ? __('Last date subscribed') : __('Last date unsubscribed'),
	                    'value'	=> $changedDate,
	                    'bold'	=> true
	             )
	        );
        }
        
        
        $this->setForm($form);
        return $this;
    }
    
    public function getStatusChangedDate()
    {
    	$subscriber = Mage::registry('subscriber');
    	if($subscriber->getChangeStatusAt()) {
    		return strftime(Mage::getStoreConfig('general/local/datetime_format_medium'), strtotime($subscriber->getChangeStatusAt()));
    	} 
    	
    	return null;
    }
    
    protected function _initChildren() 
    {
    	$this->setChild('grid',
    		$this->getLayout()->createBlock('adminhtml/customer_edit_tab_newsletter_grid','newsletter.grid')
    	);
    }
}
