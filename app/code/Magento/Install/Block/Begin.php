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
namespace Magento\Install\Block;

class Begin extends \Magento\Install\Block\AbstractBlock
{
    protected $_template = 'begin.phtml';

    /**
     * Eula file name
     *
     * @var string
     */
    protected $_eulaFile;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     * @param string|null $eulaFile
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array(),
        $eulaFile = null
    ) {
        $this->_eulaFile = $eulaFile;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get wizard URL
     *
     * @return string
     */
    public function getPostUrl()
    {
        return \Mage::getUrl('install/wizard/beginPost');
    }

    /**
     * Get License HTML contents
     *
     * @return string
     */
    public function getLicenseHtml()
    {
        return ($this->_eulaFile) ? $this->_filesystem->read(BP . DS . $this->_eulaFile) : '';
    }
}
