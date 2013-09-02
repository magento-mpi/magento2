<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento configuration DOM factory
 */
class Magento_Config_DomFactory
{

    const CLASS_NAME = 'Magento_Config_Dom';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManger
     */
    public function __construct(Magento_ObjectManager $objectManger)
    {
        $this->_objectManager = $objectManger;
    }

    /**
     * Create DOM object
     *
     * @param array $arguments
     * @return Magento_Config_Dom
     */
    public function createDom(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}