<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

class VirtualProduct extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['virtual_required'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['virtual'] = $this->_data['virtual_required'];
        $this->_data['virtual']['data']['category_name'] = '%category::getCategoryName%';
    }
}
