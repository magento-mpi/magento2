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

namespace Magento\Catalog\Test\Handler\CatalogProductVirtual;

use Mtf\Handler\Ui as AbstractUi;
use Mtf\Fixture\FixtureInterface;
use Mtf\Factory\Factory;

/**
 * Class CreateProduct
 * Create a product
 */
class Ui extends AbstractUi implements CatalogProductVirtualInterface
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

        $productBlockForm = $createProductPage->getProductForm();
        $productBlockForm->fill($fixture);
        $productBlockForm->save($fixture);
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
    }
}
