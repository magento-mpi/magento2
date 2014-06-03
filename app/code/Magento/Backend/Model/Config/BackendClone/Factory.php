<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration clone model factory
 */
namespace Magento\Backend\Model\Config\BackendClone;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new clone model
     *
     * @param string $cloneModel
     * @return mixed
     */
    public function create($cloneModel)
    {
        return $this->_objectManager->create($cloneModel);
    }
}
