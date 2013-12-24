<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;

class AssignProducts extends Product
{
    protected $assignType = '';

    /**
     * {@inheritdoc}
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders[$this->assignType . '_simple::getProductSku'] = array($this, 'productProvider');
        $this->_placeholders[$this->assignType . '_simple::getProductName'] = array($this, 'productProvider');
        $this->_placeholders[$this->assignType . '_configurable::getProductSku'] = array($this, 'productProvider');
        $this->_placeholders[$this->assignType . '_configurable::getProductName'] = array($this, 'productProvider');
    }

    /**
     * Init Data
     */
    protected function _initData()
    {
        $this->_dataConfig = array(
            'assignType ' => $this->assignType,
        );
        /** @var  $type Related|Upsell */
        $type = 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\\' . ucfirst(strtolower($this->assignType));
        $this->_data = array(
            'fields' => array(
                $this->assignType . '_products' => array(
                    'value' => array(
                        'product_1' => array(
                            'sku' => '%' . $this->assignType . '_simple::getProductSku%',
                            'name' => '%' . $this->assignType . '_simple::getProductName%'
                        ),
                        'product_2' => array(
                            'sku' => '%' . $this->assignType . '_configurable::getProductSku%',
                            'name' => '%' . $this->assignType . '_configurable::getProductName%'
                        )
                    ),
                    'group' => $type::GROUP
                )
            ),
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogAssignProducts($this->_dataConfig, $this->_data);
    }

    /**
     * @param string $productData
     * @return string
     */
    protected function formatProductType($productData)
    {
        return str_replace($this->assignType . '_', '', $productData);
    }
}
