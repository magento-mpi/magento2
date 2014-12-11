<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Source;

/**
 * Quick search weight model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Weight
{
    /**
     * Quick search weights
     *
     * @var int[]
     */
    protected $_weights = [1, 2, 3, 4, 5];

    /**
     * Retrieve search weights as options array
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getValues() as $value) {
            $res[] = ['value' => $value, 'label' => $value];
        }
        return $res;
    }

    /**
     * Retrieve search weights array
     *
     * @return int[]
     */
    public function getValues()
    {
        return $this->_weights;
    }
}
