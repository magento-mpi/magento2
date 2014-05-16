<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\Ui;

use Mtf\Handler\Ui;
use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CreateProduct
 * Create a product
 */
class CreateProduct extends Ui
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
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
    }
}
