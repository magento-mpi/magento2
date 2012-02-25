<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Breadcrumbs navigation for the current page
 */
class Mage_DesignEditor_Block_Toolbar_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Retrieve breadcrumbs for the current page location
     *
     * Result format:
     * array(
     *     array(
     *         'label' => 'Some Page Type',
     *         'url'   => http://localhost/index.php/design/editor/page/page_type/some_page_type/',
     *     ),
     *     // ...
     * )
     *
     * @return array
     */
    public function getBreadcrumbs()
    {
        $result = array();
        $layoutUpdate = $this->getLayout()->getUpdate();
        $pageTypes = $layoutUpdate->getPageHandles();
        if (!$pageTypes) {
            /** @var $controllerAction Mage_Core_Controller_Varien_Action */
            $controllerAction = Mage::app()->getFrontController()->getAction();
            if ($controllerAction) {
                $pageTypes = $layoutUpdate->getPageLayoutHandles($controllerAction->getDefaultLayoutHandle());
            }
        }
        foreach ($pageTypes as $pageTypeName) {
            $result[] = array(
                'label' => $this->escapeHtml($layoutUpdate->getPageTypeLabel($pageTypeName)),
                'url'   => $this->getUrl('design/editor/page', array('page_type' => $pageTypeName))
            );
        }
        /** @var $blockHead Mage_Page_Block_Html_Head */
        $blockHead = $this->getLayout()->getBlock('head');
        if ($blockHead && !$this->getOmitCurrentPage()) {
            $result[] = array(
                'label' => $blockHead->getTitle(),
                'url'   => '',
            );
        } else if ($result) {
            $result[count($result) - 1]['url'] = '';
        }
        return $result;
    }
}
