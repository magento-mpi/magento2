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
     * Retrieve preprocessors instances suitable to convert source content type into a destination one
     *
     * @param string $sourceContentType
     * @param string $targetContentType
     * @return \Magento\View\Asset\PreProcessorInterface[]
     */
    public function getPreProcessors($sourceContentType, $targetContentType)
    {
        $result = array();
        if ($sourceContentType == 'less') {
            if ($targetContentType == 'css') {
                $result[] = $this->objectManager->get('Magento\Css\PreProcessor\Less');
            } else if ($targetContentType == 'less') {
                $result[] = $this->objectManager->get('Magento\Less\PreProcessor\Instruction\MagentoImport');
                $result[] = $this->objectManager->get('Magento\Less\PreProcessor\Instruction\Import');
            }
        }
        if ($targetContentType == 'css') {
            $result[] = $this->objectManager->get('Magento\View\Asset\PreProcessor\ModuleNotation');
        }
        return $result;
    }
} 
