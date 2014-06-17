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
 * Product Set add page
 */
class CatalogProductSetAdd extends BackendPage
{
    const MCA = 'catalog/product_set/add';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'attributeSetForm' => [
            'name' => 'attributeSetForm',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main\Form',
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
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Main\Form
     */
    public function getAttributeSetForm()
    {
        return $this->getBlockInstance('attributeSetForm');
    }
}
