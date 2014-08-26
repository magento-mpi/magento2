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
 * Class CatalogProductSetAdd
 */
class CatalogProductSetAdd extends BackendPage
{
    const MCA = 'catalog/product_set/add';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'attributeSetForm' => [
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main\AttributeSetForm',
            'locator' => '#set_name',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main\AttributeSetForm
     */
    public function getAttributeSetForm()
    {
        return $this->getBlockInstance('attributeSetForm');
    }
}
