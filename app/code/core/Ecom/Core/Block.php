<?php

/**
 * Block factory
 * 
 * For block generation you must define Data source class, data source class method,
 * parameters array and block template
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Moshe Gurvich <moshe@varien.com>
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 */

class Ecom_Core_Block
{
    /**
     * Block types
     *
     * @var    array
     */
    private static $_blockTypes;
    
    /**
     * Block templates
     *
     * @var array
     */
    private static $_blockTemplates;
    
    /**
     * Blocks registry
     *
     * @var array
     */
    private static $_blocks;
    
    /**
     * Block groups for saving and retrieving
     *
     * @var array
     */
    private static $_groups;
    
    /**
     * Save block in blocks registry
     *
     * @param string $name
     * @param Ecom_Core_Block_Abstract $block
     */
    public static function setBlock($name, $block)
    {
        self::$_blocks[$name] = $block;
    }
    
    /**
     * Remove block from registry
     *
     * @param string $name
     */
    public static function unsetBlock($name)
    {
        self::$_blocks[$name] = null;
        unset(self::$_blocks[$name]);
    }
    
    /**
     * Block Factory
     * 
     * @param     string $type
     * @param     string $blockName
     * @param     array $attributes
     * @return	  Ecom_Core_Block_Abtract
     * @author    Moshe Gurvich <moshe@varien.com>
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */
    public static function createBlock($type, $name='', array $attributes = array())
    {
        #Ecom::setTimer(__METHOD__);

       	if (empty(self::$_blockTypes[$type])) {
       	    Ecom::exception('Invalid block type ' . $type);
       	}

   	    $className = self::$_blockTypes[$type];
	    #Ecom::loadClass($className);
	    $block = new $className();
	    
       	if (empty($name) || '.'===$name{0}) {
       	    $block->setInfo('anonymous', true);
   	        if (!empty($name)) {
   	            $block->setInfo('anonSuffix', substr($name, 1));
   	        }
   	        $name = 'ANONYMOUS_'.sizeof(self::$_blocks);
   	    }
   	    
	    $block->setInfo(array('type'=>$type, 'name'=>$name));
	    $block->setAttribute($attributes);
	    
	    self::$_blocks[$name] = $block;
	    
        #Ecom::setTimer(__METHOD__, true);

		return self::$_blocks[$name];
    }
    
    public static function createBlockLike($template, $name='', array $attributes = array())
    {
       	if (empty(self::$_blockTemplates[$template])) {
       	    Ecom::exception('Invalid block template ' . $template);
       	}
       	
       	$tpl = array_merge_recursive(self::$_blockTemplates[$template], $attributes);
       	
       	$block = self::createBlock($tpl['type'], $name, $tpl);
       	$block->setInfo(array('template'=>$template));
       	
        return $block;
    }
    
    public static function getAllBlocks()
    {
        return self::$_blocks;
    }
    
    public static function getBlockByName($name)
    {
        if (isset(self::$_blocks[$name])) {
            return self::$_blocks[$name];
        } else {
            return false;
        }
    }
    
    public static function getBlocksByGroup($groupName)
    {
        $blocks = array();
        foreach (self::$_blocks as $name=>$block) {
            if ($block->getInfo('groupName')==$groupName) {
                $blocks[$name] = $block;
            }
        }
        return $blocks;
    }
    
    /**
     * Load module block info
     * 
     * @param     none
     * @return	  none
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public static function loadTypesConfig($config)
    {
        if (!$config instanceof Zend_Config) {
           Ecom::exception('Block types config has to be Zend_Config type');
        }
        
        $arrBlocks = $config->asArray();
        if (is_array($arrBlocks)) {
        	foreach ($arrBlocks as $blockType => $blockClass) {
        		if(isset(self::$_blockTypes[$blockType])){
        		    Ecom::exception('Block type ' . $blockType . ' already exist');
        		}
        		self::$_blockTypes[$blockType] = $blockClass;
        	}
        }
    }
    
    public static function loadTemplatesConfig($config)
    {
        if (!$config instanceof Zend_Config) {
           Ecom::exception('Block templates config has to be Zend_Config type');
        }
        
        $arrBlocks = $config->asArray();
        if (is_array($arrBlocks)) {
        	foreach ($arrBlocks as $blockTemplate => $attributes) {
        		if(isset(self::$_blockTemplates[$blockTemplate])){
        		    Ecom::exception('Block template ' . $blockTemplate . ' already exist');
        		}
        		self::$_blockTemplates[$blockTemplate] = $attributes;
        	}
        }
    }
    
    
    /**
     * Parse and run block script array
     * 
     */
    static public function loadArray($arr, $block=null)
    {
        if (!is_array($arr) || empty($arr[0]) || !is_string($arr[0])) {
            return $arr;
        }
       
        $type = $arr[0]{0};
        $entity = substr($arr[0], 1);
        array_shift($arr);
        
        switch ($type) {
            
            case ':': 
                // declare layoutUpdate name and check for dependancies
                $blocks = array();
                foreach ($arr as $block) {
                    $blocks[] = self::loadArray($block);
                }
                $result = $blocks;  
                break;    
                                
            case '+': 
            case '#': 
                if ('+'===$type) {
                    // create new block using this entity as block type
                    if (!empty($arr[0]) && is_string($arr[0]) && ('#'===$arr[0]{0})) {
                        $name = substr($arr[0], 1);
                        array_shift($arr);
                    } else {
                        $name = '';
                    }
                    $block = self::createBlock($entity, $name);
                } else {
                    // retrieve existing block by entity as name
                    $block = self::getBlockByName($entity);
                }
                
                if (empty($block)) {
                    $result = false;
                    break;
                }
                foreach ($arr as $action) {
                    self::loadArray($action, $block);
                }
                $result = $block;
                break;
                
            case '>': 
                // run action method for $block
                if (empty($block)) {
                    $result = false;
                    break;
                }
                $args = array();
                foreach ($arr as $arg) {
                    $args[] = self::loadArray($arg);
                }
                $result = call_user_func_array(array($block, $entity), $args);
                break;
        }
        
        return $result;
    }
    
}// Class Ecom_Home_ContentBlock END