<?php
namespace OAuth\Common\Service;

use OAuth\Common\Http\Uri\UriInterface;

/**
 * Defines methods common among all OAuth services.
 */
interface ServiceInterface
{
    /**
     * Sends an authenticated API request to the path provided.
     * If the path provided is not an absolute URI, the base API Uri (service-specific) will be used.
     *
     * @abstract
     * @param $path string|UriInterface
     * @param string $method HTTP method
     * @param array $body Request body if applicable (key/value pairs)
     * @param array $extraHeaders Extra headers if applicable. These will override service-specific any defaults.
     * @return string
     */
    public function request($path, $method = 'GET', array $body = [], array $extraHeaders = []);

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @abstract
     * @param array $additionalParameters
     * @return UriInterface
     */
    public function getAuthorizationUri(array $additionalParameters = []);

    /**
     * Returns the authorization API endpoint.
     *
     * @abstract
     * @return UriInterface
     */
    public function getAuthorizationEndpoint();

    /**
     * Returns the access token API endpoint.
     *
     * @abstract
     * @return UriInterface
     */
    public function getAccessTokenEndpoint();
}
