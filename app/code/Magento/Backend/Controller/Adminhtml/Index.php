<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\App\AbstractAction;

/**
 * Index backend controller
 */
class Index extends AbstractAction
{
    /**
     * Search modules list
     *
     * @var array
     */
    protected $_searchModules;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param array $searchModules
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, array $searchModules = array())
    {
        $this->_searchModules = $searchModules;
        parent::__construct($context);
    }

    /**
     * Global Search Action
     *
     * @return void
     */
    public function globalSearchAction()
    {
        $items = array();

        if (!$this->_authorization->isAllowed('Magento_Adminhtml::global_search')) {
            $items[] = array(
                'id' => 'error',
                'type' => __('Error'),
                'name' => __('Access Denied'),
                'description' => __('You need more permissions to do this.')
            );
        } else {
            if (empty($this->_searchModules)) {
                $items[] = array(
                    'id' => 'error',
                    'type' => __('Error'),
                    'name' => __('No search modules were registered'),
                    'description' => __(
                        'Please make sure that all global admin search modules are installed and activated.'
                    )
                );
            } else {
                $start = $this->getRequest()->getParam('start', 1);
                $limit = $this->getRequest()->getParam('limit', 10);
                $query = $this->getRequest()->getParam('query', '');
                foreach ($this->_searchModules as $searchConfig) {

                    if ($searchConfig['acl'] && !$this->_authorization->isAllowed($searchConfig['acl'])) {
                        continue;
                    }

                    $className = $searchConfig['class'];
                    if (empty($className)) {
                        continue;
                    }
                    $searchInstance = $this->_objectManager->create($className);
                    $results = $searchInstance->setStart(
                        $start
                    )->setLimit(
                        $limit
                    )->setQuery(
                        $query
                    )->load()->getResults();
                    $items = array_merge_recursive($items, $results);
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($items)
        );
    }

    /**
     * Change locale action
     *
     * @return void
     */
    public function changeLocaleAction()
    {
        $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Admin area entry point
     * Always redirects to the startup page url
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_redirect($this->_backendUrl->getStartupPageUrl());
    }
}
