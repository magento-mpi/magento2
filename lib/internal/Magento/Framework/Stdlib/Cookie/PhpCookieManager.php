<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

use Magento\Framework\Stdlib\CookieManager as CookieManager;

/**
 * CookieManager helps manage the setting, retrieving and deleting of cookies.
 *
 * To aid in security, the cookie manager will make it possible for the application to indicate if the cookie contains
 * sensitive data so that extra protection can be added to the contents of the cookie as well as how the browser
 * stores the cookie.
 */
class PhpCookieManager implements CookieManager
{
    /**#@+
     * Constants for Cookie manager.
     * RFC 2109 - Page 15
     * http://www.ietf.org/rfc/rfc2109.txt
     */
    const MAX_NUM_COOKIES = 20;
    const MAX_COOKIE_SIZE = 4096;
    const EXPIRE_NOW_TIME = 1;
    const EXPIRE_AT_END_OF_SESSION_TIME = 0;
    /**#@-*/

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
        if (is_null($metadata)) {
            $metadata = $this->scope->getSensitiveCookieMetadata();
        }
        $metadataArray = $metadata->__toArray();
        $metadataArray[PublicCookieMetadata::KEY_SECURE] = true;
        $metadataArray[PublicCookieMetadata::KEY_HTTP_ONLY] = true;

        $this->setCookie($name, $value, $metadataArray);
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
        if (is_null($metadata)) {
            $metadata = $this->scope->getPublicCookieMetadata();
        }
        $metadataArray = $metadata->__toArray();

        $this->setCookie($name, $value, $metadataArray);
    }

    /**
     * Set a value in a cookie with the given $name $value pairing.
     *
     * @param string $name
     * @param string $value
     * @param array $metadataArray
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     * @throws BrowserNotSupportedException If browser doesn't support all features needed for setting this cookie
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     */
    private function setCookie($name, $value, array $metadataArray)
    {
        if (trim($value) == false) {
            $value = '';
            $expireTime = self::EXPIRE_NOW_TIME;
        } else {
            if (isset($metadataArray[PublicCookieMetadata::KEY_DURATION])) {
                $expireTime = $metadataArray[PublicCookieMetadata::KEY_DURATION] + time();
            } else {
                $expireTime = self::EXPIRE_AT_END_OF_SESSION_TIME;
            }

            $numCookies = count($_COOKIE);
            if ($numCookies >= self::MAX_NUM_COOKIES - 1) {
                $errorMessage = 'Unable to send the cookie. Maximum number of cookies exceeded.';
                throw new CookieSizeLimitReachedException(__($errorMessage));
            }
        }

        $sizeOfCookie = $this->sizeOfCookie($name, $value);

        if ($sizeOfCookie > self::MAX_COOKIE_SIZE) {
            $errorMessage = 'Unable to send the cookie. ' .
                'Size of cookie name=\'' . $name . '\' is ' . $sizeOfCookie . ' bytes.';
            throw new CookieSizeLimitReachedException(__($errorMessage));
        }

        $phpSetcookieSuccess = setcookie(
            $name,
            $value,
            $expireTime,
            $metadataArray[CookieMetadata::KEY_PATH],
            $metadataArray[CookieMetadata::KEY_DOMAIN],
            $metadataArray[PublicCookieMetadata::KEY_SECURE],
            $metadataArray[PublicCookieMetadata::KEY_HTTP_ONLY]
        );

        if (!$phpSetcookieSuccess) {
            $errorMessage = $value == '' ? "Unable to delete the cookie" : "Unable to send the cookie";
            $errorMessage= $errorMessage." name with cookieName = $name";

            throw new FailureToSendException(__($errorMessage));
        }
    }

    /**
     * Retrieve the size of a cookie.
     * The size of a cookie is determined by the length of "name=value" portion of the cookie.
     *
     * @param string $name
     * @param string $value
     * @return int
     */
    private function sizeOfCookie($name, $value)
    {
        return strlen($name) + strlen('=') + strlen($value);
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
     * @param CookieMetadata $metadata
     * @return void
     * @throws FailureToSendException If cookie couldn't be sent to the browser.
     *     If this exception isn't thrown, there is still no guarantee that the browser
     *     received and accepted the request to delete this cookie.
     */
    public function deleteCookie($name, CookieMetadata $metadata = null)
    {
        // Remove the cookie
        unset($_COOKIE[$name]);
        $metadataArray = $metadata->__toArray();
        if (empty($metadataArray)) {
            $metadataArray[CookieMetadata::KEY_PATH] = null;
            $metadataArray[CookieMetadata::KEY_DOMAIN] = null;
        }
        $metadataArray[PublicCookieMetadata::KEY_SECURE] = false;
        $metadataArray[PublicCookieMetadata::KEY_HTTP_ONLY] = false;

        // cookie value set to false to delete from the remote client
        $this->setCookie($name, null, $metadataArray);
    }
}
