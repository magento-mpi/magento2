<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogProductSetEdit
 * Product Set edit page
 */
class CatalogProductSetEdit extends BackendPage
{
    const MCA = 'catalog/product_set/edit';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'main' => [
            'name' => 'main',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main',
            'locator' => '.attribute-set',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main
     */
    public function getMain()
    {
        return $this->getBlockInstance('main');
    }
}
