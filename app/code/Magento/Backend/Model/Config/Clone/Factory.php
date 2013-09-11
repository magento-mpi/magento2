<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration clone model factory
 */
namespace Magento\Backend\Model\Config\Clone;

class Factory
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
     * Create new clone model
     *
     * @param string $cloneModel
     * @return \Magento\Core\Model\Config\Value
     */
    public function create($cloneModel)
    {
        return $this->_objectManager->create($cloneModel);
    }
}
