<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Service\PreProcessing;

class CacheFactory extends \Magento\Cache\Frontend\Decorator\TagScope
{
    /**
     * @var \Magento\ObjectManager
     */
    private $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\Filesystem\Directory\ReadInterface $sourceDir
     * @param array $directories
     * @return \Magento\View\Service\PreProcessing\Cache
     */
    public function create(\Magento\Filesystem\Directory\ReadInterface $sourceDir, array $directories)
    {
        return $this->objectManager->create(
            'Magento\View\Service\PreProcessing\Cache',
            array(
                'sourceDir'   => $sourceDir,
                'directories' => $directories,
            )
        );
    }
}
