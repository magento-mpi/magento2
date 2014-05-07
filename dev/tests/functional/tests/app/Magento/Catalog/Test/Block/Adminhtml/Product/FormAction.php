<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Mtf\Page\BackendPage;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class FormAction
 * Form action
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product
 */
class FormAction extends FormPageActions
{
    /**
     * "Save" button
     *
     * @var string
     */
    protected $saveButton = '#save-split-button-button';

    /**
     * Choose affected attribute set dialog popup window
     *
     * @var string
     */
    protected $affectedAttributeSet = "//div[div/@data-id='affected-attribute-set-selector']";

    /**
     * Save product form with window confirmation
     *
     * @param BackendPage $page
     * @param FixtureInterface $product
     * @return void
     */
    public function saveProduct(BackendPage $page, FixtureInterface $product) {
        parent::save();
        /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\AffectedAttributeSetForm $affectedAttributeSetForm */
        $affectedAttributeSetForm = $page->getAffectedAttributeSetForm();
        $affectedAttributeSetForm->fill($product)->confirm();
    }
}
