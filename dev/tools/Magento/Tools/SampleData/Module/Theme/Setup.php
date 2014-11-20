<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Theme;

use \Magento\Framework\App\ScopeInterface;
use \Magento\Store\Model\Store;
use Magento\Tools\SampleData\Logger;
use \Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Theme module
 */
class Setup implements SetupInterface
{
    /**
     * @var \Magento\Theme\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Core\Model\Resource\Theme\Collection
     */
    private $themeCollection;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Magento\Theme\Model\Config $config
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollection
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Theme\Model\Config $config,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollection,
        Logger $logger
    ) {
        $this->config = $config;
        $this->themeCollection = $themeCollection;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing theme' . PHP_EOL);
        $themes = $this->themeCollection->create()->loadRegisteredThemes();
        /** @var \Magento\Core\Model\Theme $theme */
        foreach ($themes as $theme) {
            if ($theme->getCode() == 'Magento/luma') {
                $this->config->assignToStore($theme, [Store::DEFAULT_STORE_ID], ScopeInterface::SCOPE_DEFAULT);
            }
        }
        $this->logger->log('.' . PHP_EOL);
    }
}
