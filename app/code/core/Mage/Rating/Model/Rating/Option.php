<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating option model
 *
 * @method Mage_Rating_Model_Resource_Rating_Option _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option getResource()
 * @method int getRatingId()
 * @method Mage_Rating_Model_Rating_Option setRatingId(int $value)
 * @method string getCode()
 * @method Mage_Rating_Model_Rating_Option setCode(string $value)
 * @method int getValue()
 * @method Mage_Rating_Model_Rating_Option setValue(int $value)
 * @method int getPosition()
 * @method Mage_Rating_Model_Rating_Option setPosition(int $value)
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Rating_Option extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Rating_Model_Resource_Rating_Option');
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

//    public function getId()
//    {
//        return $this->getOptionId();
//    }
}
