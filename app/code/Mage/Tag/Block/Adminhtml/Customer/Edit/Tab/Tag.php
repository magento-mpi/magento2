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
     * Current customer
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(Mage_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setId('tags');
        $this->setTitle(__('Product Tags'));
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
        return $this->getCustomer()->getId() && $this->_authorization->isAllowed('Mage_Tag::tag_all');
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
