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
 * Adminhtml catalog product action attribute update helper
 */
namespace Magento\Adminhtml\Helper\Catalog\Product\Edit\Action;

class Attribute extends \Magento\Backend\Helper\Data
{
    /**
     * Selected products for mass-update
     *
     * @var \Magento\Catalog\Model\Entity\Product\Collection
     */
    protected $_products;

    /**
     * Array of same attributes for selected products
     *
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    protected $_attributes;

    /**
     * Excluded from batch update attribute codes
     *
     * @var array
     */
    protected $_excludedAttributes = array('url_key');

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productsFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productsFactory
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\ConfigInterface $applicationConfig
     * @param \Magento\Core\Model\Config\Primary $primaryConfig
     * @param \Magento\Core\Model\RouterList $routerList
     * @param \Magento\Core\Model\AppInterface $app
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Backend\Model\Auth $auth
     * @param string $defaultAreaFrontName
     * @param string $backendFrontName
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Backend\Model\Session $session,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productsFactory,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\ConfigInterface $applicationConfig,
        \Magento\Core\Model\Config\Primary $primaryConfig,
        \Magento\Core\Model\RouterList $routerList,
        \Magento\Core\Model\AppInterface $app,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Backend\Model\Auth $auth,
        $defaultAreaFrontName,
        $backendFrontName
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_session = $session;
        $this->_productsFactory = $productsFactory;
        parent::__construct(
            $context, $coreData, $applicationConfig, $primaryConfig, $routerList, $app, $backendUrl, $auth,
            $defaultAreaFrontName, $backendFrontName
        );
    }

    /**
     * Return product collection with selected product filter
     * Product collection didn't load
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getProducts()
    {
        if (is_null($this->_products)) {
            $productsIds = $this->getProductIds();

            if (!is_array($productsIds)) {
                $productsIds = array(0);
            }

            $this->_products = $this->_productsFactory->create()
                ->setStoreId($this->getSelectedStoreId())
                ->addIdFilter($productsIds);
        }

        return $this->_products;
    }

    /**
     * Return array of selected product ids from post or session
     *
     * @return array|null
     */
    public function getProductIds()
    {
        if ($this->_getRequest()->isPost() && $this->_getRequest()->getActionName() == 'edit') {
            $this->_session->setProductIds($this->_getRequest()->getParam('product', null));
        }

        return $this->_session->getProductIds();
    }

    /**
     * Return selected store id from request
     *
     * @return integer
     */
    public function getSelectedStoreId()
    {
        return (int)$this->_getRequest()->getParam('store', \Magento\Core\Model\AppInterface::ADMIN_STORE_ID);
    }

    /**
     * Return array of attribute sets by selected products
     *
     * @return array
     */
    public function getProductsSetIds()
    {
        return $this->getProducts()->getSetIds();
    }

    /**
     * Return collection of same attributes for selected products without unique
     *
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes  = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
                ->getAttributeCollection()
                ->addIsNotUniqueFilter()
                ->setInAllAttributeSetsFilter($this->getProductsSetIds());

            if ($this->_excludedAttributes) {
                $this->_attributes->addFieldToFilter('attribute_code', array('nin' => $this->_excludedAttributes));
            }

            // check product type apply to limitation and remove attributes that impossible to change in mass-update
            $productTypeIds  = $this->getProducts()->getProductTypeIds();
            foreach ($this->_attributes as $attribute) {
                /* @var $attribute \Magento\Catalog\Model\Entity\Attribute */
                foreach ($productTypeIds as $productTypeId) {
                    $applyTo = $attribute->getApplyTo();
                    if (count($applyTo) > 0 && !in_array($productTypeId, $applyTo)) {
                        $this->_attributes->removeItemByKey($attribute->getId());
                        break;
                    }
                }
            }
        }

        return $this->_attributes;
    }
}
