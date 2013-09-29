<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Structure\Element\Dependency;

class FieldFactory
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
     * Create dependency field model instance.
     *
     * @param array $arguments
     * @return \Magento\Backend\Model\Config\Structure\Element\Dependency\Field
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager
            ->create('Magento\Backend\Model\Config\Structure\Element\Dependency\Field', $arguments);
    }
}
