<?php
/**
 * Helper to obtain post data for postData widget
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class PostData extends \Magento\App\Helper\AbstractHelper
{
    /**
     * get data for post by javascript in format acceptable to $.mage.dataPost widget
     *
     * @param string $url
     * @param array $data
     * @return string
     */
    public function getPostData($url, array $data = array())
    {
        if (!isset($data[\Magento\App\Action\Action::PARAM_NAME_URL_ENCODED])) {
            $data[\Magento\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->getEncodedUrl();
        }
        return json_encode(['action' => $url, 'data' => $data]);
    }

    /**
     * Get current encoded url
     *
     * @param string|null $url
     * @return string
     */
    public function getEncodedUrl($url = null)
    {
        if (!$url) {
            $url = $this->_urlBuilder->getCurrentUrl();
        }
        return $this->urlEncode($url);
    }
}
