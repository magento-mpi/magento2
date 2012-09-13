<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Request content interpreter factory
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Webapi_Model_Request_Interpreter
{
    /**
     * Request body interpreters factory
     *
     * @param string $type
     * @return Mage_Webapi_Model_Request_Interpreter_Interface
     * @throws Exception|Mage_Webapi_Exception
     */
    public static function factory($type)
    {
        /** @var $helper Mage_Webapi_Helper_Data */
        $helper = Mage::helper('Mage_Webapi_Helper_Data');
        $adapters = $helper->getRequestInterpreterAdapters();

        if (empty($adapters) || !is_array($adapters)) {
            throw new Exception('Request interpreter adapters is not set.');
        }

        $adapterModel = null;
        foreach ($adapters as $item) {
            $itemType = (string) $item->type;
            if ($itemType == $type) {
                $adapterModel = (string) $item->model;
                break;
            }
        }

        if ($adapterModel === null) {
            throw new Mage_Webapi_Exception(
                sprintf('Server can not understand Content-Type HTTP header media type "%s"', $type),
                Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST
            );
        }

        $adapter = Mage::getModel($adapterModel);
        if (!$adapter) {
            throw new Exception(sprintf('Request interpreter adapter "%s" not found.', $type));
        }

        return $adapter;
    }
}
