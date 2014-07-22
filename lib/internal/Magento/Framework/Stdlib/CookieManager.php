<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib;

/**
 * CookieManager helps manage the setting, retrieving and deleting of cookies.
 *
 * The cookie manager will make it possible for an application to indicate if a cookie contains sensitive data,
 * this will allow extra protection to be added to the contents of the cookie as well sending directives to the browser
 * about how the cookie should be stored and whether JavaScript can access the cookie.
 */
interface CookieManager
{
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
    public function setSensitiveCookie($name, $value, SensitiveCookieMetadata $metadata = null);

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
    public function setPublicCookie($name, $value, PublicCookieMetadata $metadata = null);

    /**
     * Retrieve a value from a cookie.
     *
     * @param string $name
     * @param string|null $default The default value to return if no value could be found for the given $name.
     * @return string|null
     */
    public function getCookie($name, $default = null);


    /**
     * Deletes a cookie with the given name.
     *
     * @param string $name
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     *     If this exception isn't thrown, there is still no guarantee that the browser
     *     received and accepted the request to delete this cookie.
     */
    public function deleteCookie($name);
}
