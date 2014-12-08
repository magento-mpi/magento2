<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy;

class Save extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy
{
    /**
     * Save changes
     *
     * @return void
     */
    public function execute()
    {
        $this->_initScope();
        if ($this->getRequest()->isPost()) {
            /** @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
            $node = $this->_objectManager->create(
                'Magento\VersionsCms\Model\Hierarchy\Node',
                ['data' => ['scope' => $this->_scope, 'scope_id' => $this->_scopeId]]
            );
            $data = $this->getRequest()->getPost();
            $hasError = true;

            try {
                if (isset($data['use_default_scope_property']) && $data['use_default_scope_property']) {
                    $node->deleteByScope($this->_scope, $this->_scopeId);
                } else {
                    if (!empty($data['nodes_data'])) {
                        try {
                            $nodesData = $this->_objectManager->get(
                                'Magento\Core\Helper\Data'
                            )->jsonDecode(
                                $data['nodes_data']
                            );
                        } catch (\Zend_Json_Exception $e) {
                            $nodesData = [];
                        }
                    } else {
                        $nodesData = [];
                    }
                    if (!empty($data['removed_nodes'])) {
                        $removedNodes = explode(',', $data['removed_nodes']);
                    } else {
                        $removedNodes = [];
                    }

                    // fill in meta_chapter and meta_section based on meta_chapter_section
                    foreach ($nodesData as &$n) {
                        $n['meta_chapter'] = 0;
                        $n['meta_section'] = 0;
                        if (!isset($n['meta_chapter_section'])) {
                            continue;
                        }
                        if ($n['meta_chapter_section'] == 'both' || $n['meta_chapter_section'] == 'chapter') {
                            $n['meta_chapter'] = 1;
                        }
                        if ($n['meta_chapter_section'] == 'both' || $n['meta_chapter_section'] == 'section') {
                            $n['meta_section'] = 1;
                        }
                    }

                    $node->collectTree($nodesData, $removedNodes);
                }

                $hasError = false;
                $this->messageManager->addSuccess(__('You have saved the hierarchy.'));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the hierarchy.'));
            }

            if ($hasError) {
                //save data in session
                $this->_getSession()->setFormData($data);
            }
        }

        $this->_redirect('adminhtml/*/index', ['website' => $this->_website, 'store' => $this->_store]);
        return;
    }
}
