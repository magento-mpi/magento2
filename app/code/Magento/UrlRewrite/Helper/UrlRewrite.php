<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Helper;

class UrlRewrite extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Validation error constants
     */
    const VERR_MANYSLASHES = 1;

    // Too many slashes in a row of request path, e.g. '///foo//'
    const VERR_ANCHOR = 2;

    // Anchor is not supported in request path, e.g. 'foo#bar'

    /**
     * @var \Magento\UrlRewrite\Model\OptionProvider
     */
    protected $_urlrewrite;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\UrlRewrite\Model\OptionProvider $urlrewrite
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\UrlRewrite\Model\OptionProvider $urlrewrite
    ) {
        parent::__construct($context);
        $this->_urlrewrite = $urlrewrite;
    }

    /**
     * Core func to validate request path
     * If something is wrong with a path it throws localized error message and error code,
     * that can be checked to by wrapper func to alternate error message
     *
     * @param string $requestPath
     * @return bool
     * @throws \Exception
     */
    protected function _validateRequestPath($requestPath)
    {
        if (strpos($requestPath, '//') !== false) {
            throw new \Exception(
                __('Two and more slashes together are not permitted in request path'),
                self::VERR_MANYSLASHES
            );
        }
        if (strpos($requestPath, '#') !== false) {
            throw new \Exception(__('Anchor symbol (#) is not supported in request path'), self::VERR_ANCHOR);
        }
        return true;
    }

    /**
     * Validates request path
     * Either returns TRUE (success) or throws error (validation failed)
     *
     * @param string $requestPath
     * @throws \Magento\Framework\Model\Exception
     * @return bool
     */
    public function validateRequestPath($requestPath)
    {
        try {
            $this->_validateRequestPath($requestPath);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Model\Exception($e->getMessage());
        }
        return true;
    }
}
