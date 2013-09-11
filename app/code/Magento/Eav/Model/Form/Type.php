<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Form Type Model
 *
 * @method \Magento\Eav\Model\Resource\Form\Type _getResource()
 * @method \Magento\Eav\Model\Resource\Form\Type getResource()
 * @method string getCode()
 * @method \Magento\Eav\Model\Form\Type setCode(string $value)
 * @method string getLabel()
 * @method \Magento\Eav\Model\Form\Type setLabel(string $value)
 * @method int getIsSystem()
 * @method \Magento\Eav\Model\Form\Type setIsSystem(int $value)
 * @method string getTheme()
 * @method \Magento\Eav\Model\Form\Type setTheme(string $value)
 * @method int getStoreId()
 * @method \Magento\Eav\Model\Form\Type setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Form;

class Type extends \Magento\Core\Model\AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_type';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Eav\Model\Resource\Form\Type');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return \Magento\Eav\Model\Resource\Form\Type
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve resource collection instance wrapper
     *
     * @return \Magento\Eav\Model\Resource\Form\Type\Collection
     */
    public function getCollection()
    {
        return parent::getCollection();
    }

    /**
     * Retrieve assigned Eav Entity types
     *
     * @return array
     */
    public function getEntityTypes()
    {
        if (!$this->hasData('entity_types')) {
            $this->setData('entity_types', $this->_getResource()->getEntityTypes($this));
        }
        return $this->_getData('entity_types');
    }

    /**
     * Set assigned Eav Entity types
     *
     * @param array $entityTypes
     * @return \Magento\Eav\Model\Form\Type
     */
    public function setEntityTypes(array $entityTypes)
    {
        $this->setData('entity_types', $entityTypes);
        return $this;
    }

    /**
     * Assign Entity Type to Form Type
     *
     * @param int $entityTypeId
     * @return \Magento\Eav\Model\Form\Type
     */
    public function addEntityType($entityTypeId)
    {
        $entityTypes = $this->getEntityTypes();
        if (!empty($entityTypeId) && !in_array($entityTypeId, $entityTypes)) {
            $entityTypes[] = $entityTypeId;
            $this->setEntityTypes($entityTypes);
        }
        return $this;
    }

    /**
     * Copy Form Type properties from skeleton form type
     *
     * @param \Magento\Eav\Model\Form\Type $skeleton
     * @return \Magento\Eav\Model\Form\Type
     */
    public function createFromSkeleton(\Magento\Eav\Model\Form\Type $skeleton)
    {
        $fieldsetCollection = \Mage::getModel('\Magento\Eav\Model\Form\Fieldset')->getCollection()
            ->addTypeFilter($skeleton)
            ->setSortOrder();
        $elementCollection = \Mage::getModel('\Magento\Eav\Model\Form\Element')->getCollection()
            ->addTypeFilter($skeleton)
            ->setSortOrder();

        // copy fieldsets
        $fieldsetMap = array();
        foreach ($fieldsetCollection as $skeletonFieldset) {
            /* @var $skeletonFieldset \Magento\Eav\Model\Form\Fieldset */
            $fieldset = \Mage::getModel('\Magento\Eav\Model\Form\Fieldset');
            $fieldset->setTypeId($this->getId())
                ->setCode($skeletonFieldset->getCode())
                ->setLabels($skeletonFieldset->getLabels())
                ->setSortOrder($skeletonFieldset->getSortOrder())
                ->save();
            $fieldsetMap[$skeletonFieldset->getId()] = $fieldset->getId();
        }

        // copy elements
        foreach ($elementCollection as $skeletonElement) {
            /* @var $skeletonElement \Magento\Eav\Model\Form\Element */
            $element = \Mage::getModel('\Magento\Eav\Model\Form\Element');
            $fieldsetId = null;
            if ($skeletonElement->getFieldsetId()) {
                $fieldsetId = $fieldsetMap[$skeletonElement->getFieldsetId()];
            }
            $element->setTypeId($this->getId())
                ->setFieldsetId($fieldsetId)
                ->setAttributeId($skeletonElement->getAttributeId())
                ->setSortOrder($skeletonElement->getSortOrder());
        }

        return $this;
    }
}
