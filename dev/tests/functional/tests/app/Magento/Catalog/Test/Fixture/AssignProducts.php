<?php
/**
 * {license_notice}
 *
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

    protected $group = '';

    /**
     * {@inheritdoc}
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders[$this->assignType . '_simple::getSku'] = array($this, 'productProvider');
        $this->_placeholders[$this->assignType . '_simple::getName'] = array($this, 'productProvider');
        $this->_placeholders[$this->assignType . '_configurable::getSku'] = array($this, 'productProvider');
        $this->_placeholders[$this->assignType . '_configurable::getName'] = array($this, 'productProvider');
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
                            'sku' => '%' . $this->assignType . '_simple::getSku%',
                            'name' => '%' . $this->assignType . '_simple::getName%'
                        ),
                        'product_2' => array(
                            'sku' => '%' . $this->assignType . '_configurable::getSku%',
                            'name' => '%' . $this->assignType . '_configurable::getName%'
                        )
                    ),
                    'group' => $this->group
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
