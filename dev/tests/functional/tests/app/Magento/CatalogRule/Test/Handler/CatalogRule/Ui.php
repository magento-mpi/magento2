<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Handler\CatalogRule;

use Mtf\Handler\Ui as AbstractUi;
use Mtf\Fixture\FixtureInterface;
use Mtf\Factory\Factory;

/**
 * Class Create Catalog Rule
 * Create a Catalog Rule
 *
 */
class Ui extends AbstractUi implements CatalogRuleInterface
{
    /**
     * Create catalog rule
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        Factory::getApp()->magentoBackendLoginUser();

        $createCatalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $createCatalogRulePage->init($fixture);
        $createCatalogRulePage->open();

        $catalogRuleBlockForm = $createCatalogRulePage->getCatalogPriceRuleForm();
        $catalogRuleBlockForm->fill($fixture);
        $catalogRuleBlockForm->save($fixture);
        $createCatalogRulePage->getMessagesBlock()->assertSuccessMessage();
    }
}
