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

namespace Magento\Customer\Test\Repository;

use Mtf\Factory\Factory;
use Mtf\Repository\AbstractRepository;

/**
 * Class Customer Group Repository
 *
 * @package Magento\Customer\Test\Fixture
 */
class CustomerGroup extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['valid_vat_id_domestic'] = $this->getValidDomestic($this->_data['default']);
        $this->_data['valid_vat_id_union'] = $this->getValidUnion($this->_data['default']);
        $this->_data['invalid_vat_id'] = $this->getInvalid($this->_data['default']);
        $this->_data['validation_error'] = $this->getError($this->_data['default']);
    }

    /**
     * Get Valid Domestic VAT group data
     *
     * @param array $defaultData
     * @return array
     */
    protected function getValidDomestic(array $defaultData)
    {
        $defaultData['data']['fields']['code']['value'] = 'Valid VAT ID-Domestic%isolation%';
        return $defaultData;
    }

    /**
     * Get Valid Union VAT group data
     *
     * @param array $defaultData
     * @return array
     */
    protected function getValidUnion(array $defaultData)
    {
        $defaultData['data']['fields']['code']['value'] = 'ValidVATID-IntraUnion%isolation%';
        return $defaultData;
    }

    /**
     * Get Invalid VAT group data
     *
     * @param array $defaultData
     * @return array
     */
    protected function getInvalid(array $defaultData)
    {
        $defaultData['data']['fields']['code']['value'] = 'Invalid VAT ID%isolation%';
        return $defaultData;
    }

    /**
     * Get Error group data
     *
     * @param array $defaultData
     * @return array
     */
    protected function getError(array $defaultData)
    {
        $defaultData['data']['fields']['code']['value'] = 'Validation Error Group%isolation%';
        return $defaultData;
    }
}
