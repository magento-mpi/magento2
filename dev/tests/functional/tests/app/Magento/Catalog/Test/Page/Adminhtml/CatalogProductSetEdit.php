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
 */
class CatalogProductSetEdit extends BackendPage
{
    const MCA = 'catalog/product_set/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActions' => [
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'attributeSetEditBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main',
            'locator' => '#tree-div2',
            'strategy' => 'css selector',
        ],
        'attributeSetEditForm' => [
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main\EditForm',
            'locator' => '#set_name',
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
    public function getAttributeSetEditBlock()
    {
        return $this->getBlockInstance('attributeSetEditBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main\EditForm
     */
    public function getAttributeSetEditForm()
    {
        return $this->getBlockInstance('attributeSetEditForm');
    }
}
