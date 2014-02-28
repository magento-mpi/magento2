<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource;

/**
 * Sales abstract resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractResource extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Data converter object
     *
     * @var \Magento\Sales\Model\ConverterInterface
     */
    protected $_converter = null;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Stdlib\DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Prepare data for save
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return array
     */
    protected function _prepareDataForSave(\Magento\Core\Model\AbstractModel $object)
    {
        $currentTime = $this->dateTime->now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        $data = parent::_prepareDataForSave($object);
        return $data;
    }

    /**
     * Check if current model data should be converted
     *
     * @return bool
     */
    protected function _shouldBeConverted()
    {
        return (null !== $this->_converter);
    }


    /**
     * Perform actions before object save
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        parent::_beforeSave($object);

        if (true == $this->_shouldBeConverted()) {
            foreach ($object->getData() as $fieldName => $fieldValue) {
                $object->setData($fieldName, $this->_converter->encode($object, $fieldName));
            }
        }
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Core\Model\AbstractModel $object)
    {
        if (true == $this->_shouldBeConverted()) {
            foreach ($object->getData() as $fieldName => $fieldValue) {
                $object->setData($fieldName, $this->_converter->decode($object, $fieldName));
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Perform actions after object load
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Core\Model\AbstractModel $object)
    {
        if (true == $this->_shouldBeConverted()) {
            foreach ($object->getData() as $fieldName => $fieldValue) {
                $object->setData($fieldName, $this->_converter->decode($object, $fieldName));
            }
        }
        return parent::_afterLoad($object);
    }
}
