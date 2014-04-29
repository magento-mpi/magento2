<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product qty increments block
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Block;

class Qtyincrements extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Block\IdentityInterface
{
    /**
     * Qty Increments cache
     *
     * @var float|false
     */
    protected $_qtyIncrements;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve current product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * Retrieve product qty increments
     *
     * @return float|false
     */
    public function getProductQtyIncrements()
    {
        if ($this->_qtyIncrements === null) {
            $this->_qtyIncrements = $this->getProduct()->getStockItem()->getQtyIncrements();
            if (!$this->getProduct()->isSaleable()) {
                $this->_qtyIncrements = false;
            }
        }
        return $this->_qtyIncrements;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->getProduct()->getIdentities();
    }
}
