<?php
/**
 * Catalog Category Entity Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @service true
 */
class Mage_Catalog_Service_CategoryEntity extends Mage_Core_Service_Type_Entity_Abstract
{
    public function create($context)
    {
        //
    }

    /**
     * Return resource object or resource object data.
     *
     * @param mixed $context
     * @return Mage_Catalog_Model_Category
     */
    public function item($context)
    {
        $context = $this->prepareContext(get_class($this), 'item', $context);

        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('Mage_Catalog_Model_Category');

        // `set` methods are creating troubles
        foreach ($context->getData() as $k => $v) {
            $category->setDataUsingMethod($k, $v);
        }

        $id = $category->getId();
        if ($id) {
            // TODO: we need this trick as because of improper handling when target record doesn't exist
            $category->setId(null);
            $category->load($id);
        }

        $this->prepareResponse(get_class($this), 'item', $category, $context);

        return $category;
    }

    /**
     * Returns collection of resource objects.
     *
     * @param mixed $context
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function items($context)
    {
        $context = $this->prepareContext(get_class($this), 'items', $context);

        /** @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');

        $categoryIds = $context->getCategoryIds();
        $collection->addIdFilter($categoryIds);

        $filters = $context->getFilters();

        foreach ($filters as $field => $value) {
            // $filters['offset']

            // $filters['limit']

            // $filters['sort']

            // $filters['{attribute_code}']
        }

        // TODO or not TODO
        //$collection->load();

        $this->prepareResponse(get_class($this), 'items', $collection, $context);

        return $collection;
    }

    public function update($context)
    {
        //
    }

    public function delete($context)
    {
        //
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Save object.
     *
     * @param mixed $context
     * @return Varien_Object $category
     */
    protected function _save($context)
    {
        $context = $this->prepareContext(get_class($this), 'save', $context);

        $category = $this->item($context);
        if (!$category->getId()) {
            return false;
        }

        $this->_objectManager->get('Mage_Core_Model_Event_Manager')->dispatch(
            'catalog_category_prepare_save',
            array(
                'category' => $category,
                'context'  => $context
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

        return $category;
    }

    /**
     * Move category action
     *
     * @param mixed $context
     * @return Varien_Object
     */
    public function move($context)
    {
        $context = $this->prepareContext(get_class($this), 'move', $context);

        $category = $this->item($context);
        if (!$category->getId()) {
            return false;
        }

        /**
         * New parent category identifier
         */
        $parentNodeId = $context->getPid();
        /**
         * Category id after which we have put our category
         */
        $prevNodeId = $context->getAid();

        // TODO move logic out from model
        $category->move($parentNodeId, $prevNodeId);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Check if a category can be shown
     *
     * @param  Mage_Catalog_Model_Category $category
     * @return boolean
     */
    public function canShow($category)
    {
        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }
        if (!$category->isInRootCategoryList()) {
            return false;
        }

        return true;
    }
}
