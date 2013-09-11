<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website factory
 */
namespace Magento\Core\Model\Website;

class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $data
     * @return \Magento\Core\Model\Website
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Core\Model\Website', $data);
    }
}
