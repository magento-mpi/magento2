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
    protected function _construct()
    {
        $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');

        parent::_construct();

        $data = \Mage::getSingleton('Magento_Review_Model_Session')->getFormData(true);
        $data = new \Magento\Object((array)$data);

        // add logged in customer name as nickname
        if (!$data->getNickname()) {
            $customer = $customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setNickname($customer->getFirstname());
            }
        }

        $this->setAllowWriteReviewFlag(
            $customerSession->isLoggedIn() || \Mage::helper('Magento\Review\Helper\Data')->getIsGuestAllowToWrite()
        );
        if (!$this->getAllowWriteReviewFlag()) {
            $queryParam = \Mage::helper('Magento\Core\Helper\Data')->urlEncode(
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
            ->assign('messages', \Mage::getSingleton('Magento_Review_Model_Session')->getMessages(true));
    }

    public function getProductInfo()
    {
        $product = \Mage::getModel('\Magento\Catalog\Model\Product');
        return $product->load($this->getRequest()->getParam('id'));
    }

    public function getAction()
    {
        $productId = \Mage::app()->getRequest()->getParam('id', false);
        return \Mage::getUrl('review/product/post', array('id' => $productId));
    }

    public function getRatings()
    {
        $ratingCollection = \Mage::getModel('\Magento\Rating\Model\Rating')
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
