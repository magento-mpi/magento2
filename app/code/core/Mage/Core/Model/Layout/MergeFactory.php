<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout merge factory
 */
class Mage_Core_Model_Layout_MergeFactory
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
     * @param string $layoutMergeName
     * @param array $arguments
     * @return Mage_Core_Block_Abstract
     */
    public function createLayoutMerge($layoutMergeName, array $arguments = array())
    {
        return $this->_objectManager->create($layoutMergeName, $arguments, false);
    }
}