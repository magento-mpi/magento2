<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Review_Block_Form extends Magento_Core_Block_Template
{
    /**
     * Review data
     *
     * @var Magento_Review_Helper_Data
     */
    protected $_reviewData = null;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Core_Model_Session_Generic
     */
    protected $_reviewSession;

    /**
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Session_Generic $reviewSession
     * @param Magento_Review_Helper_Data $reviewData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Session_Generic $reviewSession,
        Magento_Review_Helper_Data $reviewData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_reviewSession = $reviewSession;
        $this->_reviewData = $reviewData;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        $customerSession = $this->_customerSession;

        parent::_construct();

        $data = $this->_reviewSession->getFormData(true);
        $data = new Magento_Object((array)$data);

        // add logged in customer name as nickname
        if (!$data->getNickname()) {
            $customer = $customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setNickname($customer->getFirstname());
            }
        }

        $this->setAllowWriteReviewFlag(
            $customerSession->isLoggedIn() || $this->_reviewData->getIsGuestAllowToWrite()
        );
        if (!$this->getAllowWriteReviewFlag()) {
            $queryParam = $this->_coreData->urlEncode(
                Mage::getUrl('*/*/*', array('_current' => true)) .
                '#review-form'
            );
            $this->setLoginLink(Mage::getUrl(
                    'customer/account/login/',
                    array(Magento_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => $queryParam)
                )
            );
        }

        $this->setTemplate('form.phtml')
            ->assign('data', $data)
            ->assign('messages', $this->_reviewSession->getMessages(true));
    }

    public function getProductInfo()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        return $product->load($this->getRequest()->getParam('id'));
    }

    public function getAction()
    {
        $productId = Mage::app()->getRequest()->getParam('id', false);
        return Mage::getUrl('review/product/post', array('id' => $productId));
    }

    public function getRatings()
    {
        $ratingCollection = Mage::getModel('Magento_Rating_Model_Rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->setActiveFilter(true)
            ->load()
            ->addOptionToItems();
        return $ratingCollection;
    }
}
