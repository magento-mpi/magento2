<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Banner Repository
 */
class Banner extends AbstractRepository
{
    /**
     * Key for text banner
     */
    const TEXT_BANNER = 'text';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = ['config' => $defaultConfig, 'data' => $defaultData];

        $this->_data[self::TEXT_BANNER] = $this->getTextBanner();
    }

    /**
     * Data for Banner containing Text
     */
    protected function getTextBanner()
    {
        return [
            'data' => [
                'fields' => [
                    // Banner Name = banner1
                    'name' => [
                        'value' => 'Banner %isolation%',
                    ],
                    // Active = yes
                    'is_enabled' => [
                        'value' => '1',
                    ],
                    // Content = text/insert variable
                    'store_contents' => [
                        'value' => [
                            '0' => 'My Banner %isolation%',
                        ],
                    ],
                ],
            ]
        ];
    }
}
