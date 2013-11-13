<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

class ReadFactory
{
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
     * Create a readable file
     *
     * @param string $path
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create($path)
    {
        return $this->objectManager->create('Magento\Filesystem\File\Read', array('path' => $path));
    }
}