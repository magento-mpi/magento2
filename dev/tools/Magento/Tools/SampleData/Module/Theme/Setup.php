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
use \Magento\Tools\SampleData\Logger;
use \Magento\Tools\SampleData\SetupInterface;
use \Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

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
     * Url configuration
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $baseUrl;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $configCacheType;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Magento\Theme\Model\Config $config
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollection
     * @param \Magento\Framework\UrlInterface $baseUrl
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param FixtureHelper $fixtureHelper
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Theme\Model\Config $config,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themeCollection,
        \Magento\Framework\UrlInterface $baseUrl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        FixtureHelper $fixtureHelper,
        Logger $logger
    ) {
        $this->config = $config;
        $this->themeCollection = $themeCollection;
        $this->baseUrl = $baseUrl;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->configCacheType = $configCacheType;
        $this->directoryList = $directoryList;
        $this->moduleList = $moduleList;
        $this->fixtureHelper = $fixtureHelper;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing theme' . PHP_EOL);
        $this->assignTheme();
        $this->addHeadInclude();
        $this->logger->log('.' . PHP_EOL);
    }

    /**
     * Assign Theme
     *
     * @return void
     */
    protected function assignTheme()
    {
        $themes = $this->themeCollection->create()->loadRegisteredThemes();
        /** @var \Magento\Core\Model\Theme $theme */
        foreach ($themes as $theme) {
            if ($theme->getCode() == 'Magento/luma') {
                $this->config->assignToStore($theme, [Store::DEFAULT_STORE_ID], ScopeInterface::SCOPE_DEFAULT);
            }
        }
    }

    /**
     * Add Link to Head
     *
     * @return void
     */
    protected function addHeadInclude()
    {
        $styleContent = '';
        foreach (array_keys($this->moduleList->getModules()) as $moduleName) {
            $fileName = substr($moduleName, strpos($moduleName, "_") + 1) . '/styles.css';
            $fileName = $this->fixtureHelper->getPath($fileName);
            if (!$fileName) {
                continue;
            }
            $styleContent .= file_get_contents($fileName);
        }
        if (empty($styleContent)) {
            return;
        }
        $themesDir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        file_put_contents("$themesDir/styles.css", $styleContent);
        $linkTemplate = '<link  rel="stylesheet" type="text/css"  media="all" href="%sstyles.css" />';
        $baseUrl = $this->baseUrl->getBaseUrl(array('_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA));
        $linkText = sprintf($linkTemplate, $baseUrl);

        $miscScriptsNode = 'design/head/includes';
        $miscScripts = $this->scopeConfig->getValue($miscScriptsNode);
        if (strpos($miscScripts, $linkText) === false) {
            $this->configWriter->save($miscScriptsNode, $miscScripts . $linkText);
            $this->configCacheType->clean();
        }
    }

}
