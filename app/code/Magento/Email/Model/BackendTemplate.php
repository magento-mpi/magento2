<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model;

/**
 * Adminhtml email template model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BackendTemplate extends Template
{
    /**
     * @var \Magento\Backend\Model\Config\Structure
     */
    private $_structure;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\App\Emulation $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Email\Model\Template\FilterFactory $emailFilterFactory
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param \Magento\Backend\Model\Config\Structure $structure
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\View\DesignInterface $design,
        \Magento\Registry $registry,
        \Magento\Core\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\View\Url $viewUrl,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Email\Model\Template\FilterFactory $emailFilterFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Backend\Model\Config\Structure $structure,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $design,
            $registry,
            $appEmulation,
            $storeManager,
            $filesystem,
            $viewUrl,
            $viewFileSystem,
            $scopeConfig,
            $emailFilterFactory,
            $emailConfig,
            $data
        );
        $this->_structure = $structure;
    }

    /**
     * Collect all system config paths where current template is used as default
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedAsDefault()
    {
        $templateCode = $this->getOrigTemplateCode();
        if (!$templateCode) {
            return array();
        }

        $configData = $this->_scopeConfig->getValue(null, \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT);
        $paths = $this->_findEmailTemplateUsages($templateCode, $configData, '');
        return $paths;
    }

    /**
     * Find nodes which are using $templateCode value
     *
     * @param string $code
     * @param array $data
     * @param string $path
     * @return array
     */
    protected function _findEmailTemplateUsages($code, array $data, $path)
    {
        $output = array();
        foreach ($data as $key => $value) {
            $configPath = $path ? $path . '/' . $key : $key;
            if (is_array($value)) {
                $output = array_merge($output, $this->_findEmailTemplateUsages($code, $value, $configPath));
            } else {
                if ($value == $code) {
                    $output[] = array('path' => $configPath);
                }
            }
        }
        return $output;
    }

    /**
     * Collect all system config paths where current template is currently used
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedCurrently()
    {
        $templateId = $this->getId();
        if (!$templateId) {
            return array();
        }

        $templatePaths = $this->_structure->getFieldPathsByAttribute(
            'source_model',
            'Magento\Backend\Model\Config\Source\Email\Template'
        );

        if (!count($templatePaths)) {
            return array();
        }

        $configData = $this->_getResource()->getSystemConfigByPathsAndTemplateId($templatePaths, $templateId);
        if (!$configData) {
            return array();
        }

        return $configData;
    }
}
