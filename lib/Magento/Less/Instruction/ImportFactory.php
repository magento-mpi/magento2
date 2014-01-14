<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\Instruction;

/**
 * Import factory
 */
class ImportFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create less import instruction
     *
     * @return \Magento\Less\Instruction\Import
     */
    public function create()
    {
        return $this->_objectManager->create('Magento\Less\Instruction\Import');
    }
}
