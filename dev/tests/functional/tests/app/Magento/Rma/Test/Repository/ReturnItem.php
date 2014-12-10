<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class ReturnItem Repository
 */
class ReturnItem extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'config' => $defaultConfig,
            'data' => $defaultData,
        ];

        $this->_data['rma'] = $this->_getRma();
    }

    protected function _getRma()
    {
        return [
            'data' => [
                'fields' => [
                    'qty_requested' => '1',
                    'resolution' => 'Refund',
                    'condition' => 'Opened',
                    'reason' => 'Wrong Size',
                ],
            ]
        ];
    }
}
