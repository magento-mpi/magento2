<?php
/**
 * Magento validator config factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\PageLayout\Config;

/**
 * Page layout config builder
 */
class Builder
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\View\PageLayout\File\Collector\Aggregated
     */
    protected $fileCollector;

    /**
     * @var \Magento\Core\Model\Resource\Theme\Collection
     */
    protected $themeCollection;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\View\PageLayout\File\Collector\Aggregated $fileCollector
     * @param \Magento\Core\Model\Resource\Theme\Collection $themeCollection
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\View\PageLayout\File\Collector\Aggregated $fileCollector,
        \Magento\Core\Model\Resource\Theme\Collection $themeCollection
    ) {
        $this->objectManager = $objectManager;
        $this->fileCollector = $fileCollector;
        $this->themeCollection = $themeCollection;
    }

    /**
     * @return \Magento\Framework\View\PageLayout\Config
     */
    public function getPageLayoutsConfig()
    {
        return $this->objectManager->create(
            'Magento\Framework\View\PageLayout\Config',
            ['configFiles' => $this->getConfigFiles()]
        );
    }

    /**
     * @return array
     */
    protected function getConfigFiles()
    {
        $configFiles = [];
        foreach ($this->themeCollection->loadRegisteredThemes() as $theme) {
            $configFiles = array_merge($configFiles, $this->fileCollector->getFilesContent($theme, 'layouts.xml'));
        }

        return $configFiles;
    }
}
