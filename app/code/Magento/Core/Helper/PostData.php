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
     * @var \Magento\Data\Form\FormKey
     */
    protected $_formKey;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Data\Form\FormKey $formKey
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Data\Form\FormKey $formKey
    ) {
        parent::__construct($context);
        $this->_formKey = $formKey;
    }

    /**
     * @param string $url
     * @param array $data
     * @return string
     */
    public function getPostData($url, array $data = array())
    {
        if (!isset($data['form_key'])) {
            $data['form_key'] = $this->_formKey->getFormKey();
        }
        if (!isset($data[\Magento\App\Action\Action::PARAM_NAME_URL_ENCODED])) {
            $data[\Magento\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->getEncodedUrl();
        }
        return json_encode(['action' => $url, 'data' => $data]);
    }

    /**
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
