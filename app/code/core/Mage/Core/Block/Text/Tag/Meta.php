<?php

class Mage_Core_Block_Text_Tag_Meta extends Mage_Core_Block_Text
{
    public function toHtml()
    {
        if (!$this->getContentType()) {
            $this->setContentType('text/html; charset=utf8');
        }
        $this->addText('<meta http-equiv="Content-Type" content="'.$this->getContentType().'"/>'."\n");
        $this->addText('<title>'.$this->getTitle().'</title>'."\n");
        $this->addText('<meta name="title" content="'.$this->getTitle().'"/>'."\n");
        $this->addText('<meta name="description" content="'.$this->getDescription().'"/>'."\n");
        $this->addText('<meta name="keywords" content="'.$this->getKeywords().'"/>'."\n");
        $this->addText('<meta name="robots" content="'.$this->getRobots().'"/>'."\n");
        
        return parent::toHtml();
    }
}