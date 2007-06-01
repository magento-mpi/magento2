<?php
/**
 * Image handler library
 *
 * @package     Varien
 * @subpackage  Image
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Varien_Image
{
    protected $_adapter;

    protected $_fileName;
    
    public function __construct($adapter=Varien_Image_Adapter::ADAPTER_GD2, $fileName=null)
    {
        $this->_getAdapter($adapter);
        $this->_fileName = $fileName;
    }
    
    protected function _getAdapter($adapter=null)
    {
        if( !isset($this->_adapter) ) {
            $this->_adapter = Varien_Image_Adapter::factory( $adapter );
        }
        return $this->_adapter;
    }
    
    public function open()
    {
        $this->_getAdapter()->open($this->_fileName);
    }
    
    public function display()
    {
        $this->_getAdapter()->display();
    }
    
    public function save($destination=null, $newFileName=null)
    {
        $this->_getAdapter()->save($destination, $newFileName);
    }
    
    public function rotate()
    {
        
    }
    
    public function crop()
    {
        
    }
    
    public function resize($width=null, $height=null)
    {
        $this->_getAdapter()->resize($width, $height);
    }
    
    public function getMimeType()
    {
        return $this->_getAdapter()->getMimeType();
    }

    public function getCopy()
    {
        return clone $this;
    }
    
    public function process()
    {
        
    }

    public function instruction()
    {
        
    }
}
