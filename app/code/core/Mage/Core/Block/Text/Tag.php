<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Base html block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Text_Tag extends Mage_Core_Block_Text
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTagParams(array());
    }

    function setTagParam($param, $value=null)
    {
        if (is_array($param) && is_null($value)) {
            foreach ($param as $k=>$v) {
                $this->setTagParam($k, $v);
            }
        } else {
            $params = $this->getTagParams();
            $params[$param] = $value;
            $this->setTagParams($params);
        }
        return $this;
    }

    function setContents($text)
    {
        $this->setTagContents($text);
        return $this;
    }

    protected function _toHtml()
    {
        $this->setText('<'.$this->getTagName().' ');
        if ($this->getTagParams()) {
            foreach ($this->getTagParams() as $k=>$v) {
                $this->addText($k.'="'.$v.'" ');
            }
        }

        $this->addText('>'.$this->getTagContents().'</'.$this->getTagName().'>'."\r\n");
        return parent::_toHtml();
    }

}
