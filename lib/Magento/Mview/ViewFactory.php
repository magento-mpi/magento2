<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

class ViewFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    const INSTANCE_NAME = 'Magento\Mview\ViewInterface';

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
     * @return View
     */
    public function create(array $data = array())
    {
        return $this->objectManager->create(self::INSTANCE_NAME, $data);
    }
}
