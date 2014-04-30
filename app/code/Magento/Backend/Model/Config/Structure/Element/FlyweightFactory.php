<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure\Element;

class FlyweightFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Map of flyweight types
     *
     * @var array
     */
    protected $_flyweightMap = array(
        'section' => 'Magento\Backend\Model\Config\Structure\Element\Section',
        'group' => 'Magento\Backend\Model\Config\Structure\Element\Group',
        'field' => 'Magento\Backend\Model\Config\Structure\Element\Field'
    );

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create element flyweight flyweight
     *
     * @param string $type
     * @return \Magento\Backend\Model\Config\Structure\ElementInterface
     */
    public function create($type)
    {
        return $this->_objectManager->create($this->_flyweightMap[$type]);
    }
}
