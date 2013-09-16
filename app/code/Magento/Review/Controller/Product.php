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
 * Review controller
 *
 * @category   Magento
 * @package    Magento_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Review\Controller;

class Product extends \Magento\Core\Controller\Front\Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('post');

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $allowGuest = $this->_objectManager->get('Magento\Review\Helper\Data')->getIsGuestAllowToWrite();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        if (!$allowGuest && $action == 'post' && $this->getRequest()->isPost()) {
            if (!\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                \Mage::getSingleton('Magento\Customer\Model\Session')
                    ->setBeforeAuthUrl(\Mage::getUrl('*/*/*', array('_current' => true)));
                \Mage::getSingleton('Magento_Review_Model_Session')
                    ->setFormData($this->getRequest()->getPost())
                    ->setRedirectUrl($this->_getRefererUrl());
                $this->_redirectUrl($this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl());
            }
        }

        return $this;
    }
    /**
     * Initialize and check product
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _initProduct()
    {
        $this->_eventManager->dispatch('review_controller_product_init_before', array('controller_action'=>$this));
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');

        $product = $this->_loadProduct($productId);
        if (!$product) {
            return false;
        }

        if ($categoryId) {
            $category = \Mage::getModel('Magento\Catalog\Model\Category')->load($categoryId);
            $this->_coreRegistry->register('current_category', $category);
        }

        try {
            $this->_eventManager->dispatch('review_controller_product_init', array('product'=>$product));
            $this->_eventManager->dispatch('review_controller_product_init_after', array(
                'product'           => $product,
                'controller_action' => $this
            ));
        } catch (\Magento\Core\Exception $e) {
            \Mage::logException($e);
            return false;
        }

        return $product;
    }

    /**
     * Load product model with data by passed id.
     * Return false if product was not loaded or has incorrect status.
     *
     * @param int $productId
     * @return bool|\Magento\Catalog\Model\Product
     */
    protected function _loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        $product = \Mage::getModel('Magento\Catalog\Model\Product')
            ->setStoreId(\Mage::app()->getStore()->getId())
            ->load($productId);
        /* @var $product \Magento\Catalog\Model\Product */
        if (!$product->getId() || !$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
            return false;
        }

        $this->_coreRegistry->register('current_product', $product);
        $this->_coreRegistry->register('product', $product);

        return $product;
    }

    /**
     * Load review model with data by passed id.
     * Return false if review was not loaded or review is not approved.
     *
     * @param $reviewId
     * @return bool|\Magento\Review\Model\Review
     */
    protected function _loadReview($reviewId)
    {
        if (!$reviewId) {
            return false;
        }

        $review = \Mage::getModel('Magento\Review\Model\Review')->load($reviewId);
        /* @var $review \Magento\Review\Model\Review */
        if (!$review->getId() || !$review->isApproved() || !$review->isAvailableOnStore(\Mage::app()->getStore())) {
            return false;
        }

        $this->_coreRegistry->register('current_review', $review);

        return $review;
    }

    /**
     * Submit new review action
     */
    public function postAction()
    {
        $data = \Mage::getSingleton('Magento\Review\Model\Session')->getFormData(true);
        if ($data) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
            $rating = $this->getRequest()->getParam('ratings', array());
        }

        if (($product = $this->_initProduct()) && !empty($data)) {
            $session    = \Mage::getSingleton('Magento\Core\Model\Session');
            /* @var $session \Magento\Core\Model\Session */
            $review     = \Mage::getModel('Magento\Review\Model\Review')->setData($data);
            /* @var $review \Magento\Review\Model\Review */

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(\Magento\Review\Model\Review::STATUS_PENDING)
                        ->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
                        ->setStoreId(\Mage::app()->getStore()->getId())
                        ->setStores(array(\Mage::app()->getStore()->getId()))
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        \Mage::getModel('Magento\Rating\Model\Rating')
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
                        ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    $session->addSuccess(__('Your review has been accepted for moderation.'));
                } catch (\Exception $e) {
                    $session->setFormData($data);
                    $session->addError(__('We cannot post the review.'));
                }
            } else {
                $session->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                } else {
                    $session->addError(__('We cannot post the review.'));
                }
            }
        }

        $redirectUrl = \Mage::getSingleton('Magento\Review\Model\Session')->getRedirectUrl(true);
        if ($redirectUrl) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    }

    /**
     * Show list of product's reviews
     *
     */
    public function listAction()
    {
        $product = $this->_initProduct();
        if ($product) {
            $this->_coreRegistry->register('productId', $product->getId());

            $design = \Mage::getSingleton('Magento\Catalog\Model\Design');
            $settings = $design->getDesignSettings($product);
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }
            $this->_initProductLayout($product);

            // update breadcrumbs
            $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbsBlock) {
                $breadcrumbsBlock->addCrumb('product', array(
                    'label'    => $product->getName(),
                    'link'     => $product->getProductUrl(),
                    'readonly' => true,
                ));
                $breadcrumbsBlock->addCrumb('reviews', array('label' => __('Product Reviews')));
            }

            $this->renderLayout();
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }

    /**
     * Show details of one review
     *
     */
    public function viewAction()
    {
        $review = $this->_loadReview((int) $this->getRequest()->getParam('id'));
        if (!$review) {
            $this->_forward('noroute');
            return;
        }

        $product = $this->_loadProduct($review->getEntityPkValue());
        if (!$product) {
            $this->_forward('noroute');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Review\Model\Session');
        $this->_initLayoutMessages('Magento\Catalog\Model\Session');
        $this->renderLayout();
    }

    /**
     * Load specific layout handles by product type id
     *
     */
    protected function _initProductLayout($product)
    {
        $update = $this->getLayout()->getUpdate();
        $this->addPageLayoutHandles(
            array('id' => $product->getId(), 'sku' => $product->getSku(), 'type' => $product->getTypeId())
        );

        if ($product->getPageLayout()) {
            $this->_objectManager->get('Magento\Page\Helper\Layout')
                ->applyHandle($product->getPageLayout());
        }
        $this->loadLayoutUpdates();

        if ($product->getPageLayout()) {
            $this->_objectManager->get('Magento\Page\Helper\Layout')
                ->applyTemplate($product->getPageLayout());
        }
        $update->addUpdate($product->getCustomLayoutUpdate());
        $this->generateLayoutXml()->generateLayoutBlocks();
    }
}
