<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure\Element\Dependency;

class FieldFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create dependency field model instance.
     *
     * @param array $arguments
     * @return Field
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(
            'Magento\Backend\Model\Config\Structure\Element\Dependency\Field',
            $arguments
        );
    }
}
