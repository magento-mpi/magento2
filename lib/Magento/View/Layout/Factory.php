<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Layout;

use Magento\ObjectManager;

class Factory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento\View\Layout';

    /**
     * Object Manager instance
     *
     * @var ObjectManager
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
     * @return \Magento\View\Layout
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $data);
    }
}
