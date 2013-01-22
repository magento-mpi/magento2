<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Generator_Factory extends Magento_Di_Generator_EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'factory';

    /**
     * Generic object manager factory interface
     */
    const FACTORY_INTERFACE = '\Magento_ObjectManager_Factory';

    /**
     * @return string
     */
    protected function _generateCode()
    {
        $this->_classGenerator->setImplementedInterfaces(array(self::FACTORY_INTERFACE));

        return parent::_generateCode();
    }

    /**
     * @return array
     */
    protected function _getClassMethods()
    {
        $construct = $this->_getDefaultConstructorDefinition();

        // public function createFromArray(array $data = array())
        $createFromArray = array(
            'name'       => 'createFromArray',
            'parameters' => array(
                array('name' => 'data', 'type' => 'array', 'defaultValue' => array()),
            ),
            'body' => 'return $this->_objectManager->create(self::CLASS_NAME, $data, false);',
            'docblock' => array(
                'shortDescription' => 'Create class instance with specified parameters',
                'tags'             => array(
                    array(
                        'name'        => 'param',
                        'description' => 'array $data'
                    ),
                    array(
                        'name'        => 'return',
                        'description' => $this->_getFullyQualifiedClassName($this->_getSourceClassName())
                    ),
                ),
            ),
        );

        return array($construct, $createFromArray);
    }
}
