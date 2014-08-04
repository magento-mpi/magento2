<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Page Repository
 *
 */
class Page extends AbstractRepository
{
    const PAGE = 'page';

    const MAIN_TAB_ID = 'page_information';

    const CONTENT_TAB_ID = 'content';

    /**
     * {@inheritdoc}
     *
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @return void
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data[self::PAGE] = $this->getPage();
    }

    /**
     * Cms Page test data
     *
     * @return array
     */
    protected function getPage()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'title' => array(
                        'value' => 'CMS Page%isolation%',
                        'group' => self::MAIN_TAB_ID
                    ),
                    'identifier' => array(
                        'value' => 'identifier%isolation%',
                        'group' => self::MAIN_TAB_ID
                    ),
                    'store_id' => array(
                        'value' => 'All Store Views',
                        'group' => self::MAIN_TAB_ID,
                        'input' => 'select',
                        'input_value' => '0'
                    ),
                    'is_active' => array(
                        'value' => 'Published',
                        'group' => self::MAIN_TAB_ID,
                        'input' => 'select',
                        'input_value' => '1'
                    ),
                    'under_version_control' => array(
                        'value' => 'No',
                        'group' => self::MAIN_TAB_ID,
                        'input' => 'select',
                        'input_value' => '0'
                    ),
                    'content' => array(
                        'value' => array(
                            'content' => 'Test %isolation%'
                        ),
                        'group' => self::CONTENT_TAB_ID
                    )
                )
            )
        );
    }
}
