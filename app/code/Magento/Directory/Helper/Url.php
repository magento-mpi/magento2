<?php
/**
 * {license_notice}
 *
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
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\StoreManagerInterface $storeManager,
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
        $params[\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->_coreData->urlEncode($url);
        return $this->_getUrl('directory/currency/switch', $params);
    }
}
