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
     * Dependency Field model
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Dependency_FieldFactory
     */
    protected $_fieldFactory;

    /**
     * Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_application;

    /**
     * @param Mage_Core_Model_App $application
     * @param Mage_Backend_Model_Config_Structure_SearchInterface $fieldLocator
     * @param Mage_Backend_Model_Config_Structure_Element_Dependency_FieldFactory $dependencyFieldFactory
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Backend_Model_Config_Structure_SearchInterface $fieldLocator,
        Mage_Backend_Model_Config_Structure_Element_Dependency_FieldFactory $dependencyFieldFactory
    ) {

        $this->_fieldLocator = $fieldLocator;
        $this->_application = $application;
        $this->_fieldFactory = $dependencyFieldFactory;
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
            $field = $this->_fieldFactory->create(array('fieldData' => $depend, 'fieldPrefix' => $fieldPrefix));
            $shouldAddDependency = true;
            /** @var Mage_Backend_Model_Config_Structure_Element_Field $dependentField  */
            $dependentField = $this->_fieldLocator->getElement($depend['id']);
            /*
            * If dependent field can't be shown in current scope and real dependent config value
            * is not equal to preferred one, then hide dependence fields by adding dependence
            * based on not shown field (not rendered field)
            */
            if (false == $dependentField->isVisible()) {
                $valueInStore = $this->_application
                    ->getStore($storeCode)
                    ->getConfig($dependentField->getPath($fieldPrefix));
                $shouldAddDependency = !$field->isValueSatisfy($valueInStore);
            }
            if ($shouldAddDependency) {
                $output[$field->getId()] = $field;
            }
        }
        return $output;
    }
}
