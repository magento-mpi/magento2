<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * 3D Secure Validation Model for adminhtml area
 */
namespace Magento\Centinel\Model\Adminhtml;

class Service extends \Magento\Centinel\Model\Service
{
    /**
     * Unified validation/authentication URL getter
     *
     * @param string $suffix
     * @param bool $current
     * @return string
     */
    protected function _getUrl($suffix, $current = false)
    {
        $params = array(
            '_secure'  => true,
            '_current' => $current,
            'form_key' => $this->_session->getFormKey(),
            'isIframe' => true
        );
        return $this->_url->getUrl('adminhtml/centinel_index/' . $suffix, $params);
    }
}

