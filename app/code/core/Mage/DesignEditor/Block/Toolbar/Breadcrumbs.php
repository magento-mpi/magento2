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
        $pageTypes = $this->getLayout()->getPageTypesFlat();
        foreach ($this->getLayout()->getUpdate()->getPageHandles() as $pageHandle) {
            if (array_key_exists($pageHandle, $pageTypes)) {
                $pageTypeLabel = $pageTypes[$pageHandle]['label'];
                $result[] = array(
                    'label' => $this->escapeHtml($pageTypeLabel),
                    'url'   => $this->getUrl('design/editor/page', array('page_type' => $pageHandle))
                );
            }
        }
        /** @var $blockHead Mage_Page_Block_Html_Head */
        $blockHead = $this->getLayout()->getBlock('head');
        if ($blockHead && $blockHead->getTitle() != $blockHead->getDefaultTitle()) {
            $result[] = array(
                'label' => $blockHead->getTitle(),
                'url'   => '',
            );
        }
        return $result;
    }
}
