<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class BlockFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $blockName
     * @param array $arguments
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function createBlock($blockName, array $arguments = array())
    {
        return $this->_objectManager->create($blockName, $arguments);
    }
}
