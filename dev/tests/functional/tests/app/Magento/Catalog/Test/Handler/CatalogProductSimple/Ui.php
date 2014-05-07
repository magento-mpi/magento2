<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogProductSimple;

use Mtf\Factory\Factory;
use Mtf\Handler\Ui as AbstractUi;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CreateProduct
 * Create a product
 *
 * @package Magento\Catalog\Test\Handler\Ui
 */
class Ui extends AbstractUi implements CatalogProductSimpleInterface
{
    /**
     * Create product
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        Factory::getApp()->magentoBackendLoginUser();

        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $createProductPage->init($fixture);
        $createProductPage->open();

        $productForm = $createProductPage->getProductForm();
        $productForm->fill($fixture);
        $createProductPage->getFormAction()->save();
        $createProductPage->getMessageBlock()->assertSuccessMessage();
    }
}
