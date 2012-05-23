<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test filter data collector
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_Core_Model_Input_FilterTest extends Mage_PHPUnit_TestCase
{
    /**
     * Filter list which need to set to filter
     *
     * @var array
     */
    protected $_filters = array(
        'list_values' => array(
            'children_filters' => array(
                array(
                    'zend' => 'StringToUpper',
                    'args' => array('encoding' => 'utf-8')),
                array('zend' => 'StripTags')
            )
        ),
        'list_values_with_name' => array(
            'children_filters' => array(
                'item1' => array(
                    array(
                        'zend' => 'StringToUpper',
                        'args' => array('encoding' => 'utf-8'))),
                'item2' => array(
                    array('model' => 'core/input_filter_maliciousCode')
                ),
                'item3' => array(
                    array(
                        'helper' => 'core',
                        'method' => 'stripTags',
                        'args' => array('<p> <div>', true))
                )
            )
        )
    );

    /**
     * Filter list which need to add to filter
     *
     * @var array
     */
    protected $_filtersToAdd = array(
        'list_values_with_name' => array(
            'children_filters' => array(
                'deep_list' => array(
                    'children_filters' => array(
                        'sub1' => array(
                            array(
                                'zend' => 'StringToLower',
                                'args' => array('encoding' => 'utf-8'))),
                        'sub2' => array(array('zend' => 'Int'))
                    )
                )
            )
        )
    );

    /**
     * Test filter data collector
     */
    public function testFilter()
    {
        $filter = new Mage_Core_Model_Input_Filter;
        $filter->setFilters($this->_filters);

        $filter->addFilter('name2', new Zend_Filter_Alnum());
        $filter->addFilter('name1',
            array(
                'zend' => 'StringToUpper',
                'args' => array('encoding' => 'utf-8')));
        $filter->addFilter('name1', array('zend' => 'StripTags'), Zend_Filter::CHAIN_PREPEND);
        $filter->addFilters($this->_filtersToAdd);

        $filterData = require_once dirname(__FILE__) . '/_fixtures/filterTestFilterData.php';
        $expectedData = require_once dirname(__FILE__) . '/_fixtures/filterTestExpectedData.php';
        $data = $filter->filter($filterData);
        $this->assertTrue($data == $expectedData);
    }
}
