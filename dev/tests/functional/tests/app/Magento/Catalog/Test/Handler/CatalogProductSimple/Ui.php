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

namespace Magento\Catalog\Test\Handler\CatalogProductSimple;

use Mtf\Handler\Ui as AbstractUi;
use Mtf\Fixture\FixtureInterface;
use Mtf\Factory\Factory;

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

        $productBlockForm = $createProductPage->getProductBlockForm();
        $productBlockForm->fill($fixture);
        $productBlockForm->save($fixture);
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
    }
}
