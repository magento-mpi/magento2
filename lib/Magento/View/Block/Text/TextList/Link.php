<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Block\Text\TextList;

use Magento\View\Block\Text;

class Link extends \Magento\View\Block\Text
{
    public function setLink($liParams, $aParams, $innerText, $afterText='')
    {
        $this->setLiParams($liParams);
        $this->setAParams($aParams);
        $this->setInnerText($innerText);
        $this->setAfterText($afterText);

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
        $this->addText('><a');

        $params = $this->getAParams();
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key=>$value) {
                $this->addText(' '.$key.'="'.addslashes($value).'"');
            }
        } elseif (is_string($params)) {
            $this->addText(' '.$params);
        }

        $this->addText('>'.$this->getInnerText().'</a>'.$this->getAfterText().'</li>'."\r\n");

        return parent::_toHtml();
    }
}
