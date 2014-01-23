<?php
/**
 * Factory class for \Magento\Authorization
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorization;

use Magento\Authorization;
use Magento\ObjectManager;

class Factory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento\Authorization';

    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return Authorization
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $data);
    }
}
