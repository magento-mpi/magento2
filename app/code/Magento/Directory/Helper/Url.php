<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
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
    public function getSwitchCurrencyUrl($params = [])
    {
        $params = is_array($params) ? $params : [];

        if ($this->_getRequest()->getAlias('rewrite_request_path')) {
            $url = $this->_storeManager->getStore()->getBaseUrl() . $this->_getRequest()->getAlias(
                'rewrite_request_path'
            );
        } else {
            $url = $this->_urlBuilder->getCurrentUrl();
        }
        $params[\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->urlEncoder->encode($url);
        return $this->_getUrl('directory/currency/switch', $params);
    }
}
