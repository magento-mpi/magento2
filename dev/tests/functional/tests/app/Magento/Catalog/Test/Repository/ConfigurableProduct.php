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
 * Class Configurable Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class ConfigurableProduct extends AbstractRepository
{
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        
        $this->_data['configurable_required'] = $this->_data['default'];
        $this->_data['configurable']['data']['category_name'] = '%category%';
        $this->_data['configurable']['data']['affect_configurable_product_attributes'] = 'Template %isolation%';
    }
}
