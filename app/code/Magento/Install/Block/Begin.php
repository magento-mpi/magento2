<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation begin block
 */
class Magento_Install_Block_Begin extends Magento_Install_Block_Abstract
{
    protected $_template = 'begin.phtml';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Get wizard URL
     *
     * @return string
     */
    public function getPostUrl()
    {
        return Mage::getUrl('install/wizard/beginPost');
    }

    /**
     * Get License HTML contents
     *
     * @return string
     */
    public function getLicenseHtml()
    {
        return $this->_filesystem->read(BP . DS . (string)$this->_coreConfig->getNode('install/eula_file'));
    }
}
