<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                ),
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
     * Filter list which need to add in test
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
     * Filter data
     *
     * @var array
     */
    protected $_filterData = array(
        'name1' => 'some <b>string</b>',
        'name2' => '888 555',
        'list_values' => array(
            'some <b>string2</b>',
            'some <p>string3</p>',
        ),
        'list_values_with_name' => array(
            'item1' => 'some <b onclick="alert(\'2\')">string4</b>',
            'item2' => 'some <b onclick="alert(\'1\')">string5</b>',
            'item3' => 'some <p>string5</p> <b>bold</b> <div>div</div>',
            'deep_list' => array(
                'sub1' => 'toLowString',
                'sub2' => '5 TO INT',
            )
        )
    );

    /**
     * Expected data
     *
     * @var array
     */
    protected $_expectedData = array(
        'name1' => 'SOME STRING',
        'name2' => '888555',
        'list_values' => array(
            0 => 'SOME STRING2',
            1 => 'SOME STRING3',
        ),
        'list_values_with_name' => array(
            'item1' => 'SOME <B ONCLICK="ALERT(\'2\')">STRING4</B>',
            'item2' => 'some <b >string5</b>',
            'item3' => 'some &lt;p&gt;string5&lt;/p&gt; bold &lt;div&gt;div&lt;/div&gt;',
            'deep_list' => array(
                'sub1' => 'tolowstring',
                'sub2' => 5,
            )
        ),
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

        $data = $filter->filter($this->_filterData);
        $this->assertTrue($data == $this->_expectedData);
    }
}
