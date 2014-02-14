<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Url;

interface QueryParamsResolverInterface
{
    /**
     * Get query params part of url
     *
     * @param bool $escape "&" escape flag
     * @return string
     */
    public function getQuery($escape = false);

    /**
     * Set URL query param(s)
     *
     * @param mixed $data
     * @return \Magento\Url\QueryParamsResolverInterface
     */
    public function setQuery($data);

    /**
     * Set query param
     *
     * @param string $key
     * @param mixed $data
     * @return \Magento\Url\QueryParamsResolverInterface
     */
    public function setQueryParam($key, $data);

    /**
     * Return Query Params
     *
     * @return array
     */
    public function getQueryParams();

    /**
     * Purge Query params array
     *
     * @return \Magento\Url\QueryParamsResolverInterface
     */
    public function purgeQueryParams();

    /**
     * Set query Params as array
     *
     * @param array $data
     * @return \Magento\Url\QueryParamsResolverInterface
     */
    public function setQueryParams(array $data);

    /**
     * Unset data from the object.
     *
     * @param null|string|array $key
     * @return \Magento\Object
     */
    public function unsetData($key = null);
}
