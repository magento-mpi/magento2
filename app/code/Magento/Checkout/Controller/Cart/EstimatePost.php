<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller\Cart;

class EstimatePost extends \Magento\Checkout\Controller\Cart
{
    /**
     * Initialize shipping information
     *
     * @return void
     */
    public function execute()
    {
        $country = (string)$this->getRequest()->getParam('country_id');
        $postcode = (string)$this->getRequest()->getParam('estimate_postcode');
        $city = (string)$this->getRequest()->getParam('estimate_city');
        $regionId = (string)$this->getRequest()->getParam('region_id');
        $region = (string)$this->getRequest()->getParam('region');

        $this->cart->getQuote()->getShippingAddress()->setCountryId(
            $country
        )->setCity(
            $city
        )->setPostcode(
            $postcode
        )->setRegionId(
            $regionId
        )->setRegion(
            $region
        )->setCollectShippingRates(
            true
        );
        $this->cart->getQuote()->save();
        $this->_goBack();
    }
}
