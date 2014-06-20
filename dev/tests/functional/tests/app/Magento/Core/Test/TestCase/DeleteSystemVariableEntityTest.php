<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\TestCase;

use Magento\Core\Test\Fixture\SystemVariable;
use Magento\Core\Test\Page\Adminhtml\SystemVariableIndex;
use Magento\Core\Test\Page\Adminhtml\SystemVariableNew;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for DeleteSystemVariableEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Custom Variable is created
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to System->Other Settings->Custom Variables.
 * 3. Open Variable.
 * 4. Click 'Delete' button.
 * 5. Perform asserts.
 *
 * @group Variables_(PS)
 * @ZephyrId MAGETWO-25535
 */
class DeleteSystemVariableEntityTest extends Injectable
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
     * Injection data
     *
     * @param SystemVariableIndex $systemVariableIndex
     * @param SystemVariableNew $systemVariableNew
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        SystemVariableIndex $systemVariableIndex,
        SystemVariableNew $systemVariableNew,
        FixtureFactory $fixtureFactory
    ) {
        $this->systemVariableIndexPage = $systemVariableIndex;
        $this->systemVariableNewPage = $systemVariableNew;
        $systemVariable = $fixtureFactory->createByCode('systemVariable', ['dataSet' => 'default']);
        $systemVariable->persist();

        return ['systemVariable' => $systemVariable];
    }

    /**
     * Delete Custom System Variable Entity test
     *
     * @param SystemVariable $systemVariable
     * @return void
     */
    public function test(SystemVariable $systemVariable)
    {
        // Steps
        $filter = [
            'code' => $systemVariable->getCode(),
            'name' => $systemVariable->getName(),
        ];
        $this->systemVariableIndexPage->open();
        $this->systemVariableIndexPage->getSystemVariableGrid()->searchAndOpen($filter);
        $this->systemVariableNewPage->getFormPageActions()->delete();
    }
}
