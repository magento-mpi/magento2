<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Model\Rating;

/**
 * Rating option model
 *
 * @method \Magento\Review\Model\Resource\Rating\Option _getResource()
 * @method \Magento\Review\Model\Resource\Rating\Option getResource()
 * @method int getRatingId()
 * @method \Magento\Review\Model\Rating\Option setRatingId(int $value)
 * @method string getCode()
 * @method \Magento\Review\Model\Rating\Option setCode(string $value)
 * @method int getValue()
 * @method \Magento\Review\Model\Rating\Option setValue(int $value)
 * @method int getPosition()
 * @method \Magento\Review\Model\Rating\Option setPosition(int $value)
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Option extends \Magento\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Review\Model\Resource\Rating\Option');
    }

    /**
     * @return $this
     */
    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }
}
