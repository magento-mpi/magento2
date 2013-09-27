<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for Catalog product and category URL rewrites
 *
 * @method Magento_Catalog_Model_Product getProduct()
 * @method Magento_Catalog_Model_Category getCategory()
 * @method Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form setProduct(Magento_Catalog_Model_Product $product)
 * @method Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form setCategory(Magento_Catalog_Model_Category $category)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form extends Magento_Adminhtml_Block_Urlrewrite_Edit_Form
{
    /**
     * @var Magento_Catalog_Model_Url
     */
    protected $_catalogUrl;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_Catalog_Model_Url $catalogUrl
     * @param Magento_Core_Model_Source_Urlrewrite_TypesFactory $typesFactory
     * @param Magento_Core_Model_Source_Urlrewrite_OptionsFactory $optionFactory
     * @param Magento_Core_Model_Url_RewriteFactory $rewriteFactory
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_Catalog_Model_Url $catalogUrl,
        Magento_Core_Model_Source_Urlrewrite_TypesFactory $typesFactory,
        Magento_Core_Model_Source_Urlrewrite_OptionsFactory $optionFactory,
        Magento_Core_Model_Url_RewriteFactory $rewriteFactory,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Backend_Model_Session $backendSession,
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_catalogUrl = $catalogUrl;
        parent::__construct(
            $typesFactory, $optionFactory, $rewriteFactory, $systemStore, $backendSession, $adminhtmlData, $registry,
            $formFactory, $coreData, $context, $data
        );
    }

    /**
     * Form post init
     *
     * @param Magento_Data_Form $form
     * @return Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form
     */
    protected function _formPostInit($form)
    {
        // Set form action
        $form->setAction(
            $this->_adminhtmlData->getUrl('*/*/save', array(
                'id'       => $this->_getModel()->getId(),
                'product'  => $this->_getProduct()->getId(),
                'category' => $this->_getCategory()->getId()
            ))
        );

        // Fill id path, request path and target path elements
        /** @var $idPath Magento_Data_Form_Element_Abstract */
        $idPath = $this->getForm()->getElement('id_path');
        /** @var $requestPath Magento_Data_Form_Element_Abstract */
        $requestPath = $this->getForm()->getElement('request_path');
        /** @var $targetPath Magento_Data_Form_Element_Abstract */
        $targetPath = $this->getForm()->getElement('target_path');

        $model = $this->_getModel();
        $disablePaths = false;
        if (!$model->getId()) {
            $product = null;
            $category = null;
            if ($this->_getProduct()->getId()) {
                $product = $this->_getProduct();
                $category = $this->_getCategory();
            } elseif ($this->_getCategory()->getId()) {
                $category = $this->_getCategory();
            }

            if ($product || $category) {
                $idPath->setValue($this->_catalogUrl->generatePath('id', $product, $category));

                $sessionData = $this->_getSessionData();
                if (!isset($sessionData['request_path'])) {
                    $requestPath->setValue($this->_catalogUrl->generatePath('request', $product, $category, ''));
                }
                $targetPath->setValue($this->_catalogUrl->generatePath('target', $product, $category));
                $disablePaths = true;
            }
        } else {
            $disablePaths = $model->getProductId() || $model->getCategoryId();
        }

        // Disable id_path and target_path elements
        if ($disablePaths) {
            $idPath->setData('disabled', true);
            $targetPath->setData('disabled', true);
        }

        return $this;
    }

    /**
     * Get catalog entity associated stores
     *
     * @return array
     * @throws Magento_Core_Model_Store_Exception
     */
    protected function _getEntityStores()
    {
        $product = $this->_getProduct();
        $category = $this->_getCategory();
        $entityStores = array();

        // showing websites that only associated to products
        if ($product->getId()) {
            $entityStores = (array) $product->getStoreIds();

            //if category is chosen, reset stores which are not related with this category
            if ($category->getId()) {
                $categoryStores = (array) $category->getStoreIds();
                $entityStores = array_intersect($entityStores, $categoryStores);
            }
            // @codingStandardsIgnoreStart
            if (!$entityStores) {
                throw new Magento_Core_Model_Store_Exception(
                    __('We can\'t set up a URL rewrite because the product you chose is not associated with a website.')
                );
            }
            $this->_requireStoresFilter = true;
        } elseif ($category->getId()) {
            $entityStores = (array) $category->getStoreIds();
            if (!$entityStores) {
                throw new Magento_Core_Model_Store_Exception(
                    __('We can\'t set up a URL rewrite because the category your chose is not associated with a website.')
                );
            }
            $this->_requireStoresFilter = true;
        }
        // @codingStandardsIgnoreEnd

        return $entityStores;
    }

    /**
     * Get product model instance
     *
     * @return Magento_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setProduct($this->_productFactory->create());
        }
        return $this->getProduct();
    }

    /**
     * Get category model instance
     *
     * @return Magento_Catalog_Model_Category
     */
    protected function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory($this->_categoryFactory->create());
        }
        return $this->getCategory();
    }
}
