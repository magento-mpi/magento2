<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Structure\Element\Dependency;

class Mapper
{
    /**
     * Field locator model
     *
     * @var \Magento\Backend\Model\Config\Structure\SearchInterface
     */
    protected $_fieldLocator;

    /**
     * Dependency Field model
     *
     * @var \Magento\Backend\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    protected $_fieldFactory;

    /**
     * Application object
     *
     * @var \Magento\Core\Model\App
     */
    protected $_application;

    /**
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Backend\Model\Config\Structure\SearchInterface $fieldLocator
     * @param \Magento\Backend\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory
     */
    public function __construct(
        \Magento\Core\Model\App $application,
        \Magento\Backend\Model\Config\Structure\SearchInterface $fieldLocator,
        \Magento\Backend\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory
    ) {

        $this->_fieldLocator = $fieldLocator;
        $this->_application = $application;
        $this->_fieldFactory = $fieldFactory;
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
            /** @var \Magento\Backend\Model\Config\Structure\Element\Field $dependentField  */
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
