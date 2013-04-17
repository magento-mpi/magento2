<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_CategoryController extends Mage_Core_Controller_Front_Action
{
    /**
     * Category view action
     */
    public function viewAction()
    {
        try {
            $this->_initLayoutMessages('Mage_Catalog_Model_Session');
            $this->_initLayoutMessages('Mage_Checkout_Model_Session');

            $serviceManager = Mage::getSingleton('Mage_Core_Service_Manager');

            // if we follow convention to have entity aliases for id fields,
            // we may extract all data from request within services as a default behaviour
            $categoryId = (int)$this->getRequest()->getParam('id', false);

            $serviceManager->getService('Mage_Catalog_Service_Process_Category')
                ->call('view', array(
                    'controller_action'     => $this, // [backward compatibility]
                    'response'              => $this->getResponse(), // [use response as a container for output]
                    'entity_id'             => $categoryId,
                    'store_id'              => Mage::app()->getStore()->getId(),
                    'current_area'          => $this->_currentArea,
                    'default_layout_handle' => $this->getDefaultLayoutHandle()
                ));
        } catch (Core_Service_Exception $e) {
            // @todo what?
            if (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
        }
    }
}
