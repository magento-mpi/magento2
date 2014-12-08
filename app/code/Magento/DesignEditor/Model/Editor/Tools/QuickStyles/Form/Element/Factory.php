<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Form\Element;

use Magento\Framework\ObjectManagerInterface;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create form element with provided params
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function create($className, array $data = [])
    {
        return $this->_objectManager->create($className, ['data' => $data]);
    }
}
