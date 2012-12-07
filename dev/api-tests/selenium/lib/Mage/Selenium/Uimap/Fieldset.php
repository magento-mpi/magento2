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
 * Fieldset UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
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
        $this->_xPath = isset($fieldsetContainer['xpath'])
                            ? $fieldsetContainer['xpath'] : '';
        $this->_parseContainerArray($fieldsetContainer);
    }
}
