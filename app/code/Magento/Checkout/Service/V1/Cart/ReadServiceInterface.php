<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Cart;

use \Magento\Framework\Service\V1\Data\SearchCriteria;

interface ReadServiceInterface
{
    /**
     * Retrieve information about cart represented by given ID
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCart($cartId);

    /**
     * Retrieve list of carts that match given search criteria
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Checkout\Service\V1\Data\CartSearchResults
     */
    public function getCartList(SearchCriteria $searchCriteria);

    /**
     * Retrieve quote totals data
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\Totals
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTotals($cartId);
}
