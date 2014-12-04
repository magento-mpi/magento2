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
        $params[\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->urlEncoder->encode($url);
        return $this->_getUrl('directory/currency/switch', $params);
    }
}
