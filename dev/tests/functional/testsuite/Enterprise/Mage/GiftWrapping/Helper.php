<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftWrapping
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
class Enterprise_Mage_GiftWrapping_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create Gift Wrapping
     *
     * @param array|string $inputData
     * @param bool
     */
    public function createGiftWrapping($inputData, $save = true)
    {
        $inputData = $this->fixtureDataToArray($inputData);
        $this->clickButton('add_gift_wrapping');
        $this->fillGiftWrappingForm($inputData, $save);
    }

    /**
     * Fill gift wrapping info
     *
     * @param array $inputData
     * @param bool $save
     */
    public function fillGiftWrappingForm($inputData, $save = true)
    {
        $title = (isset($inputData['gift_wrapping_design'])) ? $inputData['gift_wrapping_design'] : '';
        $this->addParameter('elementTitle', $title);
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
            $xpathArray = array($this->_getMessageXpath('general_error'), $this->_getMessageXpath('general_validation'),
                                $this->_getControlXpath('checkbox', 'delete_image'));
            $this->clickButton('upload_file', false);
            $this->waitForElement($xpathArray);
        }
        if ($save) {
            $this->saveForm('save');
        }
    }

    /**
     * Open Gift Wrapping
     *
     * @param array|string $searchData
     */
    public function openGiftWrapping($searchData)
    {
        $searchData = $this->fixtureDataToArray($searchData);
        if (isset($searchData['filter_websites']) && !$this->controlIsVisible('dropdown', 'filter_websites')) {
            unset($searchData['filter_websites']);
        }
        //Search Gift Wrapping
        $searchData = $this->_prepareDataForSearch($searchData);
        $wrappingLocator = $this->search($searchData, 'gift_wrapping_grid');
        $this->assertNotNull($wrappingLocator, 'Gift Wrapping is not found with data: ' . print_r($searchData, true));
        $wrappingRowElement = $this->getElement($wrappingLocator);
        $wrappingUrl = $wrappingRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Gift Wrap Design');
        $cellElement = $this->getChildElement($wrappingRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($wrappingUrl));
        //Open Gift Wrapping
        $this->url($wrappingUrl);
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
            $this->clickButton('delete', false);
            $this->dismissAlert();
        }
    }

    public function verifyGiftWrapping(array $verifyData)
    {
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
            if (!$this->controlIsPresent('pageelement', 'gift_wrapping_image')) {
                $this->addVerificationMessage('Image is not uploaded.');
            } else {
                $actualImageTitle = $this->getControlAttribute('pageelement', 'gift_wrapping_image', 'title');
                $expectedImageTitle = implode('(_(\d)+)?\.', explode('.', $image));
                if (!preg_match("/$expectedImageTitle/", $actualImageTitle)) {
                    $this->addVerificationMessage(
                        'Image title does not match with specified: (Expected: ' . $image . ' . Actual: '
                        . $actualImageTitle . ')');
                }
            }
        }
        $this->assertEmptyVerificationErrors();

    }

    public function disableAllGiftWrapping()
    {
        $xpathTR = $this->search(array('filter_status' => 'Enabled'), 'gift_wrapping_grid');
        if (is_null($xpathTR)) {
            return;
        }
        $columnId = $this->getColumnIdByName('Gift Wrap Design');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $columnId);
        while ($this->controlIsPresent('pageelement', 'table_line_cell_index')) {
            $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
            $this->addParameter('elementTitle', $param);
            $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
            $this->clickControl('pageelement', 'table_line_cell_index');
            $this->fillDropdown('gift_wrapping_status', 'Disabled');
            $this->saveForm('save');
        }
    }
}
