<?php
/**
 * Html page block
 *
 * @package     Mage
 * @subpackage  Page
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Page_Block_Html_Head extends Mage_Core_Block_Text
{
    protected $_additionalCssJs = array();
    
    public function toHtml()
    {
        $this->addText('<title>'.$this->getTitle().'</title>'."\n");
        $this->addText('<meta http-equiv="Content-Type" content="'.$this->getContentType().'"/>'."\n");
        $this->addText('<meta name="title" content="'.$this->getTitle().'"/>'."\n");
        $this->addText('<meta name="description" content="'.$this->getDescription().'"/>'."\n");
        $this->addText('<meta name="keywords" content="'.$this->getKeywords().'"/>'."\n");
        $this->addText('<meta name="robots" content="'.$this->getRobots().'"/>'."\n");
        $this->addText($this->getAdditionalCssJs());
        
        return parent::toHtml();
    }
    
    public function addCss($name)
    {
        $this->_additionalCssJs['css'][] = $name;
        return $this;
    }

    public function addJs($name)
    {
        $this->_additionalCssJs['js'][] = $name;
        return $this;
    }

    public function addCssIe($name)
    {
        $this->_additionalCssJs['cssIe'][] = $name;
        return $this;
    }

    public function addJsIe($name)
    {
        $this->_additionalCssJs['jsIe'][] = $name;
        return $this;
    }
    
    public function getAdditionalCssJs()
    {
        $lines = '';
        foreach (@(array)$this->_additionalCssJs['css'] as $item) {
            $lines .= '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getSkinUrl('css/' . $item) . '" ></link>' . "\n";
        }
        foreach (@(array)$this->_additionalCssJs['cssIe'] as $item) {
            $lines .= '<!--[if IE]> <link rel="stylesheet" type="text/css" media="all" href="' . $this->getSkinUrl('css/' . $item) . '" ></link> <![endif]-->' . "\n";
        }
        foreach (@(array)$this->_additionalCssJs['js'] as $item) {
            $lines .= '<script language="javascript" type="text/javascript" src="' . $this->getSkinUrl('js/' . $item) . '" ></script>' . "\n";
        }
        foreach (@(array)$this->_additionalCssJs['jsIe'] as $item) {
            $lines .= '<!--[if IE]> <script language="javascript" type="text/javascript" src="' . $this->getSkinUrl('js/' . $item) . '" ></script> <![endif]-->' . "\n";
        }
        return $lines;
    }
    
    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
        return $this;
    }
    
    public function getContentType()
    {
        if (!$this->_contentType) {
            return $this->getMediaType().'; charset='.$this->getCharset();
        }
        else {
            return $this->_contentType;
        }
    }
    
    public function setMediaType($mediaType)
    {
        $this->_mediaType = $mediaType;
        return $this;
    }

    public function getMediaType()
    {
        if (!$this->_mediaType) {
            return $this->getDesignConfig('page/head/media_type');
        }
        else {
            return $this->_mediaType;
        }        
    }
    
    public function setCharset($charset)
    {
        $this->_charset = $charset;
        return $this;
    }

    public function getCharset()
    {
        if (!$this->_charset) {
            return $this->getDesignConfig('page/head/charset');
        }
        else {
            return $this->_charset;
        }        
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }
   
    public function getTitle()
    {
        if (!$this->_title) {
            $this->_title = $this->getDesignConfig('page/head/title');
        }
        return $this->_title;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
   
    public function getDescription()
    {
        if (!$this->_description) {
            $this->_description = $this->getDesignConfig('page/head/description');
        }
        return $this->_description;
    }

    public function setKeywords($keywords)
    {
        $this->_keywords = $keywords;
        return $this;
    }
   
    public function getKeywords()
    {
        if (!$this->_keywords) {
            $this->_keywords = $this->getDesignConfig('page/head/keywords');
        }
        return $this->_keywords;
    }

    public function setRobots($robots)
    {
        $this->_robots = $robots;
        return $this;
    }
   
    public function getRobots()
    {
        if (!$this->_robots) {
            $this->_robots = $this->getDesignConfig('page/head/robots');
        }
        return $this->_robots;
    }

}
