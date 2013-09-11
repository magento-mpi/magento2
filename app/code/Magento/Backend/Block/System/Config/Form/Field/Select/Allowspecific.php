<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System configuration shipping methods allow all countries select
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Config\Form\Field\Select;

class Allowspecific extends \Magento\Data\Form\Element\Select
{

    /**
     * Add additional Javascript code
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $javaScript = "
            <script type=\"text/javascript\">
                Event.observe('{$this->getHtmlId()}', 'change', function(){
                    specific=$('{$this->getHtmlId()}').value;
                    $('{$this->_getSpecificCountryElementId()}').disabled = (!specific || specific!=1);
                });
            </script>";
        return $javaScript . parent::getAfterElementHtml();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        if (!$this->getValue() || 1 != $this->getValue()) {
            $element = $this->getForm()->getElement($this->_getSpecificCountryElementId());
            $element->setDisabled('disabled');
        }
        return parent::getHtml();
    }

    /**
     * @return string
     */
    protected function _getSpecificCountryElementId()
    {
        return substr($this->getId(), 0, strrpos($this->getId(), 'allowspecific')) . 'specificcountry';
    }

}
