<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogRuleNew
 */
class CatalogRuleNew extends BackendPage
{
    const MCA = 'catalog_rule/promo_catalog/new';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\CatalogRule\Test\Block\Adminhtml\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'editForm' => [
            'name' => 'editForm',
            'class' => 'Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog\Edit\PromoForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CatalogRule\Test\Block\Adminhtml\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog\Edit\PromoForm
     */
    public function getEditForm()
    {
        return $this->getBlockInstance('editForm');
    }
}
