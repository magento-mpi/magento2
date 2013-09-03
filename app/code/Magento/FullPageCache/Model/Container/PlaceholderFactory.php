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

class Magento_FullPageCache_Model_Container_PlaceholderFactory
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
     * @return Magento_FullPageCache_Model_Container_Placeholder
     */
    public function create($definition)
    {
        return $this->_objectManager->create(
            'Magento_FullPageCache_Model_Container_Placeholder',
            array('definition' => $definition)
        );
    }
}
