<?php
/**
 * Filter data collector
 *
 * {license_notice}
 *
 * Model for multi-filtering all data which set to models
 * Example:
 * <code>
 * /** @var $filterFactory \Magento\Core\Model\Input\FilterFactory {@*}
 * $filter = $filterFactory->create()
 *     ->setFilters(array(
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
 *  $filter->addFilter('name2', new \Zend_Filter_Alnum());
 *  $filter->addFilter('name1',
 *      array(
 *          'zend' => 'StringToUpper',
 *          'args' => array('encoding' => 'utf-8')));
 *  $filter->addFilter('name1', array('zend' => 'StripTags'), \Zend_Filter::CHAIN_PREPEND);
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
 * @see \Magento\Core\Model\Input\FilterTest    See this class for manual
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Core\Model\Input;

class Filter implements \Zend_Filter_Interface
{
    /**
     * @var \Magento\Core\Model\Factory\Helper
     */
    protected $_helperFactory;

    /**
     * @var \Magento\Core\Model\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Core\Model\Factory\Helper $helperFactory
     * @param \Magento\Core\Model\ObjectManager $objectManager
     */
    function __construct(
        \Magento\Core\Model\Factory\Helper $helperFactory,
        \Magento\Core\Model\ObjectManager $objectManager
    ) {
        $this->_helperFactory = $helperFactory;
        $this->_objectManager = $objectManager;
    }

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
     * @param array|\Zend_Filter_Interface $filter
     * @param string $placement
     * @return \Magento\Core\Model\Input\Filter
     */
    public function addFilter($name, $filter, $placement = \Zend_Filter::CHAIN_APPEND)
    {
        if ($placement == \Zend_Filter::CHAIN_PREPEND) {
            array_unshift($this->_filters[$name], $filter);
        } else {
            $this->_filters[$name][] = $filter;
        }
        return $this;
    }

    /**
     * Add a filter to the end of the chain
     *
     * @param  array|\Zend_Filter_Interface $filter
     * @return \Magento\Core\Model\Input\Filter
     */
    public function appendFilter(\Zend_Filter_Interface $filter)
    {
        return $this->addFilter($filter, \Zend_Filter::CHAIN_APPEND);
    }

    /**
     * Add a filter to the start of the chain
     *
     * @param  array|\Zend_Filter_Interface $filter
     * @return \Magento\Core\Model\Input\Filter
     */
    public function prependFilter($filter)
    {
        return $this->addFilter($filter, \Zend_Filter::CHAIN_PREPEND);
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
     * @return \Magento\Core\Model\Input\Filter
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
     * @return \Magento\Core\Model\Input\Filter
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
     * @param bool $isFilterListSimple
     * @return array
     * @throws \Exception    \Exception when filter is not found or not instance of defined instances
     */
    protected function _filter(array $data, &$filters = null, $isFilterListSimple = false)
    {
        if (null === $filters) {
            $filters = &$this->_filters;
        }
        foreach ($data as $key => $value) {
            if (!$isFilterListSimple && !empty($filters[$key])) {
                $itemFilters = $filters[$key];
            } elseif ($isFilterListSimple && !empty($filters)) {
                $itemFilters = $filters;
            } else {
                continue;
            }

            if (!$isFilterListSimple && is_array($value) && isset($filters[$key]['children_filters'])) {
                $isChildrenFilterListSimple = is_numeric(implode('', array_keys($filters[$key]['children_filters'])));
                $value = $this->_filter($value, $filters[$key]['children_filters'], $isChildrenFilterListSimple);
            } else {
                foreach ($itemFilters as $filterData) {
                    if ($zendFilter = $this->_getZendFilter($filterData)) {
                        $value = $zendFilter->filter($value);
                    } elseif ($filtrationHelper = $this->_getFiltrationHelper($filterData)) {
                        $value = $this->_applyFiltrationWithHelper($value, $filtrationHelper, $filterData);
                    }
                }
            }
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * Call specified helper method for $value filtration
     *
     * @param mixed $value
     * @param \Magento\Core\Helper\AbstractHelper $helper
     * @param array $filterData
     * @return mixed
     */
    protected function _applyFiltrationWithHelper($value, \Magento\Core\Helper\AbstractHelper $helper, array $filterData)
    {
        if (!isset($filterData['method']) || empty($filterData['method'])) {
            throw new \Exception("Helper filtration method is not set");
        }
        if (!isset($filterData['args']) || empty($filterData['args'])) {
            $filterData['args'] = array();
        }
        $filterData['args'] = array(-100 => $value) + $filterData['args'];
        // apply filter
        $value = call_user_func_array(array($helper, $filterData['method']), $filterData['args']);
        return $value;
    }

    /**
     * Try to create Magento helper for filtration based on $filterData. Return false on failure
     *
     * @param $filterData
     * @return bool|\Magento\Core\Helper\AbstractHelper
     * @throws \Exception
     */
    protected function _getFiltrationHelper($filterData)
    {
        $helper = false;
        if (isset($filterData['helper'])) {
            $helper = $filterData['helper'];
            if (is_string($helper)) {
                $helper = $this->_helperFactory->get($helper);
            } elseif (!($helper instanceof \Magento\Core\Helper\AbstractHelper)) {
                throw new \Exception("Filter '{$helper}' not found");
            }
        }
        return $helper;
    }

    /**
     * Try to create Zend filter based on $filterData. Return false on failure
     *
     * @param $filterData
     * @return bool|\Zend_Filter_Interface
     */
    protected function _getZendFilter($filterData)
    {
        $zendFilter = false;
        if (is_object($filterData) && $filterData instanceof \Zend_Filter_Interface) {
            /** @var $zendFilter \Zend_Filter_Interface */
            $zendFilter = $filterData;
        } elseif (isset($filterData['model'])) {
            $zendFilter = $this->_createCustomZendFilter($filterData);
        } elseif (isset($filterData['zend'])) {
            $zendFilter = $this->_createNativeZendFilter($filterData);
        }
        return $zendFilter;
    }

    /**
     * Get Magento filters
     *
     * @param $filterData
     * @return \Zend_Filter_Interface
     * @throws \Exception
     */
    protected function _createCustomZendFilter($filterData)
    {
        $filter = $filterData['model'];
        if (!isset($filterData['args'])) {
            $filterData['args'] = null;
        } else {
            //use only first element because Mage factory cannot get more
            $filterData['args'] = $filterData['args'][0];
        }
        if (is_string($filterData['model'])) {
            $filter = $this->_objectManager->create($filterData['model'], $filterData['args']);
        }
        if (!($filter instanceof \Zend_Filter_Interface)) {
            throw new \Exception('Filter is not instance of \Zend_Filter_Interface');
        }
        return $filter;
    }

    /**
     * Get native \Zend_Filter
     *
     * @param $filterData
     * @return \Zend_Filter_Interface
     * @throws \Exception
     */
    protected function _createNativeZendFilter($filterData)
    {
        $filter = $filterData['zend'];
        if (is_string($filter)) {
            $class = new \ReflectionClass('Zend_Filter_' . $filter);
            if ($class->implementsInterface('Zend_Filter_Interface')) {
                if (isset($filterData['args']) && $class->hasMethod('__construct')) {
                    $filter = $class->newInstanceArgs($filterData['args']);
                } else {
                    $filter = $class->newInstance();
                }
            } else {
                throw new \Exception('Filter is not instance of \Zend_Filter_Interface');
            }
        }
        return $filter;
    }
}
