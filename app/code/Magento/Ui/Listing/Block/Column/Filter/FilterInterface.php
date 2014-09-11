<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Filter;

use Magento\Ui\Listing\Block\Column;

/**
 * Grid column filter interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface FilterInterface
{
    /**
     * @return Column
     */
    public function getColumn();

    /**
     * @param Column $column
     * @return AbstractFilter
     */
    public function setColumn($column);

    /**
     * @return string
     */
    public function getHtml();
}
