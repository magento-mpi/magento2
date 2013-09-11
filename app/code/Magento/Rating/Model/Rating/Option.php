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
 * @method \Magento\Rating\Model\Resource\Rating\Option _getResource()
 * @method \Magento\Rating\Model\Resource\Rating\Option getResource()
 * @method int getRatingId()
 * @method \Magento\Rating\Model\Rating\Option setRatingId(int $value)
 * @method string getCode()
 * @method \Magento\Rating\Model\Rating\Option setCode(string $value)
 * @method int getValue()
 * @method \Magento\Rating\Model\Rating\Option setValue(int $value)
 * @method int getPosition()
 * @method \Magento\Rating\Model\Rating\Option setPosition(int $value)
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rating\Model\Rating;

class Option extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\Rating\Model\Resource\Rating\Option');
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
