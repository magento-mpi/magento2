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
                        'description' => $this->_getSourceClassName()
                    ),
                ),
            ),
        );

        return array($construct, $createFromArray);
    }
}
