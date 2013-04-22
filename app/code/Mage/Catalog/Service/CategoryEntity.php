<?php
/**
 * Catalog Category Entity Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
class Mage_Catalog_Service_CategoryEntity extends Mage_Core_Service_Type_Abstract
{
    /**
     * Return resource object or resource object data.
     *
     * @param mixed $request
     * @return Mage_Catalog_Model_Category
     */
    public function item($request)
    {
        $request = $this->prepareRequest(get_class($this), 'item', $request);

        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('Mage_Catalog_Model_Category');

        // `set` methods are creating troubles
        foreach ($request->getData() as $k => $v) {
            $category->setDataUsingMethod($k, $v);
        }

        $id = $category->getId();
        if ($id) {
            // TODO: we need this trick as because of improper handling when target record doesn't exist
            $category->setId(null);
            $category->load($id);
        }

        $this->prepareResponse(get_class($this), 'item', $category, $request);

        return $category;
    }

    /**
     * Returns collection of resource objects.
     *
     * @param mixed $request
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function items($request)
    {
        $request = $this->prepareRequest(get_class($this), 'items', $request);

        /** @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');

        $categoryIds = $request->getCategoryIds();
        $collection->addIdFilter($categoryIds);

        $filters = $request->getFilters();

        foreach ($filters as $field => $value) {
            // $field === 'offset'

            // $field === 'limit'

            // $field === 'sort'

            // $field === '{attribute_code}'
        }

        // @todo or not TODO
        $collection->load();

        $this->prepareResponse(get_class($this), 'items', $collection, $request);

        return $collection;
    }

    public function create($request)
    {
        $request = $this->prepareRequest(get_class($this), 'create', $request);

        $this->_save($request);
    }

    public function update($request)
    {
        $request = $this->prepareRequest(get_class($this), 'update', $request);

        $this->_save($request);
    }

    public function delete($request)
    {
        //
    }

    /**
     * Move category action
     *
     * @param mixed $request
     * @return Varien_Object
     */
    public function move($request)
    {
        $request = $this->prepareRequest(get_class($this), 'move', $request);

        $category = $this->item($request);
        if (!$category->getId()) {
            return false;
        }

        /**
         * New parent category identifier
         */
        $parentNodeId = $request->getPid();
        /**
         * Category id after which we have put our category
         */
        $prevNodeId = $request->getAid();

        // TODO move logic out from model
        $category->move($parentNodeId, $prevNodeId);

        return true;
    }

    /**
     * @param mixed $request
     * @return $category | false
     */
    public function initCategoryToView($request)
    {
        try {
            $category = $this->call('item', $request);

            if (!$this->canShow($category)) {
                return false;
            }
        } catch (Mage_Core_Service_Exception $e) {
            $code = $e->getCode() ? $e->getCode() : Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR;
            throw new Mage_Core_Service_Exception($e->getMessage(), $code);
        } catch (Exception $e) {
            throw new Mage_Core_Service_Exception($e->getMessage(), Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR);
        }

        return $category;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Save object.
     *
     * @param mixed $request
     * @return Varien_Object $category
     */
    protected function _save($request)
    {
        $category = $this->item($request);
        if (!$category->getId()) {
            return false;
        }

        $this->_objectManager->get('Mage_Core_Model_Event_Manager')->dispatch(
            'catalog_category_prepare_save',
            array(
                'category' => $category,
                'request'  => $request
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
