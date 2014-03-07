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

use Magento\Catalog\Test\Fixture;
use Mtf\Repository\AbstractRepository;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class AssignProducts extends AbstractRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $this->_data['add_' . $defaultConfig['assignType '] . '_products'] = $this->_data['default'];
        $this->_data['add_' . $defaultConfig['assignType '] . '_product'] = $this->withOneProduct(
            $defaultConfig['assignType ']
        );
    }

    /**
     * @param string $type
     * @return array
     */
    protected function withOneProduct($type)
    {
        $data = $this->_data['default'];
        unset($data['data']['fields'][$type . '_products']['value']['product_2']);
        return $data;
    }
}
