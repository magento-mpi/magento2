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
     * Constructor
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Config $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct(
            $coreData,
            $context,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * @return string
     */
    public function getEncryptionKey()
    {
        $key = $this->getData('encryption_key');
        if (is_null($key)) {
            $key = (string) $this->_coreConfig->getNode('global/crypt/key');
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
        if (!\Magento\AdminNotification\Model\Survey::isSurveyUrlValid()
            || \Mage::getSingleton('Magento\Install\Model\Installer')->getHideIframe()) {
            return null;
        }
        return \Magento\AdminNotification\Model\Survey::getSurveyUrl();
    }
}
