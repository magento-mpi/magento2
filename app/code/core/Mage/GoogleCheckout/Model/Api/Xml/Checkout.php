<?php

class Mage_GoogleCheckout_Model_Api_Xml_Checkout extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    protected $_currency;

    protected function _getApiUrl()
    {
        $url = $this->_getBaseApiUrl();
        $url .= 'merchantCheckout/Merchant/'.Mage::getStoreConfig('google/checkout/merchant_id');
        return $url;
    }

    protected function _getCurrency()
    {
        if (!$this->_currency) {
            $this->_currency = $this->getQuote()->getQuoteCurrencyCode();
        }
        return $this->_currency;
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
        $weightUnit = 'LB';
        foreach ($this->getQuote()->getAllItems() as $item) {
            $digital = $item->getIsVirtual() ? 'true' : 'false';
            $xml .= <<<EOT
            <item>
                <merchant-item-id><![CDATA[{$item->getSku()}]]></merchant-item-id>
                <item-name><![CDATA[{$item->getName()}]]></item-name>
                <item-description><![CDATA[{$item->getDescription()}]]></item-description>
                <unit-price currency="{$this->_getCurrency()}">{$item->getPrice()}</unit-price>
                <quantity>{$item->getQty()}</quantity>
                <item-weight unit="{$weightUnit}" value="{$item->getWeight()}" />
                <tax-table-selector>{$item->getTaxClassId()}</tax-table-selector>
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
                {$this->_getCarrierCalculatedShippingXml()}
                {$this->_getFlatRateShippingXml()}
                {$this->_getMerchantCalculatedShippingXml()}
                {$this->_getPickupXml()}
            </shipping-methods>
EOT;
        return $xml;
    }

    protected function _getCarrierCalculatedShippingXml()
    {
        $active = Mage::getStoreConfig('google/checkout_shipping_carrier/active');
        $methods = Mage::getStoreConfig('google/checkout_shipping_carrier/methods');
        if (!$active || !$methods) {
            return '';
        }

        $country = Mage::getStoreConfig('shipping/origin/country_id');
        $region = Mage::getStoreConfig('shipping/origin/region_id');
        $postcode = Mage::getStoreConfig('shipping/origin/postcode');
        $city = Mage::getStoreConfig('shipping/origin/city');

        $sizeUnit = 'IN';#Mage::getStoreConfig('google/checkout_shipping_carrier/default_unit');
        $width = Mage::getStoreConfig('google/checkout_shipping_carrier/default_width');
        $height = Mage::getStoreConfig('google/checkout_shipping_carrier/default_height');
        $length = Mage::getStoreConfig('google/checkout_shipping_carrier/default_length');

        $addressCategory = Mage::getStoreConfig('google/checkout_shipping_carrier/address_category');

        $xml = <<<EOT
                <carrier-calculated-shipping>
                    <shipping-packages>
                        <shipping-package>
                            <ship-from id="Test">
                                <city>{$city}</city>
                                <region>{$region}</region>
                                <postal-code>{$postcode}</postal-code>
                                <country-code>{$country}</country-code>
                            </ship-from>
                            <width unit="{$sizeUnit}" value="{$width}"/>
                            <height unit="{$sizeUnit}" value="{$height}"/>
                            <length unit="{$sizeUnit}" value="{$length}"/>
                            <delivery-address-category>{$addressCategory}</delivery-address-category>
                        </shipping-package>
                    </shipping-packages>
                    <carrier-calculated-shipping-options>
EOT;

        foreach (explode(',', $methods) as $method) {
            list($company, $type) = explode('/', $method);
            $xml .= <<<EOT
                        <carrier-calculated-shipping-option>
                            <shipping-company>{$company}</shipping-company>
                            <shipping-type>{$type}</shipping-type>
                            <price currency="{$this->_getCurrency()}">11.99</price>
                        </carrier-calculated-shipping-option>
EOT;
        }

        $xml .= <<<EOT
                    </carrier-calculated-shipping-options>
                </carrier-calculated-shipping>
EOT;
        return $xml;
    }

    protected function _getFlatRateShippingXml()
    {
        if (!Mage::getStoreConfig('google/checkout_shipping_flatrate/active')) {
            return '';
        }

        for ($xml='', $i=1; $i<3; $i++) {
            $title = Mage::getStoreConfig('google/checkout_shipping_flatrate/title_'.$i);
            $price = Mage::getStoreConfig('google/checkout_shipping_flatrate/price_'.$i);

            if (empty($title) || empty($price) && '0'!==$price) {
                continue;
            }

            $xml .= <<<EOT
                <flat-rate-shipping name="{$title}">
                    <price currency="{$this->_getCurrency()}}">{$price}</price>
                </flat-rate-shipping>
EOT;
        }

        return $xml;
    }

    protected function _getMerchantCalculatedShippingXml()
    {
        if (!Mage::getStoreConfig('google/checkout_shipping_merchant/active')) {
            return '';
        }

        $xml = <<<EOT
                <merchant-calculated-shipping name="Merchant Test">
                    <price currency="{$this->_getCurrency()}">10.99</price>
                </merchant-calculated-shipping>
EOT;
        return $xml;
    }

    protected function _getPickupXml()
    {
        if (!Mage::getStoreConfig('google/checkout_shipping_pickup/active')) {
            return '';
        }

        $title = Mage::getStoreConfig('google/checkout_shipping_pickup/title');
        $price = Mage::getStoreConfig('google/checkout_shipping_pickup/price');

        $xml = <<<EOT
                <pickup name="{$title}">
                    <price currency="{$this->_getCurrency()}">{$price}</price>
                </pickup>
EOT;
        return $xml;
    }

    protected function _getTaxTablesXml()
    {
        $xml = <<<EOT
            <tax-tables>
                <default-tax-table>
                    <tax-rules>
                        <default-tax-rule>
                            <rate>0</rate>
                        </default-tax-rule>
                    </tax-rules>
                </default-tax-table>
                <alternate-tax-tables>
EOT;
        foreach ($this->_getTaxRules() as $group=>$taxRates) {
            $xml .= <<<EOT
                    <alternate-tax-table name="{$group}" standalone="false">
                        <alternate-tax-rules>
EOT;
            foreach ($taxRates as $rate) {
                $xml .= <<<EOT
                            <alternate-tax-rule>
                                <tax-area>
EOT;
                if (!empty($rate['postcode'])) {
                    $xml .= <<<EOT
                                    <postal-area>
                                        <country-code>{$rate['country']}</country-code>
                                        <postal-code-pattern>{$rate['postcode']}</postal-code-pattern>
                                    </postal-area>
EOT;
                } else {
                    $xml .= <<<EOT
                                    <us-state-area>
                                        <state>{$rate['postcode']}</state>
                                    </us-state-area>
EOT;
                }
                $xml .= <<<EOT
                                </tax-area>
                                <rate>{$rate['value']}</rate>
                            </alternate-tax-rule>
EOT;
            }
            $xml .= <<<EOT
                        </alternate-tax-rules>
                    </alternate-tax-table>
EOT;
        }

        $xml .= <<<EOT
                </alternate-tax-tables>
            </tax-tables>
EOT;
        return $xml;
    }

    protected function _getTaxRules()
    {
        $rules = array(
            'Regular' => array(
                array('state'=>'CA', 'value'=>8.25),
                array('postcode'=>'90034', 'value'=>18.25),
            ),
        );
        return $rules;
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
        $analytics = 'SW5zZXJ0IDxhbmFseXRpY3MtZGF0YT4gdmFsdWUgaGVyZS4=';
        $xml = <<<EOT
            <analytics-data><![CDATA[{$analytics}]]></analytics-data>
EOT;
        return $xml;
    }
}