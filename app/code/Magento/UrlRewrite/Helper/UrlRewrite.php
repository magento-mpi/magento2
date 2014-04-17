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
     * @var \Magento\UrlRewrite\Model\Source\Urlrewrite\Options
     */
    protected $_urlrewrite;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\UrlRewrite\Model\UrlRewrite\OptionProvider $urlrewrite
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\UrlRewrite\Model\UrlRewrite\OptionProvider $urlrewrite
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
     * @throws \Magento\Model\Exception
     * @return bool
     */
    public function validateRequestPath($requestPath)
    {
        try {
            $this->_validateRequestPath($requestPath);
        } catch (\Exception $e) {
            throw new \Magento\Model\Exception($e->getMessage());
        }
        return true;
    }

    /**
     * Validates suffix for url rewrites to inform user about errors in it
     * Either returns TRUE (success) or throws error (validation failed)
     *
     * @param string $suffix
     * @throws \Magento\Model\Exception
     * @return bool
     */
    public function validateSuffix($suffix)
    {
        try {
            // Suffix itself must be a valid request path
            $this->_validateRequestPath($suffix);
        } catch (\Exception $e) {
            // Make message saying about suffix, not request path
            switch ($e->getCode()) {
                case self::VERR_MANYSLASHES:
                    throw new \Magento\Model\Exception(
                        __('Two and more slashes together are not permitted in url rewrite suffix')
                    );
                case self::VERR_ANCHOR:
                    throw new \Magento\Model\Exception(__('Anchor symbol (#) is not supported in url rewrite suffix'));
            }
        }
        return true;
    }

    /**
     * Has redirect options set
     *
     * @param \Magento\UrlRewrite\Model\UrlRewrite $urlRewrite
     * @return bool
     */
    public function hasRedirectOptions($urlRewrite)
    {
        return in_array($urlRewrite->getOptions(), $this->_urlrewrite->getRedirectOptions());
    }
}
