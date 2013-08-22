<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist search by name and last name strategy
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Model_Search_Strategy_Name implements Enterprise_Wishlist_Model_Search_Strategy_Interface
{
    /**
     * Customer firstname provided for search
     *
     * @var string
     */
    protected $_firstname;

    /**
     * Customer lastname provided for search
     *
     * @var string
     */
    protected $_lastname;

    /**
     * Validate search params
     *
     * @param array $params
     */
    public function setSearchParams(array $params)
    {
        if (empty($params['firstname']) || strlen($params['firstname']) < 2) {
            throw new InvalidArgumentException(
                __('Please enter at least 2 letters of the first name.')
            );
        }
        $this->_firstname = $params['firstname'];
        if (empty($params['lastname']) || strlen($params['lastname']) < 2) {
            throw new InvalidArgumentException(
                __('Please enter at least 2 letters of the last name.')
            );
        }
        $this->_lastname = $params['lastname'];
    }

    /**
     * Filter wishlist collection
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection $collection
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterCollection(Magento_Wishlist_Model_Resource_Wishlist_Collection $collection)
    {
        /* @var $customers Magento_Customer_Model_Resource_Customer_Collection */
        $customers = Mage::getModel('Magento_Customer_Model_Customer')->getCollection()
            ->addAttributeToFilter(
                array(array('attribute' => 'firstname', 'like' => '%'.$this->_firstname.'%'))
            )
            ->addAttributeToFilter(
                array(array('attribute' => 'lastname', 'like' => '%'.$this->_lastname.'%'))
            );

        $collection->filterByCustomerIds($customers->getAllIds());
        foreach ($collection as $wishlist) {
            $wishlist->setCustomer($customers->getItemById($wishlist->getCustomerId()));
        }
        return $collection;
    }
}
