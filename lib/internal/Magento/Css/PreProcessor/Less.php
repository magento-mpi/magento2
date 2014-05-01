<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use Magento\Framework\View\Asset\PreProcessorInterface;

class Less implements PreProcessorInterface
{
    /**
     * @var \Magento\Less\FileGenerator
     */
    protected $fileGenerator;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @param \Magento\Less\FileGenerator $fileGenerator
     * @param AdapterInterface $adapter
     */
    public function __construct(
        \Magento\Less\FileGenerator $fileGenerator,
        AdapterInterface $adapter
    ) {
        $this->fileGenerator = $fileGenerator;
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function process(\Magento\Framework\View\Asset\PreProcessor\Chain $chain)
    {
        $chain->setContentType('less');
        $tmpLessFile = $this->fileGenerator->generateLessFileTree($chain);
        $cssContent = $this->adapter->process($tmpLessFile);
        $cssTrimmedContent = trim($cssContent);
        if (!empty($cssTrimmedContent)) {
            $chain->setContent($cssContent);
            $chain->setContentType('css');
        }
    }
}
