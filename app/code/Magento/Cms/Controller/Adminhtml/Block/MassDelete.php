<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Adminhtml\Block;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = 'block_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Magento\Cms\Model\Resource\Block\Collection';

    /**
     * Block model
     *
     * @var string
     */
    protected $model = 'Magento\Cms\Model\Block';

    /**
     * Execute action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('massaction', '[]');
        $data = json_decode($data, true);

        try {
            if (isset($data['all_selected']) && $data['all_selected'] === true) {
                if (!empty($data['excluded'])) {
                    $this->excludedDelete($data['excluded']);
                } else {
                    $this->deleteAll();
                }
            } elseif (!empty($data['selected'])) {
                $this->selectedDelete($data['selected']);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
                $this->_redirect('cms/*/index');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->_redirect('cms/*/index');
    }

    /**
     * Delete all
     *
     * @return void
     * @throws \Exception
     */
    protected function deleteAll()
    {
        /** @var \Magento\Cms\Model\Resource\Block\Collection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->_objectManager->get($this->model);
            $model->load($id);
            $model->delete();
            ++$count;
        }
        $this->setSuccessMessage($count);
    }

    /**
     * Delete all but the not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedDelete(array $excluded)
    {
        /** @var \Magento\Cms\Model\Resource\Block\Collection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->_objectManager->get($this->model);
            $model->load($id);
            $model->delete();
            ++$count;
        }
        $this->setSuccessMessage($count);
    }

    /**
     * Delete selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedDelete(array $selected)
    {
        /** @var \Magento\Cms\Model\Resource\Block\Collection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Cms\Model\Block $model */
            $model = $this->_objectManager->get($this->model);
            $model->load($id);
            $model->delete();
            ++$count;
        }
        $this->setSuccessMessage($count);
    }

    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }
}
