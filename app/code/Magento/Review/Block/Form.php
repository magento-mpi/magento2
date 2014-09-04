<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block;

use Magento\Catalog\Model\Product;
use Magento\Review\Model\Resource\Rating\Collection as RatingCollection;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Rating model
     *
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * Review session model
     *
     * @var \Magento\Review\Model\Session
     */
    protected $_reviewSession;

    /**
     * Core helper data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Message manager interface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\Session\Generic $reviewSession
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\Session\Generic $reviewSession,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_reviewSession = $reviewSession;
        $this->_reviewData = $reviewData;
        $this->_customerSession = $customerSession;
        $this->_productFactory = $productFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->messageManager = $messageManager;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * Initialize review form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $data = $this->_reviewSession->getFormData(true);
        $data = new \Magento\Framework\Object((array)$data);

        // add logged in customer name as nickname
        if (!$data->getNickname()) {
            $customer = $this->_customerSession->getCustomerDataObject();
            if ($customer && $customer->getId()) {
                $data->setNickname($customer->getFirstname());
            }
        }

        $this->setAllowWriteReviewFlag(
            $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)
            || $this->_reviewData->getIsGuestAllowToWrite()
        );
        if (!$this->getAllowWriteReviewFlag()) {
            $queryParam = $this->_coreData->urlEncode(
                $this->getUrl('*/*/*', array('_current' => true)) . '#review-form'
            );
            $this->setLoginLink(
                $this->getUrl(
                    'customer/account/login/',
                    array(\Magento\Customer\Helper\Data::REFERER_QUERY_PARAM_NAME => $queryParam)
                )
            );
        }

        $this->setTemplate(
            'form.phtml'
        )->assign(
            'data',
            $data
        )->assign(
            'messages',
            $this->messageManager->getMessages(true)
        );
    }

    /**
     * Get product info
     *
     * @return Product
     */
    public function getProductInfo()
    {
        $product = $this->_productFactory->create();
        return $product->load($this->getRequest()->getParam('id'));
    }

    /**
     * Get review product post action
     *
     * @return string
     */
    public function getAction()
    {
        $productId = $this->getRequest()->getParam('id', false);
        return $this->getUrl('review/product/post', array('id' => $productId));
    }

    /**
     * Get collection of ratings
     *
     * @return RatingCollection
     */
    public function getRatings()
    {
        return $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
            'product'
        )->setPositionOrder()->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->setActiveFilter(
            true
        )->load()->addOptionToItems();
    }
}
