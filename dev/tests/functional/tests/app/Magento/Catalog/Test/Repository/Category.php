<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Category Repository
 *
 */
class Category extends AbstractRepository
{
    /**
     * Attribute set for mapping data into ui tabs
     */
    const GROUP_GENERAL_INFORMATION = 'general_information';
    const GROUP_DISPLAY_SETTINGS = 'display_setting';

    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $this->_data['anchor_category'] = $this->_getAnchorCategory();
    }

    /**
     * Enable anchor category
     *
     * @return array
     */
    protected function _getAnchorCategory()
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
