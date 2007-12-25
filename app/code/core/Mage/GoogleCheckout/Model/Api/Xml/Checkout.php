<?php

class Mage_GoogleCheckout_Model_Api_Xml_Checkout extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    protected function _getApiUrl()
    {
        $url = $this->_getBaseApiUrl();
        $url .= 'merchantCheckout/Merchant/'.Mage::getStoreConfig('google/checkout/merchant_id');
        return $url;
    }

    public function checkout()
    {
        $quote = $this->getQuote();
        if (!($quote instanceof Mage_Sales_Model_Quote)) {
            Mage::throwException('Invalid quote');
        }

        $xml = <<<EOT
<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">
    <shopping-cart>
{$this->_getItemsXml()}
{$this->_getMerchantPrivateDataXml()}
{$this->_getCartExpirationXml()}
    </shopping-cart>
    <checkout-flow-support>
{$this->_getMerchantCheckoutFlowSupportXml()}
    </checkout-flow-support>
    <order-processing-support>
{$this->_getRequestInitialAuthDetailsXml()}
    </order-processing-support>
</checkout-shopping-cart>
EOT;

        $result = $this->_call($xml);

        $this->setRedirectUrl($result->{'redirect-url'});

        return $this;
    }

    protected function _getItemsXml()
    {
        $xml = <<<EOT
        <items>

EOT;
        $currency = $this->getQuote()->getQuoteCurrencyCode();

        foreach ($this->getQuote()->getAllItems() as $item) {
            $digital = $item->getIsVirtual() ? 'true' : 'false';
            $xml .= <<<EOT
            <item>
                <merchant-item-id><![CDATA[{$item->getSku()}]]></merchant-item-id>
                <item-name><![CDATA[{$item->getName()}]]></item-name>
                <item-description><![CDATA[{$item->getDescription()}]]></item-description>
                <unit-price currency="{$currency}">{$item->getPrice()}</unit-price>
                <quantity>{$item->getQty()}</quantity>
                {$this->_getDigitalContentXml($item)}
            </item>

EOT;
        }
        $xml .= <<<EOT
        </items>
EOT;
        return $xml;
    }

    protected function _getDigitalContentXml($item)
    {
        $xml = <<<EOT
EOT;
        return $xml;
    }

    protected function _getMerchantPrivateDataXml()
    {
        $xml = <<<EOT
EOT;
        return $xml;
    }

    protected function _getCartExpirationXml()
    {
        $xml = <<<EOT
EOT;
        return $xml;
    }

    protected function _getMerchantCheckoutFlowSupportXml()
    {
        $xml = <<<EOT
        <merchant-checkout-flow-support>
            <edit-cart-url><![CDATA[{$this->_getEditCartUrl()}]]></edit-cart-url>
            <continue-shopping-url><![CDATA[{$this->_getContinueShoppingUrl()}]]></continue-shopping-url>
            {$this->_getRequestBuyerPhoneNumberXml()}
            {$this->_getMerchantCalculationsXml()}
            {$this->_getShippingMethodsXml()}
            {$this->_getTaxTablesXml()}
            {$this->_getParameterizedUrlsXml()}
            {$this->_getPlatformIdXml()}
            {$this->_getAnalyticsDataXml()}
        </merchant-checkout-flow-support>
EOT;
        return $xml;
    }

    protected function _getRequestBuyerPhoneNumberXml()
    {
        $requestPhone = Mage::getStoreConfig('google/checkout/request_phone') ? 'true' : 'false';
        $xml = <<<EOT
            <request-buyer-phone-number>{$requestPhone}</request-buyer-phone-number>
EOT;
        return $xml;
    }

    protected function _getMerchantCalculationsXml()
    {
        $xml = <<<EOT
            <merchant-calculations>
                <merchant-calculations-url><![CDATA[{$this->_getCalculationsUrl()}]]></merchant-calculations-url>
            </merchant-calculations>
EOT;
        return $xml;
    }

    protected function _getShippingMethodsXml()
    {
        $xml = <<<EOT
            <shipping-methods>
                <flat-rate-shipping name="SuperShip Ground">
                    <price currency="USD">9.99</price>
                </flat-rate-shipping>
            </shipping-methods>
EOT;
        return $xml;
    }

    protected function _getTaxTablesXml()
    {
        $xml = <<<EOT
EOT;
        return $xml;
    }

    protected function _getRequestInitialAuthDetailsXml()
    {
        $xml = <<<EOT
        <request-initial-auth-details>true</request-initial-auth-details>
EOT;
        return $xml;
    }

    protected function _getParameterizedUrlsXml()
    {
        $xml = <<<EOT
            <parameterized-urls>
                <parameterized-url url="{$this->_getParameterizedUrl()}" />
            </parameterized-urls>
EOT;
        return $xml;
    }

    protected function _getPlatformIdXml()
    {
        $xml = <<<EOT
            <platform-id>1234567890</platform-id>
EOT;
        return ''; // need to get an ID from google
    }

    protected function _getAnalyticsDataXml()
    {
        $xml = <<<EOT

EOT;
        return $xml;
    }
}