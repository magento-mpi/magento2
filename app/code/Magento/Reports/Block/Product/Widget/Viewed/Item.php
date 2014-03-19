<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Product\Widget\Viewed;

/**
 * Reports Recently Viewed Products Widget
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Viewed Product Index type
     *
     * @var string
     */
    protected $_indexType = \Magento\Reports\Model\Product\Index\Factory::TYPE_VIEWED;

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addPriceBlockType(
            'bundle',
            'Magento\Bundle\Block\Catalog\Product\Price',
            'catalog/product/price.phtml'
        );
    }
}
