<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

use Magento\Backend\Model\Config\Backend\Cookie;
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


    /**#@+
     * Constant for metadata array key
     */
    const KEY_EXPIRE_TIME = 'expiry';
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
        $expire = $this->computeExpirationTime($metadataArray);

        $this->checkAbilityToSendCookie($name, $value);

        $phpSetcookieSuccess = setcookie(
            $name,
            $value,
            $expire,
            $this->getParameterValue(CookieMetadata::KEY_PATH, $metadataArray),
            $this->getParameterValue(CookieMetadata::KEY_DOMAIN, $metadataArray),
            $this->getParameterValue(PublicCookieMetadata::KEY_SECURE, $metadataArray),
            $this->getParameterValue(PublicCookieMetadata::KEY_HTTP_ONLY, $metadataArray)
        );

        if (!$phpSetcookieSuccess) {

            $params['name'] = $name;
            if ($value == '') {
                throw new FailureToSendException('Unable to delete the cookie with cookieName = %name', $params);
            } else {
                throw new FailureToSendException('Unable to send the cookie with cookieName = %name', $params);
            }
        }
    }

    /**
     * Retrieve the size of a cookie.
     * The size of a cookie is determined by the length of 'name=value' portion of the cookie.
     *
     * @param string $name
     * @param string $value
     * @return int
     */
    private function sizeOfCookie($name, $value)
    {
        // The constant '1' is the length of the equal sign in 'name=value'.
        return strlen($name) + 1 + strlen($value);
    }

    /**
     * Determines whether or not it is possible to send the cookie, based on the number of cookies that already
     * exist and the size of the cookie.
     *
     * @param string $name
     * @param string|null $value
     * @return void if it is possible to send the cookie
     * @throws BrowserNotSupportedException If browser doesn't support all features needed for setting this cookie
     * @throws CookieSizeLimitReachedException Thrown when the cookie is too big to store any additional data.
     */
    private function checkAbilityToSendCookie($name, $value)
    {
        $numCookies = count($_COOKIE);

        if (!isset($_COOKIE[$name])) {
            $numCookies++;
        }

        $sizeOfCookie = $this->sizeOfCookie($name, $value);

        if ($numCookies > PhpCookieManager::MAX_NUM_COOKIES) {
            throw new CookieSizeLimitReachedException(
                __('Unable to send the cookie. Maximum number of cookies would be exceeded.')
            );
        }

        if ($sizeOfCookie > PhpCookieManager::MAX_COOKIE_SIZE) {
            throw new CookieSizeLimitReachedException(
                __('Unable to send the cookie. Size of \'%1\' is %2 bytes.', $name, $sizeOfCookie)
            );
        }
    }

    /**
     * Determines the expiration time of a cookie.
     *
     * @param array $metadataArray
     * @return int in seconds since the Unix epoch.
     */
    private function computeExpirationTime(array $metadataArray)
    {
        if (isset($metadataArray[PhpCookieManager::KEY_EXPIRE_TIME])
            && $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME] < time()
        ) {
            $expireTime = $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME];
        } else {
            if (isset($metadataArray[PublicCookieMetadata::KEY_DURATION])) {
                $expireTime = $metadataArray[PublicCookieMetadata::KEY_DURATION] + time();
            } else {
                $expireTime = self::EXPIRE_AT_END_OF_SESSION_TIME;
            }
        }

        return $expireTime;
    }

    /**
     * Determines the value to be used as a $parameter for the PHP setcookie() function.
     * If the $metadataArray[$parameter] is not set, returns the default value for the given
     * $parameter as specified by php's setcookie() function.
     *
     * @param string $parameter
     * @param array $metadataArray
     * @return string|boolean
     */
    private function getParameterValue($parameter, array $metadataArray)
    {
        if (isset($metadataArray[$parameter])) {
            return $metadataArray[$parameter];
        } else {
            switch ($parameter) {
                case CookieMetadata::KEY_PATH:
                    return '';
                case CookieMetadata::KEY_DOMAIN:
                    return '';
                case PublicCookieMetadata::KEY_SECURE:
                    return false;
                case PublicCookieMetadata::KEY_HTTP_ONLY:
                    return false;
                default:
                    return '';
            }
        }
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

        // explicitly set an expiration time in the metadataArray.
        $metadataArray[PhpCookieManager::KEY_EXPIRE_TIME] = PhpCookieManager::EXPIRE_NOW_TIME;

        // cookie value set to empty string to delete from the remote client
        $this->setCookie($name, '', $metadataArray);
    }
}
