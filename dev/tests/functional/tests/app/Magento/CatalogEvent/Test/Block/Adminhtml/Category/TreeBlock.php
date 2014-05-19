<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Category;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class TreeTreeBlock
 * Categories tree block
 *
 */
class TreeBlock extends Block
{
    /**
     * Category tree
     *
     * @var string
     */
    protected $treeElement = '.x-tree-root-node';

    /**
     * Select Category
     *
     * @param string $path
     */
    public function selectCategory($path)
    {
        $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->setValue($path);
    }
}
