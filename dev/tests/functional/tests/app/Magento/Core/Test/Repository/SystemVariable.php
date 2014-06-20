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
 */
class SystemVariable extends AbstractRepository
{
    /**
     * @construct
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'code' => 'variableCode%isolation%',
            'name' => 'variableName%isolation%',
            'html_value' => '{{html_value=""}}',
            'plain_value' => 'plain_value',
        ];
    }
}
