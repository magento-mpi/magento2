<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\View\State;

class CollectionFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    const INSTANCE_NAME = 'Magento\Mview\View\State\CollectionInterface';

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
     * @return CollectionInterface
     */
    public function create(array $data = array())
    {
        return $this->objectManager->create(self::INSTANCE_NAME, $data);
    }
}
