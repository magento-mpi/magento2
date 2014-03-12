<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory URL helper
 */
namespace Magento\Directory\Helper;

class Url extends \Magento\Core\Helper\Url
{
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\Data $coreData
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $storeManager);
    }

    /**
     * Retrieve switch currency url
     *
     * @param array $params Additional url params
     * @return string
     */
    public function getSwitchCurrencyUrl($params = array())
    {
        $params = is_array($params) ? $params : array();

        if ($this->_getRequest()->getAlias('rewrite_request_path')) {
            $url = $this->_storeManager->getStore()->getBaseUrl() . $this->_getRequest()->getAlias(
                'rewrite_request_path'
            );
        } else {
            $url = $this->_urlBuilder->getCurrentUrl();
        }
        $params[\Magento\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->_coreData->urlEncode($url);
        return $this->_getUrl('directory/currency/switch', $params);
    }
}
