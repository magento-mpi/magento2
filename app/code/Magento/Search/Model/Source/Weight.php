<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quick search weight model
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Model\Source;

class Weight
{
    /**
     * Quick search weights
     *
     * @var array
     */
    protected $_weights = array(1, 2, 3, 4, 5);

    /**
     * Retrieve search weights as options array
     *
     * @return array
     */
    public function getOptions()
    {
        $res = array();
        foreach ($this->getValues() as $value) {
            $res[] = array(
               'value' => $value,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve search weights array
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_weights;
    }
}
