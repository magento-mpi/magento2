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
    protected function _getClassProperties()
    {
        // const CLASS_NAME = '<source_class_name>';
        $className = array(
            'name'         => 'CLASS_NAME',
            'const'        => true,
            'defaultValue' => $this->_getSourceClassName(),
            'docblock'     => array('shortDescription' => 'Entity class name'),
        );

        // protected $_objectManager = null;
        $objectManager = array(
            'name'       => '_objectManager',
            'visibility' => 'protected',
            'docblock'   => array(
                'shortDescription' => 'Object Manager instance',
                'tags'             => array(
                    array('name' => 'var', 'description' => 'Magento_ObjectManager')
                )
            ),
        );

        return array($className, $objectManager);
    }

    /**
     * @return array
     */
    protected function _getClassMethods()
    {
        // public function __construct(Magento_ObjectManager $objectManager)
        $construct = array(
            'name'       => '__construct',
            'parameters' => array(
                array('name' => 'objectManager', 'type' => 'Magento_ObjectManager'),
            ),
            'body' => '$this->_objectManager = $objectManager;',
            'docblock' => array(
                'shortDescription' => 'Factory constructor',
                'tags'             => array(
                    array(
                        'name'        => 'param',
                        'description' => 'Magento_ObjectManager $objectManager'
                    ),
                ),
            ),
        );

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
