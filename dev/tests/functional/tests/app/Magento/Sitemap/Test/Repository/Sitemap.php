<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Sitemap
 *
 */
class Sitemap extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'sitemap_filename' => 'sitemap.xml',
            'sitemap_path' => '/'
        ];
    }
}
