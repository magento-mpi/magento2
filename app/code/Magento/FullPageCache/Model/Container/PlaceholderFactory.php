<?php
/**
 * Container placeholder factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Container;

class PlaceholderFactory
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
     * Create container placeholder instance
     *
     * @param string $definition
     * @return \Magento\FullPageCache\Model\Container\Placeholder
     */
    public function create($definition)
    {
        return $this->_objectManager->create(
            'Magento\FullPageCache\Model\Container\Placeholder',
            array('definition' => $definition)
        );
    }
}
