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
    #**************************************************************************************
    #*                                                    Frontend Helper Methods         *
    #**************************************************************************************
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
     * Fill in data on General Tab
     *
     * @param array $generalTab
     */
    public function fillGeneralTab(array $generalTab)
    {
        parent::fillGeneralTab($generalTab);
        if (isset($generalTab['general_amounts'])) {
            foreach ($generalTab['general_amounts'] as $value) {
                $this->addGiftCardAmount($value);
            }
        }
    }

    /**
     * Verify data on General Tab
     *
     * @param array $generalTab
     */
    public function verifyGeneralTab(array $generalTab)
    {
        parent::verifyGeneralTab($generalTab);
        if (isset($generalTab['general_amounts'])) {
            $this->verifyGiftCardAmounts($generalTab['general_amounts']);
        }
    }

    /**
     * Add Gift Card Amount
     *
     * @param array $giftCardData
     */
    public function addGiftCardAmount(array $giftCardData)
    {
        $rowNumber = $this->getControlCount('pageelement', 'general_giftcard_amount_line');
        $this->addParameter('giftCardId', $rowNumber);
        $this->clickButton('add_giftcard_amount', false);
        $this->waitForAjax();
        if ($this->controlIsVisible('dropdown', 'general_giftcard_website')) {
            $this->fillDropdown('general_giftcard_website', $giftCardData['general_giftcard_website']);
        }
        $this->fillField('general_giftcard_amount', $giftCardData['general_giftcard_amount']);
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
        $rowQty = $this->getControlCount('pageelement', 'general_giftcard_amount_line');
        $needCount = count($giftCardData);
        if ($needCount != $rowQty) {
            $this->addVerificationMessage(
                'Product must contain ' . $needCount . ' gift card amount(s), but contains ' . $rowQty);
            return false;
        }
        $index = 0;
        foreach ($giftCardData as $value) {
            $this->addParameter('giftCardId', $index);
            if (!$this->controlIsVisible('dropdown', 'general_giftcard_website')) {
                unset($value['general_giftcard_website']);
            }
            $this->verifyForm($value, 'general');
            $index++;
        }
        $this->assertEmptyVerificationErrors();
        return true;
    }
}
