<?php
/**
 * Helper to obtain post data for postData widget
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class PostData extends \Magento\Framework\App\Helper\AbstractHelper
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
        if (!isset($data[\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED])) {
            $data[\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->getEncodedUrl();
        }
        return json_encode(array('action' => $url, 'data' => $data));
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
        return $this->urlEncoder->encode($url);
    }
}
