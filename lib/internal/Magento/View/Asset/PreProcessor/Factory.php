<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

use Magento\ObjectManager;

/**
 * A factory for known types of preprocessors
 */
class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get a list of preprocessors for specified content type
     *
     * @param string $contentType
     * @return \Magento\View\Asset\PreProcessorInterface[]
     */
    public function getPreProcessors($contentType)
    {
        switch ($contentType) {
            case 'css':
                return array(
                    $this->objectManager->get('Magento\View\Asset\PreProcessor\ModuleNotation'),
                );
            case 'less':
                // not implemented yet
                return array();
            default:
                return array();
        }
    }
} 
