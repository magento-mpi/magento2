<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class SystemVariable
 * Data for creation Custom Variable
 */
class SystemVariable extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['custom_variable'] = [
            'variable[code]' => 'variableCode%isolation%',
            'variable[name]' => 'variableName%isolation%',
            'variable[html_value]' => "<p class='custom-variable-test-class-%isolation%'>variableName%isolation%</p>",
            'variable[plain_value]' => 'variableName%isolation%',
        ];
    }
}
