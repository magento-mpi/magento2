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
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * Dependency injections, set identifier and title
     */
    public function __construct(array $data = array())
    {
        parent::__construct();

        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }
        if (isset($data['current_customer'])) {
            $this->_customer = $data['current_customer'];
        } else {
            $this->_customer = Mage::registry('current_customer');
        }
        if (isset($data['auth_session'])) {
            $this->_authSession = $data['auth_session'];
        } else {
            $this->_authSession = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        }

        $this->setId('tags');
        $this->setTitle($this->_helper('Mage_Tag_Helper_Data')->__('Product Tags'));
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
        if (!$this->_customer) {
            return false;
        }
        return $this->_customer->getId() && $this->_authSession->isAllowed('Mage_Tag::tag');
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
