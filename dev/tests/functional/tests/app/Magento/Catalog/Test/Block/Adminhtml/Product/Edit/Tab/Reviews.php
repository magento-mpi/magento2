<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Reviews
 * Reviews tab
 */
class Reviews extends Tab
{
    public function getGrid()
    {
        return $this->blockFactory->create(
            'Magento\Review\Test\Block\Adminhtml\Grid',
            ['element' => $this->_rootElement]
        );
    }
}
