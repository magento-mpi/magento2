<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url processing helper
 */
namespace Magento\FullPageCache\Helper;

class Url
{
    /**
     * @var \Magento\Core\Helper\Url
     */
    protected $_urlHelper;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_coreSession;

    /**
     * @param \Magento\Core\Helper\Url $urlHelper
     * @param \Magento\Core\Model\Session $coreSession
     */
    public function __construct(
        \Magento\Core\Helper\Url $urlHelper,
        \Magento\Core\Model\Session $coreSession
    ) {
        $this->_urlHelper = $urlHelper;
        $this->_coreSession = $coreSession;
    }

    /**
     * Retrieve unique marker value
     *
     * @return string
     */
    protected static function _getSidMarker()
    {
        return '{{' . chr(1) . chr(2) . chr(3) . '_SID_MARKER_' . chr(3) . chr(2) . chr(1) . '}}';
    }

    /**
     * Replace all occurrences of session_id with unique marker
     *
     * @param  string $content
     * @return bool
     */
    public function replaceSid(&$content)
    {
        if (!$content) {
            return false;
        }
        $replacementCount = 0;
        $content = str_replace(
            $this->_coreSession->getSessionIdQueryParam() . '=' . $this->_coreSession->getSessionId(),
            $this->_coreSession->getSessionIdQueryParam() . '=' . self::_getSidMarker(),
            $content, $replacementCount);
        return ($replacementCount > 0);
    }

    /**
     * Restore session_id from marker value
     *
     * @param string $content
     * @param string $sidValue
     * @return bool
     */
    public static function restoreSid(&$content, $sidValue)
    {
        if (!$content) {
            return false;
        }
        $replacementCount = 0;
        $content = str_replace(self::_getSidMarker(), $sidValue, $content, $replacementCount);
        return ($replacementCount > 0);
    }

    /**
     * Calculate UENC parameter value and replace it
     *
     * @param string $content
     * @return string
     */
    public function replaceUenc($content)
    {
        $search = '/\/(' . \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED . ')\/[^\/]*\//';
        $replace = '/$1/' . $this->_urlHelper->getEncodedUrl() . '/';
        $content = preg_replace($search, $replace, $content);
        return $content;
    }
}
