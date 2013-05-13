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
class Mage_Catalog_Service_CategoryService extends Mage_Core_Service_Type_Abstract
{
    /**
     * Return resource object or resource object data.
     *
     * @param mixed $request
     * @param mixed $version [optional]
     * @return Mage_Catalog_Model_Category
     */
    public function item($request, $version = null)
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

        $this->prepareModel(get_class($this), 'item', $category, $request);

        return $category;
    }

    /**
     * Returns collection of resource objects.
     *
     * @param mixed $request
     * @param mixed $version [optional]
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function items($request, $version = null)
    {
        $request = $this->prepareRequest(get_class($this), 'items', $request);

        /** @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');

        $helper = $this->_serviceManager->getServiceHelper('Mage_Core_Service_Helper_Filters');

        $helper->applyPaginationToCollection($collection, $request);

        $filters = $request->getFilters();
        if ($filters) {
            $helper->applyFiltersToCollection($collection, $filters);
        }

        // @todo or not TODO
        $collection->load();

        $this->prepareCollection(get_class($this), 'items', $collection, $request);

        return $collection;
    }

    /**
     * @param $request
     * @param mixed $version [optional]
     */
    public function create($request, $version = null)
    {
        $request = $this->prepareRequest(get_class($this), 'create', $request);

        $this->_save($request);
    }

    /**
     * @param $request
     * @param mixed $version [optional]
     */
    public function update($request, $version = null)
    {
        $request = $this->prepareRequest(get_class($this), 'update', $request);

        $this->_save($request);
    }

    public function delete($request, $version = null)
    {
        //
    }

    /**
     * Move category action
     *
     * @param mixed $request
     * @param mixed $version [optional]
     * @return Varien_Object
     */
    public function move($request, $version = null)
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
     * @param mixed $version [optional]
     * @return $category | false
     */
    public function init($request)
    {
        try {
            $category = $this->call('item', $request);

            if (!$this->canShow($category)) {
                return false;
            }
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
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
