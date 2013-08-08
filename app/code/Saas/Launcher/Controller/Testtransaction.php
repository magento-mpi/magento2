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
class Saas_Launcher_Controller_Testtransaction extends Magento_Core_Controller_Front_Action
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
     * @var Saas_Launcher_Helper_Data
     */
    protected $_launcherHelper;

    /**
     * Constructor
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Mage_Checkout_Model_Cart $cartModel
     * @param Mage_Catalog_Model_Product $productModel
     * @param Mage_Checkout_Model_Session $checkoutSession
     * @param Saas_Launcher_Helper_Data $launcherHelper
     * @param string $areaCode
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Mage_Checkout_Model_Cart $cartModel,
        Mage_Catalog_Model_Product $productModel,
        Mage_Checkout_Model_Session $checkoutSession,
        Saas_Launcher_Helper_Data $launcherHelper,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);

        $this->_cartModel = $cartModel;
        $this->_productModel = $productModel;
        $this->_checkoutSession = $checkoutSession;
        $this->_launcherHelper = $launcherHelper;
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
            } else {
                $this->_checkoutSession->addNotice($this->_launcherHelper->__(
                    'You need to have at least one Simple or Virtual Product to run test transaction.'
                ));
            }
        }
        $this->_redirect('checkout/cart');
    }
}
