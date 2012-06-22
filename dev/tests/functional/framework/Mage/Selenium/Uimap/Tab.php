<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tab UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    /**
     * Get Fieldset names in tab
     * @return array
     */
    public function getFieldsetNames()
    {
        if (!isset($this->_elements['fieldsets'])) {
            return array();
        }
        $names = array();
        foreach ($this->_elements['fieldsets'] as $fieldsetName => $content) {
            $names[] = $fieldsetName;
        }
        return $names;
    }
}