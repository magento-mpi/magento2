<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CustomerSegment
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
class Enterprise_Mage_CustomerSegment_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Action_helper method for Create Segment
     *
     * Preconditions: 'Manage Segment' page is opened.
     *
     * @param array $segmentData Array which contains DataSet for filling of the current form
     */
    public function createSegment($segmentData)
    {
        $this->clickButton('add_new_segment');
        $this->fillTabs($segmentData, 'general properties');
        $this->saveForm('save_segment');
    }

    /**
     * Filling tabs
     *
     * @param string|array $segmentData
     */
    public function fillTabs($segmentData)
    {
        $segmentData = $this->fixtureDataToArray($segmentData);
        $generalTab = (isset($segmentData['general_properties']))
            ? $segmentData['general_properties']
            : array();
        if (isset($generalTab['assigned_to_website'])
            && !$this->controlIsVisible('multiselect', 'assigned_to_website')
        ) {
            unset($generalTab['assigned_to_website']);
        }
        $this->fillTab($generalTab, 'general_properties');
    }

    /**
     * Open Customer Segment.
     *
     * Preconditions: 'Customer Segment' page is opened.
     *
     * @param array $searchData
     */
    public function openSegment($searchData)
    {
        //Search Segment
        $searchData = $this->_prepareDataForSearch($searchData);
        $segmentLocator = $this->search($searchData, 'customer_segment_grid');
        $this->assertNotNull($segmentLocator, 'Segment is not found with data: ' . print_r($searchData, true));
        $segmentRowElement = $this->getElement($segmentLocator);
        $segmentUrl = $segmentRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Segment');
        $cellElement = $this->getChildElement($segmentRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($segmentUrl));
        //Open Segment
        $this->url($segmentUrl);
        $this->validatePage();
    }

    /**
     * Open Segment and delete
     *
     * @param array $segmentSearch
     */
    public function deleteSegment(array $segmentSearch)
    {
        $this->openSegment($segmentSearch);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
    }
}
