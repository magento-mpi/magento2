<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Core\Test\Fixture\SystemVariable;
use Magento\Core\Test\Page\Adminhtml\SystemVariableIndex;
use Magento\Core\Test\Page\Adminhtml\SystemVariableNew;
use Magento\Store\Test\Fixture\Store;

/**
 * Test Creation for CreateCustomVariableEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Custom system variable is created.
 * 2. Additional Non Default Storeview is created.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to System->Other Settings->Custom Variables.
 * 3. Open from grid created custom system variable.
 * 4. Navigate to the Store Switcher.
 * 5. Choose Appropriate Storeview (non default).
 * 6. Set Use Default Variable Values.
 * 7. Edit necessary fields.
 * 8. Save Custom variable using correspond saveActions.
 * 9. Perform all assertions.
 *
 * @group Variables_(PS)
 * @ZephyrId MAGETWO-26241
 */
class UpdateCustomVariableEntityTest extends Injectable
{
    /**
     * Custom System Variable grid page
     *
     * @var SystemVariableIndex
     */
    protected $systemVariableIndexPage;

    /**
     * Custom System Variable new and edit page
     *
     * @var SystemVariableNew
     */
    protected $systemVariableNewPage;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $storeOrigin = $fixtureFactory->createByCode('store', ['dataSet' => 'german']);
        $storeOrigin->persist();

        return [
            'storeOrigin' => $storeOrigin
        ];
    }


    /**
     * Injection data
     *
     * @param SystemVariableIndex $systemVariableIndex
     * @param SystemVariableNew $systemVariableNew
     * @param SystemVariable $systemVariableOrigin
     * @return array
     */
    public function __inject(
        SystemVariableIndex $systemVariableIndex,
        SystemVariableNew $systemVariableNew,
        SystemVariable $systemVariableOrigin
    ) {
        $this->systemVariableIndexPage = $systemVariableIndex;
        $this->systemVariableNewPage = $systemVariableNew;

        $systemVariableOrigin->persist();

        return ['systemVariableOrigin' => $systemVariableOrigin];
    }

    /**
     * Update Custom System Variable Entity test
     *
     * @param SystemVariable $systemVariable
     * @param SystemVariable $systemVariableOrigin
     * @param Store $storeOrigin
     * @param $saveAction
     * @return void
     */
    public function test(
        SystemVariable $systemVariable,
        SystemVariable $systemVariableOrigin,
        Store $storeOrigin,
        $saveAction
    ) {
        $filter = [
            'code' => $systemVariableOrigin->getCode(),
        ];

        // Steps
        $this->systemVariableIndexPage->open();
        $this->systemVariableIndexPage->getSystemVariableGrid()->searchAndOpen($filter);
        $this->systemVariableNewPage->getFormPageActions()->selectStoreView($storeOrigin->getData('store_id'));
        $this->systemVariableNewPage->getSystemVariableForm()->fill($systemVariable);
        $this->systemVariableNewPage->getFormPageActions()->$saveAction();
    }
}
