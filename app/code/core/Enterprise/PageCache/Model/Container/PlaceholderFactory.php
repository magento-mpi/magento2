<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_Container_PlaceholderFactory
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
     * @param $definition
     * @return Enterprise_PageCache_Model_Container_Placeholder
     */
    public function create($definition)
    {
        return $this->_objectManager->create(
            'Enterprise_PageCache_Model_Container_Placeholder',
            array('definition' => $definition)
        );
    }
}
