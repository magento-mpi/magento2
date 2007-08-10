<?php
/**
 * Multishipping checkout state model
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Model_Type_Multishipping_State extends Varien_Object
{
    const STEP_SELECT_ADDRESSES = 'multishipping_addresses';
    const STEP_SHIPPING         = 'multishipping_shipping';
    const STEP_BILLING          = 'multishipping_billing';
    const STEP_OVERVIEW         = 'multishipping_overview';
    const STEP_SUCCESS          = 'multishipping_success';
    
    protected $_steps;
    
    public function __construct()
    {
        parent::__construct();
        $this->_steps = array(
            self::STEP_SELECT_ADDRESSES => new Varien_Object(array(
                'label' => __('Select Addresses')
            )),
            self::STEP_SHIPPING => new Varien_Object(array(
                'label' => __('Shipping Information')
            )),
            self::STEP_BILLING => new Varien_Object(array(
                'label' => __('Billing Information')
            )),
            self::STEP_OVERVIEW => new Varien_Object(array(
                'label' => __('Place Order')
            )),
            self::STEP_SUCCESS => new Varien_Object(array(
                'label' => __('Order Success')
            )),
        );
        
        $this->_steps[$this->getActiveStep()]->setIsActive(true);
    }
    
    /**
     * Retrieve available checkout steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }
    
    /**
     * Retrieve active step code
     *
     * @return string
     */
    public function getActiveStep()
    {
        $step = $this->getCheckoutSession()->getCheckoutState();
        if (isset($this->_steps[$step])) {
            return $step;
        }
        return self::STEP_SELECT_ADDRESSES;
    }
    
    public function setActiveStep($step)
    {
        if (isset($this->_steps[$step])) {
            $step = $this->getCheckoutSession()->setCheckoutState($step);
        }
        else {
            $step = $this->getCheckoutSession()->setCheckoutState(self::STEP_SELECT_ADDRESSES);
        }
        return $this;
    }
    
    public function canSelectAddresses()
    {
        
    }
    
    public function canInputShipping()
    {
        
    }
    
    public function canSeeOverview()
    {
        
    }
    
    public function canSuccess()
    {
        
    }
    
    /**
     * Retrieve checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
