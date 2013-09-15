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
namespace Magento\Review\Block;

class Form extends \Magento\Core\Block\Template
{
    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_reviewData = $reviewData;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');

        parent::_construct();

        $data = \Mage::getSingleton('Magento\Review\Model\Session')->getFormData(true);
        $data = new \Magento\Object((array)$data);

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
                \Mage::getUrl('*/*/*', array('_current' => true)) .
                '#review-form'
            );
            $this->setLoginLink(\Mage::getUrl(
                    'customer/account/login/',
                    array(\Magento\Customer\Helper\Data::REFERER_QUERY_PARAM_NAME => $queryParam)
                )
            );
        }

        $this->setTemplate('form.phtml')
            ->assign('data', $data)
            ->assign('messages', \Mage::getSingleton('Magento\Review\Model\Session')->getMessages(true));
    }

    public function getProductInfo()
    {
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        return $product->load($this->getRequest()->getParam('id'));
    }

    public function getAction()
    {
        $productId = \Mage::app()->getRequest()->getParam('id', false);
        return \Mage::getUrl('review/product/post', array('id' => $productId));
    }

    public function getRatings()
    {
        $ratingCollection = \Mage::getModel('Magento\Rating\Model\Rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->addRatingPerStoreName(\Mage::app()->getStore()->getId())
            ->setStoreFilter(\Mage::app()->getStore()->getId())
            ->setActiveFilter(true)
            ->load()
            ->addOptionToItems();
        return $ratingCollection;
    }
}
