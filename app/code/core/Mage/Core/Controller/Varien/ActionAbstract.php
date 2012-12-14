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
 * Abstract redirect/forward action class
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Controller_Varien_ActionAbstract implements Mage_Core_Controller_Varien_DispatchableInterface
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * @var string
     */
    protected $_currentArea;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param string $areaCode
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        $areaCode = null
    ) {
        $this->_request     = $request;
        $this->_response    = $response;
        $this->_currentArea = $areaCode;
    }
}
