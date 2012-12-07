<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tab UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Uimap_Tab extends Mage_Selenium_Uimap_Abstract
{
    /**
     * Tab ID
     *
     * @var string
     */
    protected $_tabId = '';

    /**
     * Construct a Uimap_Tab
     *
     * @param string $tabId Tab's ID
     * @param array $tabContainer Array of data that contains the specific tab
     */
    public function  __construct($tabId, array &$tabContainer)
    {
        $this->_tabId = $tabId;
        $this->_xPath = isset($tabContainer['xpath'])
                            ? $tabContainer['xpath']
                            : '';

        $this->_parseContainerArray($tabContainer);
    }

    /**
     * Get page ID
     *
     * @return string
     */
    public function getTabId()
    {
        return $this->_tabId;
    }

    /**
     * Get Fieldset structure by ID
     *
     * @param string $id Fieldset ID
     *
     * @return Mage_Selenium_Uimap_Fieldset|null
     */
    public function getFieldset($id)
    {
        return isset($this->_elements['fieldsets'])
                ? $this->_elements['fieldsets']->getFieldset($id)
                : null;
    }
}