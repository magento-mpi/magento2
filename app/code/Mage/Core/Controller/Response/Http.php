<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Custom Zend_Controller_Response_Http class (formally)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Controller_Response_Http extends Zend_Controller_Response_Http
{
    /**
     * Transport object for observers to perform
     *
     * @var Varien_Object
     */
    protected static $_transportObject = null;

    /**
     * Fixes CGI only one Status header allowed bug
     *
     * @link  http://bugs.php.net/bug.php?id=36705
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function sendHeaders()
    {
        if (!$this->canSendHeaders()) {
            Mage::log('HEADERS ALREADY SENT: '.mageDebugBacktrace(true, true, true));
            return $this;
        }

        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            $statusSent = false;
            foreach ($this->_headersRaw as $index => $header) {
                if (stripos($header, 'status:')===0) {
                    if ($statusSent) {
                        unset($this->_headersRaw[$index]);
                    } else {
                        $statusSent = true;
                    }
                }
            }
            foreach ($this->_headers as $index => $header) {
                if (strcasecmp($header['name'], 'status') === 0) {
                    if ($statusSent) {
                        unset($this->_headers[$index]);
                    } else {
                        $statusSent = true;
                    }
                }
            }
        }
        return parent::sendHeaders();
    }

    public function sendResponse()
    {
        return parent::sendResponse();
    }

    /**
     * Additionally check for session messages in several domains case
     *
     * @param string $url
     * @param int $code
     * @return Mage_Core_Controller_Response_Http
     */
    public function setRedirect($url, $code = 302)
    {
        /**
         * Use single transport object instance
         */
        if (self::$_transportObject === null) {
            self::$_transportObject = new Varien_Object;
        }
        self::$_transportObject->setUrl($url);
        self::$_transportObject->setCode($code);
        Mage::dispatchEvent('controller_response_redirect',
                array('response' => $this, 'transport' => self::$_transportObject));

        return parent::setRedirect(self::$_transportObject->getUrl(), self::$_transportObject->getCode());
    }
}
