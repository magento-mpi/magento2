<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    public $productTabs = array('general', 'prices', 'meta_information', 'images', 'recurring_profile', 'design',
                                'gift_options', 'inventory', 'websites', 'related', 'up_sells', 'cross_sells',
                                'custom_options', 'bundle_items', 'associated', 'downloadable_information',
                                'giftcardinfo');

    #**************************************************************************************
    #*                                                    Frontend Helper Methods         *
    #**************************************************************************************
    /**
     * Choose custom options and additional products
     *
     * @param array $dataForBuy
     */
    public function frontAddProductToCart($dataForBuy = null)
    {
        $customize = $this->controlIsPresent('button', 'customize_and_add_to_cart');
        $customizeFieldset = $this->_getControlXpath('fieldset', 'customize_product_info');
        if ($customize) {
            $productInfoFieldset = $this->_getControlXpath('fieldset', 'product_info');
            $this->clickButton('customize_and_add_to_cart', false);
            $this->waitForElementVisible($customizeFieldset);
            $this->waitForElement($productInfoFieldset . "/parent::*[@style='display: none;']");
        }
        parent::frontAddProductToCart($dataForBuy);
    }

    /**
     * Verify Gift Card info on frontend
     *
     * @param array $productData
     */
    public function frontVerifyGiftCardInfo(array $productData)
    {
        $this->markTestIncomplete('@TODO - implement frontVerifyGiftCardInfo');
    }

    #**************************************************************************************
    #*                                                    Backend Helper Methods          *
    #**************************************************************************************

    /**
     * @param array $pricesTab
     */
    public function fillPricesTab(array $pricesTab)
    {
        $this->openTab('prices');
        if (isset($pricesTab['prices_gift_card_amounts'])) {
            foreach ($pricesTab['prices_gift_card_amounts'] as $value) {
                $this->addGiftCardAmount($value);
            }
            unset($pricesTab['prices_gift_card_amounts']);
        }
        parent::fillPricesTab($pricesTab);
    }

    /**
     * @param array $pricesTab
     */
    public function verifyPricesTab($pricesTab)
    {
        $this->openTab('prices');
        if (isset($pricesTab['prices_gift_card_amounts'])) {
            $this->verifyGiftCardAmounts($pricesTab['prices_gift_card_amounts']);
            unset($pricesTab['prices_gift_card_amounts']);
        }
        parent::fillPricesTab($pricesTab);
    }

    /**
     * Add Gift Card Amount
     *
     * @param array $giftCardData
     */
    public function addGiftCardAmount(array $giftCardData)
    {
        $rowNumber = $this->getControlCount('fieldset', 'prices_gift_card_amounts');
        $this->addParameter('giftCardId', $rowNumber);
        $this->clickButton('add_gift_card_amount', false);
        $this->waitForAjax();
        $this->fillTab($giftCardData, 'prices');
    }

    /**
     * Verify GiftCardAmounts
     *
     * @param array $giftCardData
     *
     * @return boolean
     */
    public function verifyGiftCardAmounts(array $giftCardData)
    {
        $rowQty = count($this->getControlElements('fieldset', 'prices_gift_card_amounts', null, false));
        $needCount = count($giftCardData);
        if ($needCount != $rowQty) {
            $this->addVerificationMessage(
                'Product must contain ' . $needCount . ' gift card amount(s), but contains ' . $rowQty);
            return false;
        }
        $index = $rowQty - 1;
        foreach ($giftCardData as $value) {
            $this->addParameter('giftCardId', $index);
            $this->verifyForm($value, 'prices');
            --$index;
        }
        $this->assertEmptyVerificationErrors();
        return true;
    }
}