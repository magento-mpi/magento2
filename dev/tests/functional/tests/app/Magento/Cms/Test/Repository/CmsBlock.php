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
 * Class CmsBlock
 * Data for creation CMS Block
 */
class CmsBlock extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'title' => 'block_%isolation%',
            'identifier' => 'identifier_%isolation%',
            'store_id' => ['dataSet' => ['All Store Views']],
            'is_active' => 'Enabled',
            'content' => 'description_%isolation%'
        ];
    }
}
