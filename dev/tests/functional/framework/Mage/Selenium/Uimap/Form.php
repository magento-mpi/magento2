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
 * Form UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Form extends Mage_Selenium_Uimap_Abstract
{
    /**
     * Construct an Uimap_Form
     *
     * @param array $formContainer Array of data, which contains in the form
     */
    public function  __construct(array &$formContainer)
    {
        $this->_parseContainerArray($formContainer);
    }

    /**
     * Get tab defined on the current form
     *
     * @param string $id Tab's Identifier
     *
     * @return Mage_Selenium_Uimap_Tab|null
     */
    public function getTab($id)
    {
        return isset($this->_elements['tabs'])
                ? $this->_elements['tabs']->getTab($id)
                : null;
    }

    /**
     * Get fieldsets defined in the current form(that not included into tab)
     * @return mixed
     */
    public function getMainFormFieldsets()
    {
        if (isset($this->_elements['fieldsets'])) {
            return $this->_elements['fieldsets'];
        }
        return null;
    }
}
