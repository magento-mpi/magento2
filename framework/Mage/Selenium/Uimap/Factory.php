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
 * UIMap factory class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Factory
{
    /**
     * Array of allowed element names
     *
     * @var array
     */
    protected static $_allowedElementNames = array('buttons', 'messages', 'links', 'fields', 'dropdowns', 'multiselects',
                                                  'checkboxes', 'radiobuttons', 'required', 'pageelements');

    /**
     * Construct an Uimap_Factory
     */
    protected function __construct()
    {
    }

    /**
     * Creates a UIMap object
     *
     * @param string $elemKey
     * @param string|array $elemValue
     *
     * @return mixed
     */
    public static function createUimapElement($elemKey, &$elemValue)
    {
        $elements = null;

        switch ($elemKey) {
            case 'form':
                $elements = new Mage_Selenium_Uimap_Form($elemValue);
                break;
            case 'tabs':
                $elements = new Mage_Selenium_Uimap_TabsCollection();
                foreach ($elemValue as &$tabArrayValue) {
                    foreach ($tabArrayValue as $tabKey => &$tabValue) {
                        $elements[$tabKey] = new Mage_Selenium_Uimap_Tab($tabKey, $tabValue);
                    }
                }
                break;
            case 'fieldsets':
                $elements = new Mage_Selenium_Uimap_FieldsetsCollection();
                foreach ($elemValue as &$fieldsetArrayValue) {
                    foreach ($fieldsetArrayValue as $fieldsetKey => &$fieldsetValue) {
                        $elements[$fieldsetKey] =
                            new Mage_Selenium_Uimap_Fieldset($fieldsetKey, $fieldsetValue);
                    }
                }
                break;
            default:
                if (in_array($elemKey, self::$_allowedElementNames)) {
                    $elements = new Mage_Selenium_Uimap_ElementsCollection($elemKey,
                                    $elemValue);
                }
        }
        return $elements;
    }
}
