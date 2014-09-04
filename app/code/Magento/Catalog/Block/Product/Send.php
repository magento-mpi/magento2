<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product;

/**
 * Product send to friend block
 */
class Send extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Customer view helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerView;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\View $customerView
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\View $customerView,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerView = $customerView;
        parent::__construct(
            $context,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->_customerView->getCustomerName($this->_customerSession->getCustomerDataObject());
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return (string)$this->_customerSession->getCustomerDataObject()->getEmail();
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }
}
