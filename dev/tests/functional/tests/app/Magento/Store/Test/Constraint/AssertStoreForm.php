<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Store\Test\Fixture\StoreGroup;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backend\Test\Page\Adminhtml\EditGroup;

/**
 * Class AssertStoreForm
 * Assert that displayed Store Group data on edit page equals passed from fixture
 */
class AssertStoreForm extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed Store Group data on edit page equals passed from fixture
     *
     * @param StoreIndex $storeIndex
     * @param EditGroup $editGroup
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function processAssert(
        StoreIndex $storeIndex,
        EditGroup $editGroup,
        StoreGroup $storeGroup
    ) {
        $fixtureData = $storeGroup->getData();
        $storeIndex->open()->getStoreGrid()->searchAndOpenStore($storeGroup);
        $formData = $editGroup->getEditFormGroup()->getData();
        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store Group data on edit page equals data from fixture.';
    }
}
