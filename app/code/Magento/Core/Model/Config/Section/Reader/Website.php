<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Config\Section\Reader;

class Website
{
    /**
     * @var \Magento\Core\Model\Config\Initial
     */
    protected $_initialConfig;

    /**
     * @var \Magento\Core\Model\Config\SectionPool
     */
    protected $_sectionPool;

    /**
     * @var \Magento\Core\Model\Config\Section\Converter
     */
    protected $_converter;

    /**
     * @var \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Core\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @var \Magento\Core\Model\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Model\Config\Initial $initialConfig
     * @param \Magento\Core\Model\Config\SectionPool $sectionPool
     * @param \Magento\Core\Model\Config\Section\Converter $converter
     * @param \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory $collectionFactory
     * @param \Magento\Core\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Core\Model\App\State $appState
     */
    public function __construct(
        \Magento\Core\Model\Config\Initial $initialConfig,
        \Magento\Core\Model\Config\SectionPool $sectionPool,
        \Magento\Core\Model\Config\Section\Converter $converter,
        \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory $collectionFactory,
        \Magento\Core\Model\WebsiteFactory $websiteFactory,
        \Magento\Core\Model\App\State $appState
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_sectionPool = $sectionPool;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_appState = $appState;
    }

    /**
     * Read configuration by code
     *
     * @param string $code
     * @return array
     */
    public function read($code)
    {
        $config = array_replace_recursive(
            $this->_sectionPool->getSection('default')->getSource(), $this->_initialConfig->getWebsite($code)
        );

        if ($this->_appState->isInstalled()) {
            $website = $this->_websiteFactory->create();
            $website->load($code);
            $collection = $this->_collectionFactory->create(array(
                'scope' => 'websites', 'scopeId' => $website->getId())
            );
            $dbWebsiteConfig = array();
            foreach ($collection as $configValue) {
                $dbWebsiteConfig[$configValue->getPath()] = $configValue->getValue();
            }
            $dbWebsiteConfig = $this->_converter->convert($dbWebsiteConfig);

            if (count($dbWebsiteConfig)) {
                $config = array_replace_recursive($config, $dbWebsiteConfig);
            }
        }
        return $config;
    }
}
