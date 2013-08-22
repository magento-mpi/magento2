<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_BlockFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $blockName
     * @param array $arguments
     * @return Magento_Core_Block_Abstract
     */
    public function createBlock($blockName, array $arguments = array())
    {
        return $this->_objectManager->create($blockName, $arguments);
    }
}
