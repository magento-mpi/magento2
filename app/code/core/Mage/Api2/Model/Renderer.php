<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice API2 renderer factory model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Renderer
{
    /**
     * Get Renderer of given type
     *
     * @param array|string $acceptTypes
     * @throws Mage_Api2_Exception
     * @throws Exception
     * @return Mage_Api2_Model_Renderer_Interface
     */
    public static function factory($acceptTypes)
    {
        /** @var $helper Mage_Api2_Helper_Data */
        $helper   = Mage::helper('Mage_Api2_Helper_Data');
        $adapters = $helper->getResponseRenderAdapters();

        if (!is_array($acceptTypes)) {
            $acceptTypes = array($acceptTypes);
        }

        $type = null;
        $adapterPath = null;
        foreach ($acceptTypes as $type) {
            foreach ($adapters as $item) {
                $itemType = (string) $item->type;
                if ($type == $itemType
                    || $type == current(explode('/', $itemType)) . '/*' || $type == '*/*'
                ) {
                    $adapterPath = (string) $item->model;
                    break 2;
                }
            }
        }

        //if server can't respond in any of accepted types it SHOULD send 406(not acceptable)
        if (null === $adapterPath) {
            throw new Mage_Api2_Exception(
                'Server can not understand Accept HTTP header media type.',
                Mage_Api2_Controller_Front_Rest::HTTP_NOT_ACCEPTABLE
            );
        }

        $adapter = Mage::getModel($adapterPath);
        if (!$adapter) {
            throw new Exception(sprintf('Response renderer adapter for content type "%s" not found.', $type));
        }

        return $adapter;
    }
}
