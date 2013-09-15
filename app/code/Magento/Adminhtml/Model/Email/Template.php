<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml email template model
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Model\Email;

class Template extends \Magento\Core\Model\Email\Template
{
    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\View\FileSystem $viewFileSystem
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\Core\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\View\FileSystem $viewFileSystem,
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\Core\Model\Config $config,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($context, $registry, $filesystem, $viewUrl, $viewFileSystem, $design, $data);
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

        $configData = $this->_config->getValue(null, 'default');
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
                $output = array_merge(
                    $output,
                    $this->_findEmailTemplateUsages($code, $value, $configPath)
                );
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

        /** @var \Magento\Backend\Model\Config\Structure $configStructure  */
        $configStructure = \Mage::getSingleton('Magento\Backend\Model\Config\Structure');
        $templatePaths = $configStructure
            ->getFieldPathsByAttribute('source_model', 'Magento\Backend\Model\Config\Source\Email\Template');

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
