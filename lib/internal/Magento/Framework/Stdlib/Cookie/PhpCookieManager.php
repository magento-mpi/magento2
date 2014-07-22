<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

/**
 * CookieManager helps manage the setting, retrieving and deleting of cookies.
 *
 * To aid in security, the cookie manager will make it possible for the application to indicate if the cookie contains
 * sensitive data so that extra protection can be added to the contents of the cookie as well as how the browser
 * stores the cookie.
 */
class PhpCookieManager implements CookieManager
{
    /**
     * @var CookieScope
     */
    private $scope;

    /**
     * @param CookieScope $scope
     */
    public function __construct(CookieScope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * Set a value in a private cookie with the given $name $value pairing.
     *
     * Sensitive cookies cannot be accessed by JS. HttpOnly will always be set to true for these cookies.
     *
     * @param string $name
     * @param string $value
     * @param SensitiveCookieMetadata $metadata
     * @return void
     * @throws FailureToSendException Cookie couldn't be sent to the browser.  If this exception isn't thrown,
     * there is still no guarantee that the browser received and accepted the cookie.
     * @throws BrowserNotSupportedException Is thrown if we detected that the browser doesn't support all features
     * needed for setting this cookie.
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     */
    public function setSensitiveCookie($name, $value, SensitiveCookieMetadata $metadata = null)
    {
        // TODO: Implement setSensitiveCookie() method.
    }

    /**
     * Set a value in a public cookie with the given $name $value pairing.
     *
     * Public cookies can be accessed by JS. HttpOnly will be set to false by default for these cookies,
     * but can be changed to true.
     *
     * @param string $name
     * @param string $value
     * @param PublicCookieMetadata $metadata
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     * @throws BrowserNotSupportedException If browser doesn't support all features needed for setting this cookie
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     */
    public function setPublicCookie($name, $value, PublicCookieMetadata $metadata = null)
    {
        // TODO: Implement setPublicCookie() method.
    }

    /**
     * Retrieve a value from a cookie.
     *
     * @param string $name
     * @param string|null $default The default value to return if no value could be found for the given $name.
     * @return string|null
     */
    public function getCookie($name, $default = null)
    {
        return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $default;
    }

    /**
     * Deletes a cookie with the given name.
     *
     * @param string $name
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     *     If this exception isn't thrown, there is still no guarantee that the browser
     *     received and accepted the request to delete this cookie.
     */
    public function deleteCookie($name)
    {
        // TODO: Implement deleteCookie() method.
    }

    /**
     * Merges the input override metadata with any defaults set on this CookieManager, and then returns a CookieMetadata
     * object representing the merged values.
     *
     * @param SensitiveCookieMetadata|null $override
     * @return SensitiveCookieMetadata
     */
    private function getSensitiveCookieMetadata(SensitiveCookieMetadata $override)
    {
        // TODO: Implement getSensitiveCookieMetadata
        return $override;
    }

    /**
     * Merges the input override metadata with any defaults set on this CookieManager, and then returns a CookieMetadata
     * object representing the merged values.
     *
     * @param PublicCookieMetadata|null $override
     * @return PublicCookieMetadata
     */
    private function getPublicCookieMetadata(PublicCookieMetadata $override)
    {
        // TODO: Implement getPublicCookieMetadata
        return $override;
    }
}
