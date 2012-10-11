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
 * Request content interpreter factory.
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Webapi_Controller_Request_Interpreter
{
    /**
     * Request body interpreters factory.
     *
     * @param string $type
     * @return Mage_Webapi_Controller_Request_InterpreterInterface
     * @throws LogicException|Mage_Webapi_Exception
     */
    public static function factory($type)
    {
        /** @var $helper Mage_Webapi_Helper_Rest */
        $helper = Mage::helper('Mage_Webapi_Helper_Rest');
        $adapters = $helper->getRequestInterpreterAdapters();

        if (empty($adapters) || !is_array($adapters)) {
            throw new LogicException('Request interpreter adapter is not set.');
        }

        $adapterModel = null;
        foreach ($adapters as $item) {
            $itemType = (string)$item->type;
            if ($itemType == $type) {
                $adapterModel = (string)$item->model;
                break;
            }
        }

        if ($adapterModel === null) {
            throw new Mage_Webapi_Exception(
                sprintf('Server can not understand Content-Type HTTP header media type "%s"', $type),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }

        $adapter = Mage::getModel($adapterModel);
        if (!$adapter) {
            throw new LogicException(sprintf('Request interpreter adapter "%s" not found.', $type));
        }

        return $adapter;
    }
}
