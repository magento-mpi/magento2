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

class Magento_Adminhtml_Model_Email_Template extends Magento_Core_Model_Email_Template
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     * @param Magento_Core_Model_View_Design_Proxy $design
     * @param Magento_Core_Model_Config $config
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_View_FileSystem $viewFileSystem,
        Magento_Core_Model_View_Design_Proxy $design,
        Magento_Core_Model_Config $config,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($context, $filesystem, $viewUrl, $viewFileSystem, $design, $data);
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

        /** @var Magento_Backend_Model_Config_Structure $configStructure  */
        $configStructure = Mage::getSingleton('Magento_Backend_Model_Config_Structure');
        $templatePaths = $configStructure
            ->getFieldPathsByAttribute('source_model', 'Magento_Backend_Model_Config_Source_Email_Template');

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
