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
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Rating_Model_RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Review_Model_Session
     */
    protected $_reviewSession;

    /**
     * @param Magento_Review_Helper_Data $reviewData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Rating_Model_RatingFactory $ratingFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Session_Generic $reviewSession,
        Magento_Review_Helper_Data $reviewData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Rating_Model_RatingFactory $ratingFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_reviewSession = $reviewSession;
        $this->_reviewData = $reviewData;
        $this->_customerSession = $customerSession;
        $this->_productFactory = $productFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_storeManager = $storeManager;
        /** @todo Should be fixed in scope of MAGETWO-14639 */
        $this->_reviewSession = Magento_Core_Model_ObjectManager::getInstance()->get('Magento_Review_Model_Session');
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $data = $this->_reviewSession->getFormData(true);
        $data = new Magento_Object((array)$data);

        // add logged in customer name as nickname
        if (!$data->getNickname()) {
            $customer = $this->_customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setNickname($customer->getFirstname());
            }
        }

        $this->setAllowWriteReviewFlag(
            $this->_customerSession->isLoggedIn() || $this->_reviewData->getIsGuestAllowToWrite()
        );
        if (!$this->getAllowWriteReviewFlag()) {
            $queryParam = $this->_coreData->urlEncode(
                $this->getUrl('*/*/*', array('_current' => true)) . '#review-form'
            );
            $this->setLoginLink($this->getUrl(
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
        $product = $this->_productFactory->create();
        return $product->load($this->getRequest()->getParam('id'));
    }

    public function getAction()
    {
        $productId = $this->getRequest()->getParam('id', false);
        return $this->getUrl('review/product/post', array('id' => $productId));
    }

    public function getRatings()
    {
        return $this->_ratingFactory->create()
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->addRatingPerStoreName($this->_storeManager->getStore()->getId())
            ->setStoreFilter($this->_storeManager->getStore()->getId())
            ->setActiveFilter(true)
            ->load()
            ->addOptionToItems();
    }
}
