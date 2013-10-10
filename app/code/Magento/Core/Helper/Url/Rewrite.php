<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url rewrite helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Helper\Url;

class Rewrite extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Validation error constants
     */
    const VERR_MANYSLASHES = 1; // Too many slashes in a row of request path, e.g. '///foo//'
    const VERR_ANCHOR = 2;      // Anchor is not supported in request path, e.g. 'foo#bar'

    /**
     * @var \Magento\Core\Model\Source\Urlrewrite\Options
     */
    protected $_urlrewrite;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Source\Urlrewrite\Options $urlrewrite
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Source\Urlrewrite\Options $urlrewrite
    ) {
        parent::__construct($context);
        $this->_urlrewrite = $urlrewrite;
    }

    /**
     * Core func to validate request path
     * If something is wrong with a path it throws localized error message and error code,
     * that can be checked to by wrapper func to alternate error message
     *
     * @return bool
     */
    protected function _validateRequestPath($requestPath)
    {
        if (strpos($requestPath, '//') !== false) {
            throw new \Exception(__('Two and more slashes together are not permitted in request path'), self::VERR_MANYSLASHES);
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
     * @param $requestPath
     * @throws \Magento\Core\Exception
     * @return bool
     */
    public function validateRequestPath($requestPath)
    {
        try {
            $this->_validateRequestPath($requestPath);
        } catch (\Exception $e) {
            throw new \Magento\Core\Exception($e->getMessage());
        }
        return true;
    }

    /**
     * Validates suffix for url rewrites to inform user about errors in it
     * Either returns TRUE (success) or throws error (validation failed)
     *
     * @param $suffix
     * @throws \Magento\Core\Exception
     * @return bool
     */
    public function validateSuffix($suffix)
    {
        try {
            $this->_validateRequestPath($suffix); // Suffix itself must be a valid request path
        } catch (\Exception $e) {
            // Make message saying about suffix, not request path
            switch ($e->getCode()) {
                case self::VERR_MANYSLASHES:
                    throw new \Magento\Core\Exception(
                        __('Two and more slashes together are not permitted in url rewrite suffix')
                    );
                case self::VERR_ANCHOR:
                    throw new \Magento\Core\Exception(__('Anchor symbol (#) is not supported in url rewrite suffix'));
            }
        }
        return true;
    }

    /**
     * Has redirect options set
     *
     * @param \Magento\Core\Model\Url\Rewrite $urlRewrite
     * @return bool
     */
    public function hasRedirectOptions($urlRewrite)
    {
        return in_array($urlRewrite->getOptions(), $this->_urlrewrite->getRedirectOptions());
    }
}
