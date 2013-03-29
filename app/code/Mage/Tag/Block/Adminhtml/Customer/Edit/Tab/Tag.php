<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Mage_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag setTitle() setTitle(string $title)
 * @method string getTitle() getTitle()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Tag_Block_Adminhtml_Customer_Edit_Tab_Tag extends Mage_Backend_Block_Template
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * Array of data helpers
     *
     * @var array
     */
    protected $_helpers;

    /**
     * Current customer
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Backend auth session
     *
     * @var Mage_Core_Model_Authorization
     */
    protected $_authSession;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Authorization $authSession
     * @param array $data
     */
    public function __construct(Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Authorization $authSession,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_authSession = $authSession;
        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }
        $this->setId('tags');
        $this->setTitle($this->_helper('Mage_Tag_Helper_Data')->__('Product Tags'));
    }

    /**
     * Set customer object
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Retrieve current customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }

        return $this->_customer;
    }

    /**
     * Helper getter
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _helper($helperName)
    {
        return isset($this->_helpers[$helperName]) ? $this->_helpers[$helperName] : Mage::helper($helperName);
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTitle();
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * Check whether tab can be showed
     *
     * @return bool
     */
    public function canShowTab()
    {
        if (!$this->getCustomer()) {
            return false;
        }
        return $this->getCustomer()->getId() && $this->_authSession->isAllowed('Mage_Tag::tag_all');
    }

    /**
     * Check whether tab should be hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Place current tab after "Product Reviews"
     *
     * @return string
     */
    public function getAfter()
    {
        return 'reviews';
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Tab URL getter
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/customer/productTags', array('_current' => true));
    }
}
