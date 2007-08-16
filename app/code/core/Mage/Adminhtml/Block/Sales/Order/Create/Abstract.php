<?php
/**
 * Adminhtml sales order create abstract block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Abstract extends Mage_Adminhtml_Block_Widget
{

    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Model_Quote
     */
    protected $_session = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/create/abstract.phtml');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Adminhtml_Model_Quote $session
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Abstract
     */
    public function setSession($session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function getSession()
    {
        if (is_null($this->_session)) {
             $this->setSession(Mage::getSingleton('adminhtml/quote'));
        }
        return $this->_session;
    }

    /**
     *
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    public function getCustomerId()
    {
        return $this->getSession()->getCustomerId();
    }

    public function getIsOldCustomer()
    {
        return $this->getSession()->getIsOldCustomer();
    }

    public function getStoreId()
    {
        return $this->getSession()->getStoreId();
    }

    public function getStore()
    {
        return $this->getSession()->getStore();
    }

    public function formatPrice($price)
    {
        return $this->getSession()->formatPrice($price);
    }

    public function getHeaderText()
    {
        return 'Header Text';
    }

    public function getHeaderCssClass()
    {
        return 'head-edit-form';
    }

    public function getScUrl($action = '')
    {
        if ($action) {
            return Mage::getUrl('*/*/' . $action);
        }
        return Mage::getUrl('*/*');
    }

}
