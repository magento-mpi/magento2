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
 * Catalog product controller
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Product extends \Magento\Backend\App\Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('edit');

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $registry = null;

    /**
     * @var \Magento\Core\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var Product\Initialization\Helper
     */
    protected $initializationHelper;

    /**
     * @var Product\Initialization\StockDataFilter
     */
    protected $stockFilter;

    /**
     * @var \Magento\Catalog\Model\Product\Copier
     */
    protected $productCopier;

    /**
     * @param Action\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Filter\Date $dateFilter
     * @param Product\Initialization\Helper $initializationHelper
     * @param Product\Initialization\StockDataFilter $stockFilter
     * @param \Magento\Catalog\Model\Product\Copier $productCopier
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Filter\Date $dateFilter,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter,
        \Magento\Catalog\Model\Product\Copier $productCopier
    ) {
        $this->stockFilter = $stockFilter;
        $this->initializationHelper = $initializationHelper;
        $this->registry = $registry;
        $this->_dateFilter = $dateFilter;
        $this->productCopier = $productCopier;
        parent::__construct($context);
    }

    /**
     * Initialize product from request parameters
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _initProduct()
    {
        $this->_title->add(__('Products'));

        $productId  = (int)$this->getRequest()->getParam('id');
        /** @var $product \Magento\Catalog\Model\Product */
        $product    = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $product->setStoreId($this->getRequest()->getParam('store', 0));

        $typeId = $this->getRequest()->getParam('type');
        if (!$productId && $typeId) {
            $product->setTypeId($typeId);
        }

        $product->setData('_edit_mode', true);
        if ($productId) {
            try {
                $product->load($productId);
            } catch (\Exception $e) {
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }

        $setId = (int)$this->getRequest()->getParam('set');
        if ($setId) {
            $product->setAttributeSetId($setId);
        }

        if ($this->getRequest()->has('attributes')) {
            $attributes = $this->getRequest()->getParam('attributes');
            if (!empty($attributes)) {
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE);
                $this->_objectManager->get('Magento\Catalog\Model\Product\Type\Configurable')
                    ->setUsedProductAttributeIds($attributes, $product);
            } else {
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
            }
        }

        // Required attributes of simple product for configurable creation
        if ($this->getRequest()->getParam('popup')
            && $requiredAttributes = $this->getRequest()->getParam('required')) {
            $requiredAttributes = explode(",", $requiredAttributes);
            foreach ($product->getAttributes() as $attribute) {
                if (in_array($attribute->getId(), $requiredAttributes)) {
                    $attribute->setIsRequired(1);
                }
            }
        }

        if ($this->getRequest()->getParam('popup')
            && $this->getRequest()->getParam('product')
            && !is_array($this->getRequest()->getParam('product'))
            && $this->getRequest()->getParam('id', false) === false
        ) {

            $configProduct = $this->_objectManager->create('Magento\Catalog\Model\Product')
                ->setStoreId(0)
                ->load($this->getRequest()->getParam('product'))
                ->setTypeId($this->getRequest()->getParam('type'));

            /* @var $configProduct \Magento\Catalog\Model\Product */
            $data = array();
            foreach ($configProduct->getTypeInstance()->getEditableAttributes($configProduct) as $attribute) {
                /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                if (!$attribute->getIsUnique()
                    && $attribute->getFrontend()->getInputType() != 'gallery'
                    && $attribute->getAttributeCode() != 'required_options'
                    && $attribute->getAttributeCode() != 'has_options'
                    && $attribute->getAttributeCode() != $configProduct->getIdFieldName()
                ) {
                    $data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
                }
            }
            $product->addData($data)
                ->setWebsiteIds($configProduct->getWebsiteIds());
        }

        $this->registry->register('product', $product);
        $this->registry->register('current_product', $product);
        $this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config')->setStoreId(
            $this->getRequest()->getParam('store')
        );
        return $product;
    }

    /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param \Magento\Backend\Block\Widget\Grid $gridBlock
     * @param array $productsArray
     * @return \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Ajax\Serializer
     */
    protected function _createSerializerBlock(
        $inputName,
        \Magento\Backend\Block\Widget\Grid $gridBlock,
        $productsArray
    ) {
        return $this->_view->getLayout()
            ->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Ajax\Serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName);
    }

    /**
     * Output specified blocks as a text list
     *
     * @return void
     */
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->_view->getLayout()->createBlock('Magento\Backend\Block\Text\ListText');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }

    /**
     * Product list page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Products'));
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_products');
        $this->_view->renderLayout();
    }

    /**
     * Create new product page
     *
     * @return void
     */
    public function newAction()
    {
        if (!$this->getRequest()->getParam('set')) {
            $this->_forward('noroute');
            return;
        }

        $product = $this->_initProduct();

        $productData = $this->getRequest()->getPost('product');
        if ($productData) {
            $stockData = isset($productData['stock_data']) ? $productData['stock_data'] : array();
            $productData['stock_data'] = $this->stockFilter->filter($stockData);
            $product->addData($productData);
        }

        $this->_title->add(__('New Product'));

        $this->_eventManager->dispatch('catalog_product_new_action', array('product' => $product));

        if ($this->getRequest()->getParam('popup')) {
            $this->_view->loadLayout('popup');
        } else {
            $_additionalLayoutPart = '';
            if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE
                && !($product->getTypeInstance()->getUsedProductAttributeIds($product))
            ) {
                $_additionalLayoutPart = '_new';
            }
            $this->_view->loadLayout(array(
                'default',
                strtolower($this->_request->getFullActionName()),
                'catalog_product_' . $product->getTypeId() . $_additionalLayoutPart
            ));
            $this->_setActiveMenu('Magento_Catalog::catalog_products');
        }

        $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->_view->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->_view->renderLayout();
    }

    /**
     * Product edit form
     *
     * @return void
     */
    public function editAction()
    {
        $productId  = (int)$this->getRequest()->getParam('id');
        $product = $this->_initProduct();

        if ($productId && !$product->getId()) {
            $this->messageManager->addError(
                __('This product no longer exists.')
            );
            $this->_redirect('catalog/*/');
            return;
        }

        $this->_title->add($product->getName());

        $this->_eventManager->dispatch('catalog_product_edit_action', array('product' => $product));

        $additionalLayoutPart = '';
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE
           && !($product->getTypeInstance()->getUsedProductAttributeIds($product))
        ) {
            $additionalLayoutPart = '_new';
        }

        $this->_view->loadLayout(array(
            'default',
            strtolower($this->_request->getFullActionName()),
            'catalog_product_'.$product->getTypeId() . $additionalLayoutPart
        ));

        $this->_setActiveMenu('Magento_Catalog::catalog_products');

        if (
            !$this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')->isSingleStoreMode()
            && ($switchBlock = $this->_view->getLayout()->getBlock('store_switcher'))
        ) {
            $switchBlock->setDefaultStoreName(__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl('catalog/*/*', array(
                        '_current' => true,
                        'active_tab' => null,
                        'tab' => null,
                        'store' => null
            )));
        }

        $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->_view->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->_view->renderLayout();
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     * @return void
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore($storeId)->getBaseUrl(\Magento\UrlInterface::URL_TYPE_MEDIA);

        $content = $this->_view->getLayout()->createBlock(
            'Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg\Content',
            '',
            array(
                'data' => array(
                    'editor_element_id' => $elementId,
                    'store_id'          => $storeId,
                    'store_media_url'   => $storeMediaUrl,
                )
            )
        );
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Product grid for AJAX request
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Get specified tab grid
     *
     * @return void
     */
    public function gridOnlyAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();

        $block = $this->getRequest()->getParam('gridOnlyBlock');
        $blockClassSuffix = str_replace(' ', '_', ucwords(str_replace('_', ' ', $block)));

        $this->getResponse()->setBody(
            $this->_view->getLayout()
                ->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\\' . $blockClassSuffix)
                ->toHtml()
        );
    }

    /**
     * Generate product variations matrix
     *
     * @return void
     */
    public function generateVariationsAction()
    {
        $this->_saveAttributeOptions();
        $this->initializationHelper->initialize($this->_initProduct());
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Save attribute options just created by user
     *
     * @TODO Move this logic to configurable product type model
     *   when full set of operations for attribute options during
     *   product creation will be implemented: edit labels, remove, reorder.
     * Currently only addition of options to end and removal of just added option is supported.
     *
     * @return void
     */
    protected function _saveAttributeOptions()
    {
        $productData = (array)$this->getRequest()->getParam('product');
        if (!isset($productData['configurable_attributes_data'])) {
            return;
        }

        foreach ($productData['configurable_attributes_data'] as &$attributeData) {
            $values = array();
            foreach ($attributeData['values'] as $valueId => $priceData) {
                if (isset($priceData['label'])) {
                    /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                    $attribute = $this->_objectManager->create('Magento\Catalog\Model\Resource\Eav\Attribute');
                    $attribute->load($attributeData['attribute_id']);
                    $optionsBefore = $attribute->getSource()->getAllOptions(false);

                    $attribute->setOption(array(
                        'value' => array('option_0' => array($priceData['label'])),
                        'order' => array('option_0' => count($optionsBefore) + 1),
                    ));
                    $attribute->save();

                    /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                    $attribute = $this->_objectManager->create('Magento\Catalog\Model\Resource\Eav\Attribute');
                    $attribute->load($attributeData['attribute_id']);
                    $optionsAfter = $attribute->getSource()->getAllOptions(false);

                    $newOption = array_pop($optionsAfter);

                    unset($priceData['label']);
                    $valueId = $newOption['value'];
                    $priceData['value_index'] = $valueId;
                }
                $values[$valueId] = $priceData;
            }
            $attributeData['values'] = $values;
        }

        $this->getRequest()->setParam('product', $productData);
    }

    /**
     * Get categories fieldset block
     *
     * @return void
     */
    public function categoriesAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Get options fieldset block
     *
     * @return void
     */
    public function optionsAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Get related products grid and serializer block
     *
     * @return void
     */
    public function relatedAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->_view->renderLayout();
    }

    /**
     * Get upsell products grid and serializer block
     *
     * @return void
     */
    public function upsellAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.upsell')
            ->setProductsUpsell($this->getRequest()->getPost('products_upsell', null));
        $this->_view->renderLayout();
    }

    /**
     * Get crosssell products grid and serializer block
     *
     * @return void
     */
    public function crosssellAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.crosssell')
            ->setProductsCrossSell($this->getRequest()->getPost('products_crosssell', null));
        $this->_view->renderLayout();
    }

    /**
     * Get related products grid
     *
     * @return void
     */
    public function relatedGridAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->_view->renderLayout();
    }

    /**
     * Get upsell products grid
     *
     * @return void
     */
    public function upsellGridAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.upsell')
            ->setProductsRelated($this->getRequest()->getPost('products_upsell', null));
        $this->_view->renderLayout();
    }

    /**
     * Get crosssell products grid
     *
     * @return void
     */
    public function crosssellGridAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.crosssell')
            ->setProductsRelated($this->getRequest()->getPost('products_crosssell', null));
        $this->_view->renderLayout();
    }

    /**
     * Get product reviews grid
     *
     * @return void
     */
    public function reviewsAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('admin.product.reviews')
            ->setProductId($this->registry->registry('product')->getId())
            ->setUseAjax(true);
        $this->_view->renderLayout();
    }

    /**
     * Get super config grid
     *
     * @return void
     */
    public function superConfigAction()
    {
        $this->_initProduct();
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Validate product
     *
     * @return void
     */
    public function validateAction()
    {
        $response = new \Magento\Object();
        $response->setError(false);

        try {
            $productData = $this->getRequest()->getPost('product');

            if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
                $productData['stock_data']['use_config_manage_stock'] = 0;
            }
            /* @var $product \Magento\Catalog\Model\Product */
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
            $product->setData('_edit_mode', true);
            $storeId = $this->getRequest()->getParam('store');
            if ($storeId) {
                $product->setStoreId($storeId);
            }
            $setId = $this->getRequest()->getParam('set');
            if ($setId) {
                $product->setAttributeSetId($setId);
            }
            $typeId = $this->getRequest()->getParam('type');
            if ($typeId) {
                $product->setTypeId($typeId);
            }
            $productId = $this->getRequest()->getParam('id');
            if ($productId) {
                $product->load($productId);
            }

            $dateFieldFilters = array();
            $attributes = $product->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $productData) && $productData[$attrKey] != '') {
                        $dateFieldFilters[$attrKey] = $this->_dateFilter;
                    }
                }
            }
            $inputFilter = new \Zend_Filter_Input($dateFieldFilters, array(), $productData);
            $productData = $inputFilter->getUnescaped();
            $product->addData($productData);

            /* set restrictions for date ranges */
            $resource = $product->getResource();
            $resource->getAttribute('special_from_date')
                ->setMaxValue($product->getSpecialToDate());
            $resource->getAttribute('news_from_date')
                ->setMaxValue($product->getNewsToDate());
            $resource->getAttribute('custom_design_from')
                ->setMaxValue($product->getCustomDesignTo());

            $variationProducts = (array)$this->getRequest()->getPost('variations-matrix');
            if ($variationProducts) {
                $validationResult = $this->_validateProductVariations($product, $variationProducts);
                if (!empty($validationResult)) {
                    $response->setError(true)
                        ->setMessage(__('Some product variations fields are not valid.'))
                        ->setAttributes($validationResult);
                }
            }
            $product->validate();
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (\Magento\Core\Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_view->getLayout()->initMessages();
            $response->setError(true);
            $response->setMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Product variations attributes validation
     *
     * @param \Magento\Catalog\Model\Product $parentProduct
     * @param array $products
     * @return array
     */
    protected function _validateProductVariations($parentProduct, array $products)
    {
        $this->_eventManager->dispatch(
            'catalog_product_validate_variations_before',
            array('product' => $parentProduct, 'variations' => $products)
        );
        $validationResult = array();
        foreach ($products as $productData) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
            $product->setData('_edit_mode', true);
            $storeId = $this->getRequest()->getParam('store');
            if ($storeId) {
                $product->setStoreId($storeId);
            }
            $product->setAttributeSetId($parentProduct->getAttributeSetId());

            $product->addData($productData);
            $product->setCollectExceptionMessages(true);
            $configurableAttribute = $this->_objectManager->get('Magento\Core\Helper\Data')
                ->jsonDecode($productData['configurable_attribute']);
            $configurableAttribute = implode('-', $configurableAttribute);

            $errorAttributes = $product->validate();
            if (is_array($errorAttributes)) {
                foreach ($errorAttributes as $attributeCode => $result) {
                    if (is_string($result)) {
                        $key = 'variations-matrix-' . $configurableAttribute . '-' . $attributeCode;
                        $validationResult[$key] = $result;
                    }
                }
            }
        }

        return $validationResult;
    }

    /**
     * Save product action
     *
     * @return void
     */
    public function saveAction()
    {
        $storeId = $this->getRequest()->getParam('store');
        $redirectBack = $this->getRequest()->getParam('back', false);
        $productId = $this->getRequest()->getParam('id');
        $isEdit = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            $product = $this->initializationHelper->initialize($this->_initProduct());
            $this->_eventManager->dispatch(
                'catalog_product_transition_product_type',
                array('product' => $product, 'request' => $this->getRequest())
            );

            try {
                if (isset($data['product'][$product->getIdFieldName()])) {
                    throw new \Magento\Core\Exception(__('Unable to save product'));
                }

                $originalSku = $product->getSku();
                $product->save();
                $productId = $product->getId();

                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $this->_objectManager->create('Magento\Catalog\Model\Product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                $this->_objectManager->create('Magento\CatalogRule\Model\Rule')->applyAllRulesToProduct($productId);

                $this->messageManager->addSuccess(__('You saved the product.'));
                if ($product->getSku() != $originalSku) {
                    $this->messageManager->addNotice(__('SKU for product %1 has been changed to %2.',
                            $this->_objectManager->get('Magento\Escaper')->escapeHtml($product->getName()),
                            $this->_objectManager->get('Magento\Escaper')->escapeHtml($product->getSku()))
                    );
                }

                $this->_eventManager->dispatch(
                    'controller_action_catalog_product_save_entity_after',
                    array('controller' => $this)
                );

                if ($redirectBack === 'duplicate') {
                    $newProduct = $this->productCopier->copy($product);
                    $this->messageManager->addSuccess(__('You duplicated the product.'));
                }

            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setProductData($data);
                $redirectBack = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack === 'new') {
            $this->_redirect('catalog/*/new', array(
                'set'  => $product->getAttributeSetId(),
                'type' => $product->getTypeId()
            ));
        } elseif ($redirectBack === 'duplicate' && isset($newProduct)) {
            $this->_redirect(
                'catalog/*/edit',
                array(
                    'id' => $newProduct->getId(),
                    'back' => null,
                    '_current' => true
                )
            );
        } elseif ($redirectBack) {
            $this->_redirect('catalog/*/edit', array(
                'id'       => $productId,
                '_current' => true
            ));
        } elseif ($this->getRequest()->getParam('popup')) {
            $this->_redirect('catalog/*/created', array(
                '_current' => true,
                'id'       => $productId,
                'edit'     => $isEdit
            ));
        } else {
            $this->_redirect('catalog/*/', array('store'=>$storeId));
        }
    }

    /**
     * Create product duplicate
     *
     * @return void
     */
    public function duplicateAction()
    {
        $product = $this->_initProduct();
        try {
            $newProduct = $this->productCopier->copy($product);
            $this->messageManager->addSuccess(__('You duplicated the product.'));
            $this->_redirect('catalog/*/edit', array('_current'=>true, 'id'=>$newProduct->getId()));
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('catalog/*/edit', array('_current'=>true));
        }
    }

    /**
     * Get alerts price grid
     *
     * @return void
     */
    public function alertsPriceGridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Get alerts stock grid
     *
     * @return void
     */
    public function alertsStockGridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function addAttributeAction()
    {
        $this->_view->loadLayout('popup');
        $this->_initProduct();
        $this->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Attribute\NewAttribute\Product\Created')
        );
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function createdAction()
    {
        $this->_view->loadLayout('popup');
        $this->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Created')
        );
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->messageManager->addError(__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productId);
                        $product->delete();
                    }
                    $this->messageManager->addSuccess(
                        __('A total of %1 record(s) have been deleted.', count($productIds))
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('catalog/*/index');
    }

    /**
     * Update product(s) status action
     *
     * @return void
     */
    public function massStatusAction()
    {
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            $this->_objectManager->get('Magento\Catalog\Model\Product\Action')
                ->updateAttributes($productIds, array('status' => $status), $storeId);

            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been updated.', count($productIds))
            );
        } catch (\Magento\Core\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()
                ->addException($e, __('Something went wrong while updating the product(s) status.'));
        }

        $this->_redirect('catalog/*/', array('store'=> $storeId));
    }

    /**
     * Validate batch of products before theirs status will be set
     *
     * @param array $productIds
     * @param int $status
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        if ($status == \Magento\Catalog\Model\Product\Status::STATUS_ENABLED) {
            if (!$this->_objectManager->create('Magento\Catalog\Model\Product')->isProductsHasSku($productIds)) {
                throw new \Magento\Core\Exception(
                    __('Please make sure to define SKU values for all processed products.')
                );
            }
        }
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }

    /**
     * Show item update result from updateAction
     * in Wishlist and Cart controllers.
     *
     * @return bool
     */
    public function showUpdateResultAction()
    {
        $session = $this->_objectManager->get('Magento\Backend\Model\Session');
        if ($session->hasCompositeProductResult()
            && $session->getCompositeProductResult() instanceof \Magento\Object) {
            $this->_objectManager->get('Magento\Catalog\Helper\Product\Composite')
                ->renderUpdateResult($session->getCompositeProductResult());
            $session->unsCompositeProductResult();
        } else {
            $session->unsCompositeProductResult();
            return false;
        }
    }

    /**
     * Show product grid for custom options import popup
     *
     * @return void
     */
    public function optionsImportGridAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Show custom options in JSON format for specified products
     *
     * @return void
     */
    public function customOptionsAction()
    {
        $this->registry->register('import_option_products', $this->getRequest()->getPost('products'));
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Action for product template selector
     *
     * @return void
     */
    public function suggestProductTemplatesAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
            $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Product\TemplateSelector')
                ->getSuggestedTemplates($this->getRequest()->getParam('label_part'))
        ));
    }

    /**
     * Search for attributes by part of attribute's label in admin store
     *
     * @return void
     */
    public function suggestAttributesAction()
    {
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
            $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes\Search')
                ->getSuggestedAttributes($this->getRequest()->getParam('label_part'))
        ));
    }

    /**
     * Add attribute to product template
     *
     * @return void
     */
    public function addAttributeToTemplateAction()
    {
        $request = $this->getRequest();
        try {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute')
                ->load($request->getParam('attribute_id'));

            $attributeSet = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
                ->load($request->getParam('template_id'));

            /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection $attributeGroupCollection */
            $attributeGroupCollection = $this->_objectManager
                ->get('Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection');
            $attributeGroupCollection->setAttributeSetFilter($attributeSet->getId());
            $attributeGroupCollection->addFilter('attribute_group_code', $request->getParam('group'));
            $attributeGroupCollection->setPageSize(1);

            $attributeGroup = $attributeGroupCollection->getFirstItem();

            $attribute->setAttributeSetId($attributeSet->getId())->loadEntityAttributeIdBySet();

            $attribute->setEntityTypeId($attributeSet->getEntityTypeId())
                ->setAttributeSetId($request->getParam('template_id'))
                ->setAttributeGroupId($attributeGroup->getId())
                ->setSortOrder('0')
                ->save();

            $this->getResponse()->setBody($attribute->toJson());
        } catch (\Exception $e) {
            $response = new \Magento\Object();
            $response->setError(false);
            $response->setMessage($e->getMessage());
            $this->getResponse()->setBody($response->toJson());
        }
    }
}
