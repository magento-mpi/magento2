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
 * @method Magento_Eav_Model_Resource_Form_Type _getResource()
 * @method Magento_Eav_Model_Resource_Form_Type getResource()
 * @method string getCode()
 * @method Magento_Eav_Model_Form_Type setCode(string $value)
 * @method string getLabel()
 * @method Magento_Eav_Model_Form_Type setLabel(string $value)
 * @method int getIsSystem()
 * @method Magento_Eav_Model_Form_Type setIsSystem(int $value)
 * @method string getTheme()
 * @method Magento_Eav_Model_Form_Type setTheme(string $value)
 * @method int getStoreId()
 * @method Magento_Eav_Model_Form_Type setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Form_Type extends Magento_Core_Model_Abstract
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
        $this->_init('Magento_Eav_Model_Resource_Form_Type');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Magento_Eav_Model_Resource_Form_Type
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve resource collection instance wrapper
     *
     * @return Magento_Eav_Model_Resource_Form_Type_Collection
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
     * @return Magento_Eav_Model_Form_Type
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
     * @return Magento_Eav_Model_Form_Type
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
     * @param Magento_Eav_Model_Form_Type $skeleton
     * @return Magento_Eav_Model_Form_Type
     */
    public function createFromSkeleton(Magento_Eav_Model_Form_Type $skeleton)
    {
        $fieldsetCollection = Mage::getModel('Magento_Eav_Model_Form_Fieldset')->getCollection()
            ->addTypeFilter($skeleton)
            ->setSortOrder();
        $elementCollection = Mage::getModel('Magento_Eav_Model_Form_Element')->getCollection()
            ->addTypeFilter($skeleton)
            ->setSortOrder();

        // copy fieldsets
        $fieldsetMap = array();
        foreach ($fieldsetCollection as $skeletonFieldset) {
            /* @var $skeletonFieldset Magento_Eav_Model_Form_Fieldset */
            $fieldset = Mage::getModel('Magento_Eav_Model_Form_Fieldset');
            $fieldset->setTypeId($this->getId())
                ->setCode($skeletonFieldset->getCode())
                ->setLabels($skeletonFieldset->getLabels())
                ->setSortOrder($skeletonFieldset->getSortOrder())
                ->save();
            $fieldsetMap[$skeletonFieldset->getId()] = $fieldset->getId();
        }

        // copy elements
        foreach ($elementCollection as $skeletonElement) {
            /* @var $skeletonElement Magento_Eav_Model_Form_Element */
            $element = Mage::getModel('Magento_Eav_Model_Form_Element');
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
