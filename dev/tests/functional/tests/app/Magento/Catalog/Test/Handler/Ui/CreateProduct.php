<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\Ui;

use Mtf\Fixture;
use Mtf\Handler\Ui;
use Mtf\Factory\Factory;

/**
 * Class CreateProduct
 * Create a product
 *
 * @package Magento\Catalog\Test\Handler\Ui
 */
class CreateProduct extends Ui
{
    /**
     * Create product
     *
     * @param Fixture|\Mtf\Fixture\DataFixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        Factory::getApp()->magentoBackendLoginUser();

        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $createProductPage->init($fixture);
        $createProductPage->open();

        $productBlockForm = $createProductPage->getProductBlockForm();
        $productBlockForm->fill($fixture);
        $productBlockForm->save($fixture);
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
    }
}
