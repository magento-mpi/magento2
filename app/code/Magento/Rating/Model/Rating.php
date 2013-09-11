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
 * Rating model
 *
 * @method \Magento\Rating\Model\Resource\Rating getResource()
 * @method \Magento\Rating\Model\Resource\Rating _getResource()
 * @method array getRatingCodes()
 * @method \Magento\Rating\Model\Rating setRatingCodes(array $value)
 * @method array getStores()
 * @method \Magento\Rating\Model\Rating setStores(array $value)
 * @method string getRatingCode()
 * @method \Magento\Rating\Model\Rating getRatingCode(string $value)
 *
 * @category   Magento
 * @package    Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rating\Model;

class Rating extends \Magento\Core\Model\AbstractModel
{
    /**
     * rating entity codes
     *
     */
    const ENTITY_PRODUCT_CODE           = 'product';
    const ENTITY_PRODUCT_REVIEW_CODE    = 'product_review';
    const ENTITY_REVIEW_CODE            = 'review';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('\Magento\Rating\Model\Resource\Rating');
    }

    public function addOptionVote($optionId, $entityPkValue)
    {
        \Mage::getModel('\Magento\Rating\Model\Rating\Option')->setOptionId($optionId)
            ->setRatingId($this->getId())
            ->setReviewId($this->getReviewId())
            ->setEntityPkValue($entityPkValue)
            ->addVote();
        return $this;
    }

    public function updateOptionVote($optionId)
    {
        \Mage::getModel('\Magento\Rating\Model\Rating\Option')->setOptionId($optionId)
            ->setVoteId($this->getVoteId())
            ->setReviewId($this->getReviewId())
            ->setDoUpdate(1)
            ->addVote();
        return $this;
    }

    /**
     * retrieve rating options
     *
     * @return array
     */
    public function getOptions()
    {
        if ($options = $this->getData('options')) {
            return $options;
        }
        elseif ($id = $this->getId()) {
            return \Mage::getResourceModel('\Magento\Rating\Model\Resource\Rating\Option\Collection')
               ->addRatingFilter($id)
               ->setPositionOrder()
               ->load()
               ->getItems();
        }
        return array();
    }

    /**
     * Get rating collection object
     *
     * @return \Magento\Data\Collection\Db
     */

    public function getEntitySummary($entityPkValue,  $onlyForCurrentStore = true)
    {
        $this->setEntityPkValue($entityPkValue);
        return $this->_getResource()->getEntitySummary($this, $onlyForCurrentStore);
    }

    public function getReviewSummary($reviewId,  $onlyForCurrentStore = true)
    {
        $this->setReviewId($reviewId);
        return $this->_getResource()->getReviewSummary($this, $onlyForCurrentStore);
    }

    /**
     * Get rating entity type id by code
     *
     * @param string $entityCode
     * @return int
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }
}
