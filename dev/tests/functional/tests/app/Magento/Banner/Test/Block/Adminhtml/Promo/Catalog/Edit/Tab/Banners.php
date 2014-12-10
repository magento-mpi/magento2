<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block\Adminhtml\Promo\Catalog\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Banners
 * 'Related Banners' tab on Cart Price Rule form
 */
class Banners extends Tab
{
    /**
     * Banners grid locator
     *
     * @var string
     */
    protected $bannersGrid = '#related_catalogrule_banners_grid';

    /**
     * Get banners grid on Catalog Price Rules form
     *
     * @return \Magento\Banner\Test\Block\Adminhtml\Promo\Catalog\Edit\Tab\BannersGrid
     */
    public function getBannersGrid()
    {
        return $this->blockFactory->create(
            'Magento\Banner\Test\Block\Adminhtml\Promo\Catalog\Edit\Tab\BannersGrid',
            ['element' => $this->_rootElement->find($this->bannersGrid)]
        );
    }
}
