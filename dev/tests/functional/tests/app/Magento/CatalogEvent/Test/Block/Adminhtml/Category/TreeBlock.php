<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Category;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class TreeTreeBlock
 * Categories tree block
 */
class TreeBlock extends Block
{
    /**
     * Category tree locator
     *
     * @var string
     */
    protected $treeElement = '.x-tree-root-node';

    /**
     * Select Category
     *
     * @param string $path
     * @return void
     */
    public function selectCategory($path)
    {
        $this->_rootElement->find($this->treeElement, Locator::SELECTOR_CSS, 'tree')->setValue($path);
    }
}
