<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Directory;

class ReadFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    private $objectManager;

    /**
     * File read factory
     *
     * @var \Magento\Filesystem\File\ReadFactory
     */
    private $fileFactory;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Filesystem\File\ReadFactory $fileFactory
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Filesystem\File\ReadFactory $fileFactory
    )
    {
        $this->objectManager = $objectManager;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Create a readable directory
     *
     * @param array $config
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create(array $config)
    {
        return $this->objectManager->create(
            'Magento\Filesystem\Directory\Read',
            array(
                'config' => $config,
                'fileFactory' => $this->fileFactory
            )
        );
    }
}