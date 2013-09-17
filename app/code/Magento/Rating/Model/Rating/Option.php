<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating option model
 *
 * @method Magento_Rating_Model_Resource_Rating_Option _getResource()
 * @method Magento_Rating_Model_Resource_Rating_Option getResource()
 * @method int getRatingId()
 * @method Magento_Rating_Model_Rating_Option setRatingId(int $value)
 * @method string getCode()
 * @method Magento_Rating_Model_Rating_Option setCode(string $value)
 * @method int getValue()
 * @method Magento_Rating_Model_Rating_Option setValue(int $value)
 * @method int getPosition()
 * @method Magento_Rating_Model_Rating_Option setPosition(int $value)
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Rating_Option extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Rating_Model_Resource_Rating_Option');
    }

    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }
}
