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
 * Form UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
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
}
