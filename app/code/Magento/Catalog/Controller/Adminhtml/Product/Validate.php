<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

class Validate extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var \Magento\Catalog\Model\Product\Validator
     */
    protected $productValidator;

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Catalog\Model\Product\Validator $productValidator
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Catalog\Model\Product\Validator $productValidator
    ) {
        $this->_dateFilter = $dateFilter;
        $this->productValidator = $productValidator;
        parent::__construct($context, $productBuilder);
    }

    /**
     * Validate product
     *
     * @return void
     */
    public function execute()
    {
        $response = new \Magento\Framework\Object();
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
            $resource->getAttribute('special_from_date')->setMaxValue($product->getSpecialToDate());
            $resource->getAttribute('news_from_date')->setMaxValue($product->getNewsToDate());
            $resource->getAttribute('custom_design_from')->setMaxValue($product->getCustomDesignTo());

            $this->productValidator->validate($product, $this->getRequest(), $response);
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (\Magento\Framework\Model\Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_view->getLayout()->initMessages();
            $response->setError(true);
            $response->setHtmlMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->representJson($response->toJson());
    }
}
