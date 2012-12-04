<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Dependency_Mapper
{
    /**
     * Field locator model
     *
     * @var Mage_Backend_Model_Config_Structure_SearchInterface
     */
    protected $_fieldLocator;


    /**
     * Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_application;

    /**
     * @param Mage_Core_Model_App $application
     * @param Mage_Backend_Model_Config_Structure_SearchInterface $fieldLocator
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Backend_Model_Config_Structure_SearchInterface $fieldLocator
    ) {

        $this->_fieldLocator = $fieldLocator;
        $this->_application = $application;
    }

    /**
     * Retrieve field dependencies
     *
     * @param array $dependencies
     * @param string $storeCode
     * @param string $fieldPrefix
     * @return array
     */
    public function getDependencies($dependencies, $storeCode, $fieldPrefix = '')
    {
        $output = array();

        foreach ($dependencies as $depend) {
            /* @var array $depend */
            $fieldId = $fieldPrefix . array_pop($depend['dependPath']);
            $depend['dependPath'][] = $fieldId;
            $dependentId = implode('_', $depend['dependPath']);

            $shouldBeAddedDependence = true;

            $dependentValue = $depend['value'];

            if (isset($depend['separator'])) {
                $dependentValue = explode($depend['separator'], $dependentValue);
            }

            /** @var Mage_Backend_Model_Config_Structure_Element_Field $dependentField  */
            $dependentField = $this->_fieldLocator->getElement($depend['id']);

            /*
            * If dependent field can't be shown in current scope and real dependent config value
            * is not equal to preferred one, then hide dependence fields by adding dependence
            * based on not shown field (not rendered field)
            */
            if (false == $dependentField->isVisible()) {
                $dependentValueInStore = $this->_application
                    ->getStore($storeCode)
                    ->getConfig($dependentField->getPath($fieldPrefix));
                if (is_array($dependentValue)) {
                    $shouldBeAddedDependence = !in_array($dependentValueInStore, $dependentValue);
                } else {
                    $shouldBeAddedDependence = $dependentValue != $dependentValueInStore;
                }
            }
            if ($shouldBeAddedDependence) {
                $output[$dependentId] = $dependentValue;
            }
        }
        return $output;
    }
}
