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

namespace Magento\Cms\Test\Repository;

use Mtf\Factory\Factory;
use Mtf\Repository\AbstractRepository;

/**
 * Class Page Repository
 *
 * @package Magento\Cms\Test\Repository
 */
class Page extends AbstractRepository
{
    const PAGE = 'page';

    const MAIN_TAB_ID = 'page_tabs_main_section';

    const CONTENT_TAB_ID = 'page_tabs_content_section';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data[self::PAGE] = $this->getPage();
    }

    protected function getPage()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'page_title' => array(
                        'value' => 'CMS Page%isolation%',
                        'group' => self::MAIN_TAB_ID
                    ),
                    'page_identifier' => array(
                        'value' => 'identifier%isolation%',
                        'group' => self::MAIN_TAB_ID
                    ),
                    'page_store_id' => array(
                        'value' => 'All Store Views',
                        'group' => self::MAIN_TAB_ID,
                        'input' => 'select',
                        'input_value' => '0'
                    ),
                    'page_is_active' => array(
                        'value' => 'Published',
                        'group' => self::MAIN_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'page_under_version_control' => array(
                        'value' => 'No',
                        'group' => self::MAIN_TAB_ID,
                        'input' => 'select',
                        'input_value' => '0'
                    ),
                    'page_content' => array(
                        'value' => 'Test %isolation%',
                        'group' => self::CONTENT_TAB_ID
                    )
                )
            )
        );
    }
}
