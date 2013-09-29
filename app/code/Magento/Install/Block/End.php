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
 * Installation ending block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

class End extends \Magento\Install\Block\AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'end.phtml';

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var \Magento\AdminNotification\Model\Survey
     */
    protected $_survey;

    /**
     * Cryptographic key
     * 
     * @var string
     */
    protected $_cryptKey;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\AdminNotification\Model\Survey $survey
     * @param string $cryptKey
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\AdminNotification\Model\Survey $survey,
        $cryptKey,
        array $data = array()
    ) {
        $this->_cryptKey = $cryptKey;
        parent::__construct($coreData, $context, $data);
        $this->_coreConfig = $coreConfig;
        $this->_survey = $survey;
    }

    /**
     * @return string
     */
    public function getEncryptionKey()
    {
        $key = $this->getData('encryption_key');
        if (is_null($key)) {
            $key = $this->_cryptKey;
            $this->setData('encryption_key', $key);
        }
        return $key;
    }

    /**
     * Return url for iframe source
     *
     * @return string|null
     */
    public function getIframeSourceUrl()
    {
        if (!$this->_survey->isSurveyUrlValid()
            || \Mage::getSingleton('Magento\Install\Model\Installer')->getHideIframe()) {
            return null;
        }
        return $this->_survey->getSurveyUrl();
    }
}
