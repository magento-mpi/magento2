<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Launcher test transaction controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_TesttransactionController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mage_Checkout_Model_Cart
     */
    protected $_cartModel;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_productModel;

    /**
     * @var Mage_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Checkout_Model_Cart $cartModel
     * @param Mage_Catalog_Model_Product $productModel
     * @param Mage_Checkout_Model_Session $checkoutSession
     * @param string $areaCode
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Checkout_Model_Cart $cartModel,
        Mage_Catalog_Model_Product $productModel,
        Mage_Checkout_Model_Session $checkoutSession,
        $areaCode = null
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode);

        $this->_cartModel = $cartModel;
        $this->_productModel = $productModel;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Add product to cart if needed and redirect to Shopping Cart page
     *
     * @return null
     */
    public function indexAction()
    {
        if (!$this->_cartModel->getQuote()->getItemsCount()) {
            $products = $this->_productModel->getResourceCollection()
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addFieldToFilter('type_id', array(
                    Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
                    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
                ));
            if ($products->count()) {
                $productId = $products->addAttributeToSort('entity_id', 'ASC')->getFirstItem()->getId();
                $product = $this->_productModel->load($productId);
                $this->_cartModel->addProduct($product, 1);
                $this->_cartModel->save();
                $this->_checkoutSession->setCartWasUpdated(true);
            }
        }
        $this->_redirect('checkout/cart');
    }
}
