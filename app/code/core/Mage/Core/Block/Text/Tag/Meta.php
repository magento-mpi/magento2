<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Block_Text_Tag_Meta extends Mage_Core_Block_Text
{
    protected function _toHtml()
    {
        if (!$this->getContentType()) {
            $this->setContentType('text/html; charset=utf-8');
        }
        $this->addText('<meta http-equiv="Content-Type" content="'.$this->getContentType().'"/>'."\n");
        $this->addText('<title>'.$this->getTitle().'</title>'."\n");
        $this->addText('<meta name="title" content="'.$this->getTitle().'"/>'."\n");
        $this->addText('<meta name="description" content="'.$this->getDescription().'"/>'."\n");
        $this->addText('<meta name="keywords" content="'.$this->getKeywords().'"/>'."\n");
        $this->addText('<meta name="robots" content="'.$this->getRobots().'"/>'."\n");

        return parent::_toHtml();
    }
}
