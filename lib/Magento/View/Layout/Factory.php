<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Layout;

class Factory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento\View\Layout';

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\View\Layout
     */
    public function create(array $data = array())
    {
        return $this->objectManager->create(self::CLASS_NAME, $data);
    }
}
