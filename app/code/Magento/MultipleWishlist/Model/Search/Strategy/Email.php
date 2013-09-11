<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist search by email strategy
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model\Search\Strategy;

class Email implements \Magento\MultipleWishlist\Model\Search\Strategy\StrategyInterface
{
    /**
     * Email provided for search
     *
     * @var string
     */
    protected $_email;

    /**
     * Set search fields required by search strategy
     *
     * @param array $params
     */
    public function setSearchParams(array $params)
    {
        if (empty($params['email']) || !Zend_Validate::is($params['email'], 'EmailAddress')) {
            throw new \InvalidArgumentException(
                __('Please input a valid email address.')
            );
        }
        $this->_email = $params['email'];
    }

    /**
     * Filter given wishlist collection
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $collection
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function filterCollection(\Magento\Wishlist\Model\Resource\Wishlist\Collection $collection)
    {
        $customer = \Mage::getModel('\Magento\Customer\Model\Customer')
            ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($this->_email);

        $collection->filterByCustomer($customer);
        foreach ($collection as $item){
            $item->setCustomer($customer);
        }
        return $collection;
    }
}
