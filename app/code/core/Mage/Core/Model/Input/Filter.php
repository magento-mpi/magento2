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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Filter data collector
 *
 * Model for multi-filtering all data which set to models
 * Example:
 * <code>
 * /** @var $filter Mage_Core_Model_Input_Filter {@*}
 * $filter = Mage::getModel('core/input_filter');
 * $filter->setFilters(array(
 *      'list_values' => array(
 *          'children_filters' => array( //filters will applied to all children
 *              array(
 *                  'zend' => 'StringToUpper',
 *                  'args' => array('encoding' => 'utf-8')),
 *              array('zend' => 'StripTags')
 *          )
 *      ),
 *      'list_values_with_name' => array(
 *          'children_filters' => array(
 *              'item1' => array(
 *                  array(
 *                      'zend' => 'StringToUpper',
 *                      'args' => array('encoding' => 'utf-8'))),
 *              'item2' => array(
 *                  array('model' => 'core/input_filter_maliciousCode')
 *              ),
 *              'item3' => array(
 *                  array(
 *                      'helper' => 'core',
 *                      'method' => 'stripTags',
 *                      'args' => array('<p> <div>', true))
 *              )
 *          )
 *      )
 *  ));
 *  $filter->addFilter('name2', new Zend_Filter_Alnum());
 *  $filter->addFilter('name1',
 *      array(
 *          'zend' => 'StringToUpper',
 *          'args' => array('encoding' => 'utf-8')));
 *  $filter->addFilter('name1', array('zend' => 'StripTags'), Zend_Filter::CHAIN_PREPEND);
 *  $filter->addFilters(protected $_filtersToAdd = array(
 *      'list_values_with_name' => array(
 *          'children_filters' => array(
 *              'deep_list' => array(
 *                  'children_filters' => array(
 *                      'sub1' => array(
 *                          array(
 *                              'zend' => 'StringToLower',
 *                              'args' => array('encoding' => 'utf-8'))),
 *                      'sub2' => array(array('zend' => 'Int'))
 *                  )
 *              )
 *          )
 *      )
 *  ));
 *  $filter->filter(array(
 *      'name1' => 'some <b>string</b>',
 *      'name2' => '888 555',
 *      'list_values' => array(
 *          'some <b>string2</b>',
 *          'some <p>string3</p>',
 *      ),
 *      'list_values_with_name' => array(
 *          'item1' => 'some <b onclick="alert(\'2\')">string4</b>',
 *         'item2' => 'some <b onclick="alert(\'1\')">string5</b>',
 *          'item3' => 'some <p>string5</p> <b>bold</b> <div>div</div>',
 *          'deep_list' => array(
 *              'sub1' => 'toLowString',
 *              'sub2' => '5 TO INT',
 *          )
 *      )
 *  ));
 * </code>
 *
 * @see Mage_Core_Model_Input_FilterTest    See this class for manual
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_Core_Model_Input_Filter implements Zend_Filter_Interface
{
    /**
     * Filters data collectors
     *
     * @var array
     */
    protected $_filters = array();

    /**
     * Add filter
     *
     * @param string $name
     * @param array|Zend_Filter_Interface $filter
     * @param string $placement
     * @return Mage_Core_Model_Input_Filter
     */
    public function addFilter($name, $filter, $placement = Zend_Filter::CHAIN_APPEND)
    {
        if ($placement == Zend_Filter::CHAIN_PREPEND) {
            array_unshift($this->_filters[$name], $filter);
        } else {
            $this->_filters[$name][] = $filter;
        }
        return $this;
    }

    /**
     * Add a filter to the end of the chain
     *
     * @param  array|Zend_Filter_Interface $filter
     * @return Mage_Core_Model_Input_Filter
     */
    public function appendFilter(Zend_Filter_Interface $filter)
    {
        return $this->addFilter($filter, Zend_Filter::CHAIN_APPEND);
    }

    /**
     * Add a filter to the start of the chain
     *
     * @param  array|Zend_Filter_Interface $filter
     * @return Mage_Core_Model_Input_Filter
     */
    public function prependFilter($filter)
    {
        return $this->addFilter($filter, Zend_Filter::CHAIN_PREPEND);
    }

    /**
     * Add filters
     *
     * Filters data must be has view as
     *      array(
     *          'key1' => $filters,
     *          'key2' => array( ... ), //array filters data
     *          'key2' => $filters
     *      )
     *
     * @param array $filters
     * @return Mage_Core_Model_Input_Filter
     */
    public function addFilters(array $filters)
    {
        $this->_filters = array_merge_recursive($this->_filters, $filters);
        return $this;
    }

    /**
     * Set filters
     *
     * @param array $filters
     * @return Mage_Core_Model_Input_Filter
     */
    public function setFilters(array $filters)
    {
        $this->_filters = $filters;
        return $this;
    }

    /**
     * Get filters
     *
     * @param string|null $name     Get filter for selected name
     * @return array
     */
    public function getFilters($name = null)
    {
        if (null === $name) {
            return $this->_filters;
        } else {
            return isset($this->_filters[$name]) ? $this->_filters[$name] : null;
        }
    }

    /**
     * Filter data
     *
     * @param array $data
     * @return array    Return filtered data
     */
    public function filter($data)
    {
        return $this->_filter($data);
    }

    /**
     * Recursive filtering
     *
     * @param array $data
     * @param array|null $filters
     * @param bool $simpleFilterList
     * @return array
     * @throws Exception    Exception when filter is not found or not instance of defined instances
     */
    protected function _filter(array $data, &$filters = null, $simpleFilterList = false)
    {
        if (null === $filters) {
            $filters = &$this->_filters;
        }
        foreach ($data as $key => $value) {
            if (!$simpleFilterList && !empty($filters[$key])) {
                $itemFilters = $filters[$key];
            } elseif ($simpleFilterList && !empty($filters)) {
                $itemFilters = $filters;
            } else {
                continue;
            }

            if (!$simpleFilterList && is_array($value) && isset($filters[$key]['children_filters'])) {
                $value = $this->_filter(
                    $value,
                    $filters[$key]['children_filters'],
                    !(!is_numeric(implode('', array_keys($filters[$key]['children_filters'])))));
            } else {
                foreach ($itemFilters as $filterData) {
                    //case Zend_Filter
                    if (is_object($filterData) || isset($filterData['zend']) || isset($filterData['model'])) {
                        if (is_object($filterData)) {
                            /** @var $filter Zend_Filter_Interface */
                            $filter = $filterData;
                        } elseif (isset($filterData['model'])) {
                            /**
                             * Get Magento filters
                             */
                            /** @var $filter Zend_Filter_Interface */
                            $filter = $filterData['model'];
                            if (!isset($filterData['args'])) {
                                $filterData['args'] = null;
                            } else {
                                //use only first element because Mage factory cannot get more
                                $filterData['args'] = $filterData['args'][0];
                            }
                            if (is_string($filterData['model'])) {
                                $filter = Mage::getModel($filterData['model'], $filterData['args']);
                            }
                            if (!($filter instanceof Zend_Filter_Interface)) {
                                throw new Exception('Filter is not instance of Zend_Filter_Interface');
                            }
                        } elseif (isset($filterData['zend'])) {
                            /**
                             * Get native Zend_Filter
                             */
                            /** @var $filter Zend_Filter_Interface */
                            $filter = $filterData['zend'];
                            if (is_string($filter)) {
                                $class = new ReflectionClass('Zend_Filter_' . $filter);
                                if ($class->implementsInterface('Zend_Filter_Interface')) {
                                    if (isset($filterData['args']) && $class->hasMethod('__construct')) {
                                        $filter = $class->newInstanceArgs($filterData['args']);
                                    } else {
                                        $filter = $class->newInstance();
                                    }
                                } else {
                                    throw new Exception('Filter is not instance of Zend_Filter_Interface');
                                }
                            }
                        } else {
                            continue;
                        }
                        //filtering
                        $value = $filter->filter($value);
                    } elseif (isset($filterData['helper'])) {
                        /**
                         * Filtering via Magento helper method
                         */
                        if (empty($filterData['args'])) {
                            $filterData['args'] = array();
                        }
                        /** @var $helper Mage_Core_Helper_Abstract */
                        $helper = $filterData['helper'];
                        if (is_string($helper)) {
                            $helper = Mage::helper($helper);
                        }
                        if (!($helper instanceof Mage_Core_Helper_Abstract)) {
                            throw new Exception("Filter '{$filterData['helper']}' not found");
                        }
                        $filterData['args'] = array(-100 => $value) + $filterData['args'];
                        //filtering
                        $value = call_user_func_array(
                            array($helper, $filterData['method']),
                            $filterData['args']);
                    }
                }
            }
            $data[$key] = $value;
        }
        return $data;
    }
}
