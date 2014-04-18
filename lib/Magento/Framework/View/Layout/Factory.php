<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

class Factory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento\Framework\View\Layout';

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
     * @return \Magento\Framework\View\Layout
     */
    public function create(array $data = array())
    {
        return $this->objectManager->create(self::CLASS_NAME, $data);
    }
}
