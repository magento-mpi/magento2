<?php
/**
 * Created by PhpStorm.
 * User: sivashchenko
 * Date: 10/2/2014
 * Time: 5:23 PM
 */

namespace Magento\Demo\Ui\DataProvider;

use Magento\Ui\DataProvider\DataProviderInterface;

class Sample implements DataProviderInterface
{

    /**
     * Get meta data
     *
     * @return array
     */
    public function getMeta()
    {
        return [
            'sample_input' => [
                'title' => 'Sample Input',
                'data_type' => 'text',
                'index' => 'sample_input'
            ],
            'sample_select' => [
                'title' => 'Sample Select',
                'data_type' => 'select',
                'index' => 'sample_select',
                'options' => [
                    ['label' => 'First', 'value' => '1'],
                    ['label' => 'Second', 'value' => '2'],
                    ['label' => 'Third', 'value' => '3'],
                ],
            ],
            'sample_date' => [
                'title' => 'Sample Date',
                'data_type' => 'date',
                'index' => 'sample_date'
            ]
        ];
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return [
            'sample_input' => 'Value10',
            'sample_select' => '3',
            'sample_date' => '2014-10-06'
        ];
    }
} 