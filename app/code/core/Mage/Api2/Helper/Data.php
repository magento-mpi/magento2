<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Webservice API2 data helper
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Request interpret adapters
     */
    const XML_PATH_API2_REQUEST_INTERPRETERS = 'global/api2/request/interpreters';

    /**#@+
     * Config paths
     */
    const XML_PATH_AUTH_ADAPTERS = 'global/api2/auth_adapters';
    const XML_PATH_USER_TYPES    = 'global/api2/user_types';
    /**#@- */

    /**
     * Compare order to be used in adapters list sort
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    protected static function _compareOrder($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }
        return ($a['order'] < $b['order']) ? -1 : 1;
    }

    /**
     * Retrieve Auth adapters info from configuration file as array
     *
     * @param bool $enabledOnly
     * @return array
     */
    public function getAuthAdapters($enabledOnly = false)
    {
        $adapters = Mage::getConfig()->getNode(self::XML_PATH_AUTH_ADAPTERS);

        if (!$adapters) {
            return array();
        }
        $adapters = $adapters->asArray();

        if ($enabledOnly) {
            foreach ($adapters as $adapter) {
                if (empty($adapter['enabled'])) {
                    unset($adapters);
                }
            }
            $adapters = (array) $adapters;
        }
        uasort($adapters, array('Mage_Api2_Helper_Data', '_compareOrder'));

        return $adapters;
    }

    /**
     * Retrieve enabled user types in form of user type => user model pairs
     *
     * @return array
     */
    public function getUserTypes()
    {
        $typesModels = array();
        $types = Mage::getConfig()->getNode(self::XML_PATH_USER_TYPES);

        if ($types) {
            foreach ($types->asArray() as $type => $params) {
                if (!empty($type['allowed'])) {
                    $typesModels[$type] = $params['model'];
                }
            }
        }
        return $typesModels;
    }

    /**
     * Format XML data to array form
     *
     * @deprecated
     * @static
     * @param SimpleXMLElement $xml
     * @param string $keyTrimmer
     * @return array|string
     */
    public static function simpleXmlToArray($xml, $keyTrimmer = null)
    {
        $result = array();

        $isTrimmed = false;
        if (null !== $keyTrimmer){
            $isTrimmed = true;
        }

        if (is_object($xml)){
            foreach (get_object_vars($xml->children()) as $key => $node)
            {
                $arrKey = $key;
                if ($isTrimmed){
                    $arrKey = str_replace($keyTrimmer, '', $key);//, &$isTrimmed);
                }
                if (is_numeric($arrKey)){
                    $arrKey = 'Obj' . $arrKey;
                }
                if (is_object($node)){
                    $result[$arrKey] = self::simpleXmlToArray($node, $keyTrimmer);
                } elseif(is_array($node)){
                    $result[$arrKey] = array();
                    foreach($node as $node_key => $node_value){
                        $result[$arrKey][] = self::simpleXmlToArray($node_value, $keyTrimmer);
                    }
                } else {
                    $result[$arrKey] = (string) $node;
                }
            }
        } else {
            $result = (string) $xml;
        }
        return $result;
    }

    /**
     * Get renderer type most preferred and acceptable by client
     *
     * @throw Mage_Api2_Exception 406
     * @param Mage_Api2_Model_Request $request
     * @return string
     */
    public function getRendererType(Mage_Api2_Model_Request $request)
    {
        /** @var $config Mage_Api2_Model_Config */
        $config = Mage::getModel('api2/config');

        $found = null;
        foreach ($types = $request->getAcceptTypes() as $accepted) {
            foreach ($config->getMimeTypesMappingForResponse() as $supported=>$path) {
                if ($accepted==$supported || $accepted=='*/*' || $accepted==current(explode('/', $supported)).'/*') {
                    $found = $path;
                    break(2);
                }
            }
        }

        //if server can't respond in any of accepted types it SHOULD send 406(not acceptable)
        if ($found===null) {
            throw new Mage_Api2_Exception(
                sprintf(
                    'Invalid media types in Accept HTTP header "%s".',
                    htmlspecialchars($request->getHeader('Accept'))
                ),
                Mage_Api2_Model_Server::HTTP_NOT_ACCEPTABLE
            );
        }

        return $found;
    }

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    public function getRequestInterpreterAdapters()
    {
        $adapters = (array) Mage::app()->getConfig()->getNode(self::XML_PATH_API2_REQUEST_INTERPRETERS);
        return $adapters;
    }
}
