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

namespace Magento\Rma\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class ReturnItem Repository
 *
 * @package Magento\Rma\Repository
 */
class ReturnItem extends AbstractRepository
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

        $this->_data['rma'] = $this->_getRma();
    }

    protected function _getRma()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'quantity' => '1',
                    'resolution' => 'Refund',
                    'condition' => 'Opened',
                    'reason' => 'Wrong Size'
                )
            )
        );
    }
}
