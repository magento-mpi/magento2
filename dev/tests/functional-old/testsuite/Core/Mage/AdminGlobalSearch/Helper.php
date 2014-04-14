<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class for admin global search
 */
class Core_Mage_AdminGlobalSearch_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Search data via global search in admin and click the found link
     *
     * @param string $search
     * @param string $recordName
     */
    public function searchAndOpen($search, $recordName)
    {
        $this->clickButton('global_search');
        $this->waitForControlVisible(self::FIELD_TYPE_INPUT, 'global_record_search');
        $this->getControlElement(self::FIELD_TYPE_INPUT, 'global_record_search')->value($search);
        $this->waitForControlVisible(self::FIELD_TYPE_INPUT, 'global_record_search');
        $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'search_global_list');
        $this->addParameter('recordName', $recordName);
        if (!$this->controlIsVisible(self::FIELD_TYPE_LINK, 'record_item')) {
            $this->fail('Record was not found.');
        }
        $this->clickControl(self::FIELD_TYPE_LINK, 'record_item');
    }
}