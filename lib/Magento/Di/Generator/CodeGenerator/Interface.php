<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Di_Generator_CodeGenerator_Interface
{
    /**
     * Generates and returns class source code
     *
     * @return string
     */
    public function generate();

    /**
     * @param string $name
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setName($name);

    /**
     * @param array|string $docblock
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setDocblock($docblock);

    /**
     * @param array $properties
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setProperties(array $properties);

    /**
     * @param array $methods
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setMethods(array $methods);

    /**
     * @param string $extendedClass
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setExtendedClass($extendedClass);
}
