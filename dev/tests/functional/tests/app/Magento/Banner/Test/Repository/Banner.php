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

namespace Magento\Banner\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Banner Repository
 *
 * @package Magento\Catalog\Test\Repository
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
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array('config' => $defaultConfig, 'data' => $defaultData);

        $this->_data[self::TEXT_BANNER] = $this->getTextBanner();
    }

    /**
     * Data for Banner containing Text
     */
    protected function getTextBanner()
    {
        return array(
            'data' => array(
                'fields' => array(
                    // Banner Name = banner1
                    'name' => array(
                        'value' => 'banner1'
                    ),
                    // Active = yes
                    'is_enabled' => array(
                        'value' => '1'
                    ),
                    // Content = text/insert variable
                    'store_contents' => array(
                        'value' => array(
                            '0' => 'My Banner'
                        )
                    )
                )
            )
        );
    }
}
