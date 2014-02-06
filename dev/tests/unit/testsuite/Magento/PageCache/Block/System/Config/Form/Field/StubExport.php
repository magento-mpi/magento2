<?php
/**
 * {license_notice}
 * Page cache data helper
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Block\System\Config\Form\Field;

class StubExport extends \Magento\PageCache\Block\System\Config\Form\Field\Export
{
    /**
     * Disable parent constructor
     */
    public function __construct()
    {
    }

    public function setUrlBuilder(\Magento\UrlInterface $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;
    }
    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function getElementHtml($element)
    {
        return $this->_getElementHtml($element);
    }
}
