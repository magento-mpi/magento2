<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Product\Widget;

/**
 * Reports Recently Compared Products Widget
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Compared extends \Magento\Reports\Block\Product\Compared implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend(
            'one_column',
            5
        )->addColumnCountLayoutDepend(
            'two_columns_left',
            4
        )->addColumnCountLayoutDepend(
            'two_columns_right',
            4
        )->addColumnCountLayoutDepend(
            'three_columns',
            3
        );
    }
}
