<?php
/**
 * Catalog Category Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @service true
 */
class Mage_Catalog_Service_Category extends Mage_Core_Service_Abstract
{
    const SERVICE_ID = 'catalogCategory';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function __construct(
        Magento_ObjectManager $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return resource object or resource object data.
     *
     * @param Mage_Core_Service_Args $args
     * @return Mage_Catalog_Model_Category | array
     */
    public function item(Mage_Core_Service_Args $args)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('Mage_Catalog_Model_Category');

        // `set` methods are creating troubles
        foreach ($args->getData() as $k => $v) {
            $category->setDataUsingMethod($k, $v);
        }

        $id = $category->getId();
        if ($id) {
            // TODO: we need this trick as because of improper handling when target record doesn't exist
            $category->setId(null);
            $category->load($id);
        }

        if ($args->getAsArray()) {
            // fake conversion to an array
            return $category->toArray();
        }

        return $category;
    }

    /**
     * Returns collection of resource objects.
     *
     * @param Mage_Core_Service_Args $args
     * @return mixed
     */
    public function items(Mage_Core_Service_Args $args)
    {
        /** @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');

        $categoryIds = $args->getCategoryIds();
        $collection->addIdFilter($categoryIds);

        $filters = $args->getFilters();
        $collection->addAttributeToFilter($filters);

        // TODO or not TODO
        //$collection->load();

        if (!$args->getAsObject()) {
            // fake conversion to an array
            return $collection->toArray();
        }

        return $collection;
    }

    /**
     * Save object.
     *
     * @param Mage_Core_Service_Args $args
     * @return Varien_Object | array
     */
    public function save(Mage_Core_Service_Args $args)
    {
        $category = $this->item($args);
        if (!$category->getId()) {
            return false;
        }

        $this->_objectManager->get('Mage_Core_Model_Event_Manager')->dispatch(
            'catalog_category_prepare_save',
            array(
                'category' => $category,
                'args'     => $args
            )
        );

        $validate = $category->validate();

        if ($validate !== true) {
            foreach ($validate as $code => $error) {
                if ($error === true) {
                    $attribute = $category->getResource()->getAttribute($code)->getFrontend()->getLabel();
                    throw new Mage_Core_Exception(
                        $this->__('Attribute "%s" is required.', $attribute)
                    );
                } else {
                    throw new Mage_Core_Exception($error);
                }
            }
        }

        $category->save();

        return true;
    }

    /**
     * Move category action
     *
     * @param Mage_Core_Service_Args $args
     * @return Varien_Object | array
     */
    public function move(Mage_Core_Service_Args $args)
    {
        $category = $this->item($args);
        if (!$category->getId()) {
            return false;
        }

        /**
         * New parent category identifier
         */
        $parentNodeId   = $args->getPid();
        /**
         * Category id after which we have put our category
         */
        $prevNodeId     = $args->getAid();

        // TODO move logic out from model
        $category->move($parentNodeId, $prevNodeId);

        return true;
    }

    /**
     * Returns unique service identifier.
     *
     * @return string
     */
    protected function _getServiceId()
    {
        return self::SERVICE_ID;
    }
}
