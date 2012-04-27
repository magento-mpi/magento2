<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@ magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_Helper extends Mage_Selenium_TestCase
{
    /**
     * Create Gift Wrapping
     *
     * @param array|string $inputData
     * @param bool
     */
    public function createGiftWrapping($inputData, $save = true)
    {
        if (is_string($inputData)) {
            $inputData = $this->loadData($inputData);
        }
        $inputData = $this->arrayEmptyClear($inputData);

        $this->clickButton('add_gift_wrapping');
        $this->fillGiftWrappingForm($inputData, $save);

    }

    /**
     * Fill gift wrapping info
     *
     * @param $inputData
     * @param bool $save
     * @return bool
     */
    public function fillGiftWrappingForm($inputData, $save = true)
    {
        if (isset($inputData['gift_wrapping_websites'])
            && !$this->controlIsPresent('multiselect', 'gift_wrapping_websites')
        ) {
            unset($inputData['gift_wrapping_websites']);
        }
        if (isset($inputData['gift_wrapping_file'])) {
            //@TODO uploading file
            unset($inputData['gift_wrapping_file']);
        }
        $this->clickControl('field', 'gift_wrapping_file', false);
        $this->fillForm($inputData);
        if (isset($inputData['gift_wrapping_file'])) {
            $this->clickButton('upload_file', false);
            $this->waitForElement(array(self::$xpathErrorMessage, self::$xpathValidationMessage,
                                       $this->_getControlXpath('checkbox', 'delete_image')));
        }
        if ($save) {
            $this->saveForm('save');
        } else {
            return false;
        }
    }

    /**
     * Open Gift Wrapping
     *
     * @param array|string $wrappingSearch
     */
    public function openGiftWrapping($wrappingSearch)
    {
        if (is_string($wrappingSearch)) {
            $wrappingSearch = $this->loadData($wrappingSearch);
        }
        $wrappingSearch = $this->arrayEmptyClear($wrappingSearch);
        if (array_key_exists('filter_websites', $wrappingSearch)
            && !$this->controlIsPresent('dropdown', 'filter_websites')
        ) {
            unset($wrappingSearch['filter_websites']);
        }
        $xpathTR = $this->search($wrappingSearch, 'gift_wrapping_grid');
        $this->assertNotNull($xpathTR, 'Gift Wrapping is not found');
        $cellId = $this->getColumnIdByName('Gift Wrapping Design');
        $this->addParameter('elementTitle', $this->getText($xpathTR . '//td[' . $cellId . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . "//a[text()='Edit']");
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage('edit_gift_wrapping');
    }

    /**
     * Open and delete Gift Wrapping
     *
     * @param array $wrappingSearch
     * @param bool $cancelDelete
     */
    public function deleteGiftWrapping(array $wrappingSearch, $cancelDelete = false)
    {
        $this->openGiftWrapping($wrappingSearch);
        if ($cancelDelete == false) {
            $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        } else {
            $this->chooseCancelOnNextConfirmation();
            $this->clickButton('delete', false);
            $this->getConfirmation();
        }
    }

    public function verifyGiftWrapping(array $verifyData)
    {
        $verifyData = $this->arrayEmptyClear($verifyData);
        //Get Gift Wrapping image name.
        $image = '';
        if (isset($verifyData['gift_wrapping_file'])) {
            $image = $verifyData['gift_wrapping_file'];
            unset($verifyData['gift_wrapping_file']);
        }
        //Verify Gift Wrapping data(except Image field).
        if (isset($verifyData['gift_wrapping_websites'])
            && !$this->controlIsPresent('multiselect', 'gift_wrapping_websites')
        ) {
            $this->verifyForm($verifyData, null, array('gift_wrapping_websites'));
        } else {
            $this->verifyForm($verifyData);
        }
        //Verify image properties.
        if ($image) {
            if (!$this->controlIsPresent('checkbox', 'delete_image')) {
                $this->addVerificationMessage('Checkbox  \'Delete Image\' is not on page.');
            }
            $imageXpath = $this->_getControlXpath('pageelement', 'gift_wrapping_image');
            if (!$this->isElementPresent($imageXpath)) {
                $this->addVerificationMessage('Image is not uploaded.');
            } else {
                $actualImageTitle = $this->getAttribute($imageXpath . '@title');
                $expectedImageTitle = implode('(_(\d)+)?\.', explode('.', $image));
                if (!preg_match("/$expectedImageTitle/", $actualImageTitle)) {
                    $this->addVerificationMessage('Image title does not match with specified: (Expected: '
                                                      . $image . ' . Actual: ' . $actualImageTitle . ')');
                }
            }
        }
        $this->assertEmptyVerificationErrors();

    }

    public function disableAllGiftWrapping()
    {
        $xpathTR = $this->search(array('filter_status' => 'Enabled'));
        $id = $this->getColumnIdByName('Gift Wrapping Design');
        while ($this->isElementPresent($xpathTR)) {
            $this->addParameter('elementTitle', $this->getText($xpathTR . "//td[$id]"));
            $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
            $this->click($xpathTR . "//a[text()='Edit']");
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->validatePage('edit_gift_wrapping');
            $this->fillForm(array('gift_wrapping_status' => 'Disabled'));
            $this->saveForm('save');
        }
    }
}
