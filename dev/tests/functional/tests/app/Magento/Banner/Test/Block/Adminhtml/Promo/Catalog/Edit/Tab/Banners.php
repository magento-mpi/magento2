<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block\Adminhtml\Promo\Catalog\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Banners
 * Banners grid tab
 */
class Banners extends Tab
{
    /**
     * Banners grid locator
     *
     * @var string
     */
    protected $bannersGrid = '#edit_form';

    /**
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
