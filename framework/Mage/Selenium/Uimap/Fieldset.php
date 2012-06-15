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
 * Fieldset UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Fieldset extends Mage_Selenium_Uimap_Abstract
{
    /**
     * @var string
     */
    protected $_fieldsetId = '';

    /**
     * Construct a Uimap_Fieldset
     *
     * @param string $fieldsetId Fieldset ID
     * @param array $fieldsetContainer Array of data, which contains in specific fieldset
     */
    public function  __construct($fieldsetId, array &$fieldsetContainer)
    {
        $this->_fieldsetId = $fieldsetId;
        $this->_xPath = isset($fieldsetContainer['xpath']) ? $fieldsetContainer['xpath'] : '';
        $this->_parseContainerArray($fieldsetContainer);
        if ($this->_xPath != '' && isset($this->_elements)) {
            $parent = $this->_xPath;
            foreach ($this->_elements as $elementsType => $elementData) {
                if ($elementsType == 'required') {
                    continue;
                }
                foreach ($elementData as $elementName => $elementXpath) {
                    if (preg_match('|^' . preg_quote($parent) . '|', $elementXpath)) {
                        continue;
                    }
                    $elementXpath = str_ireplace('css=', ' ', $elementXpath);
                    $elementData[$elementName] = $parent . $elementXpath;
                }
            }
        }
    }

    /**
     * Get Fieldset ID
     * @return string
     */
    public function getFieldsetId()
    {
        return $this->_fieldsetId;
    }

    /**
     * Get Fieldset elements
     * @return array
     */
    public function getFieldsetElements()
    {
        $elementsArray = array();
        foreach ($this->_elements as $elementType => $elementData) {
            foreach ($elementData as $elementName => $elementValue) {
                $type = preg_replace('/(e)?s$/', '', $elementType);
                $elementsArray[$type][$elementName] = $elementValue;
            }
        }
        return $elementsArray;
    }
}
