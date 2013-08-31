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
     * Object manager
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Config_Dom
     */
    protected $_dom;

    /**
     * @param Magento_ObjectManager $objectManger
     */
    public function __construct(Magento_ObjectManager $objectManger)
    {
        $this->_objectManager = $objectManger;
    }

    /**
     * Retrieve DOM object
     *
     * @return Magento_Config_Dom
     */
    public function getDom(array $arguments = array())
    {
        if (!$this->_dom) {
            $this->_dom = $this->_objectManager->create(self::CLASS_NAME, $arguments);
        }
        return $this->_dom;
    }
}