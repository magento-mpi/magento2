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

/**
 * Class Category Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Category extends AbstractRepository
{
    /**
     * Attribute set for mapping data into ui tabs
     */
    const GROUP_GENERAL_INFORMATION = 'category_info_tabs_group_4';
    const GROUP_DISPLAY_SETTINGS = 'category_info_tabs_group_5';

    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $this->_data['anchor_category'] = $this->_anchorData();
    }

    /**
     * Enable anchor category
     *
     * @return array
     */
    protected function _anchorData()
    {
        $anchor = array(
            'data' => array(
                'fields' => array(
                    'is_anchor' => array(
                        'value' => 'Yes',
                        'input_value' => '1',
                        'group' => static::GROUP_DISPLAY_SETTINGS,
                        'input' => 'select'
                    )
                )
            )
        );
        return array_replace_recursive($this->_data['default'], $anchor);
    }
}
