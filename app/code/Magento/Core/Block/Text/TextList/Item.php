<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * List item block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Block\Text\TextList;

class Item extends \Magento\Core\Block\Text
{
    function setLink($liParams, $innerText)
    {
        $this->setLiParams($liParams);
        $this->setInnerText($innerText);

        return $this;
    }

    protected function _toHtml()
    {
        $this->setText('<li');
        $params = $this->getLiParams();
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key=>$value) {
                $this->addText(' '.$key.'="'.addslashes($value).'"');
            }
        } elseif (is_string($params)) {
            $this->addText(' '.$params);
        }
        $this->addText('>'.$this->getInnerText().'</li>'."\r\n");

        return parent::_toHtml();
    }

}
