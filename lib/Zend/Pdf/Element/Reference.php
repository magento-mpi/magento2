<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Pdf_Element */
#require_once 'Zend/Pdf/Element.php';

/** Zend_Pdf_Element_Reference_Context */
#require_once 'Zend/Pdf/Element/Reference/Context.php';

/** Zend_Pdf_Element_Reference_Table */
#require_once 'Zend/Pdf/Element/Reference/Table.php';

/** Zend_Pdf_ElementFactory */
#require_once 'Zend/Pdf/ElementFactory.php';


/**
 * PDF file 'reference' element implementation
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Element_Reference extends Zend_Pdf_Element
{
    /**
     * Object value
     * The reference to the object
     *
     * @var mixed
     */
    private $_ref;

    /**
     * Object number within PDF file
     *
     * @var integer
     */
    private $_objNum;

    /**
     * Generation number
     *
     * @var integer
     */
    private $_genNum;

    /**
     * Reference context
     *
     * @var Zend_Pdf_Element_Reference_Context
     */
    private $_context;


    /**
     * Reference to the factory.
     *
     * It's the same as referenced object factory, but we save it here to avoid
     * unnecessary dereferencing, whech can produce cascade dereferencing and parsing.
     * The same for duplication of getFactory() function. It can be processed by __call()
     * method, but we catch it here.
     *
     * @var Zend_Pdf_ElementFactory
     */
    private $_factory;

    /**
     * Object constructor:
     *
     * @param integer $objNum
     * @param integer $genNum
     * @param Zend_Pdf_Element_Reference_Context $context
     * @param Zend_Pdf_ElementFactory $factory
     * @throws Zend_Pdf_Exception
     */
    public function __construct($objNum, $genNum = 0, Zend_Pdf_Element_Reference_Context $context, Zend_Pdf_ElementFactory $factory)
    {
        if ( !(is_integer($objNum) && $objNum > 0) ) {
            throw new Zend_Pdf_Exception('Object number must be positive integer');
        }
        if ( !(is_integer($genNum) && $genNum >= 0) ) {
            throw new Zend_Pdf_Exception('Generation number must be non-negative integer');
        }

        $this->_objNum  = $objNum;
        $this->_genNum  = $genNum;
        $this->_ref     = null;
        $this->_context = $context;
        $this->_factory = $factory;
    }

    /**
     * Check, that object is generated by specified factory
     *
     * @return Zend_Pdf_ElementFactory
     */
    public function getFactory()
    {
        return $this->_factory;
    }


    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        if ($this->_ref === null) {
            $this->_dereference();
        }

        return $this->_ref->getType();
    }


    /**
     * Return reference to the object
     *
     * @param Zend_Pdf_Factory $factory
     * @return string
     */
    public function toString($factory = null)
    {
        if ($factory === null) {
            $shift = 0;
        } else {
            $shift = $factory->getEnumerationShift($this->_factory);
        }

        return $this->_objNum + $shift . ' ' . $this->_genNum . ' R';
    }


    /**
     * Dereference.
     * Take inderect object, take $value member of this object (must be Zend_Pdf_Element),
     * take reference to the $value member of this object and assign it to
     * $value member of current PDF Reference object
     * $obj can be null
     *
     * @throws Zend_Pdf_Exception
     */
    private function _dereference()
    {
        $obj = $this->_context->getParser()->getObject(
                       $this->_context->getRefTable()->getOffset($this->_objNum . ' ' . $this->_genNum . ' R'),
                       $this->_context
                                                      );

        if ($obj === null ) {
            $this->_ref = new Zend_Pdf_Element_Null();
            return;
        }

        if ($obj->toString() != $this->_objNum . ' ' . $this->_genNum . ' R') {
            throw new Zend_Pdf_Exception('Incorrect reference to the object');
        }

        $this->_ref = $obj;
        $this->setParentObject($obj);

        $this->_factory->registerObject($this);
    }

    /**
     * Mark object as modified, to include it into new PDF file segment.
     */
    public function touch()
    {
        if ($this->_ref === null) {
            $this->_dereference();
        }

        $this->_ref->touch();
    }


    /**
     * Get handler
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if ($this->_ref === null) {
            $this->_dereference();
        }

        return $this->_ref->$property;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($property, $value)
    {
        if ($this->_ref === null) {
            $this->_dereference();
        }

        $this->_ref->$property = $value;
    }

    /**
     * Call handler
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if ($this->_ref === null) {
            $this->_dereference();
        }

        switch (count($args)) {
            case 0:
                return $this->_ref->$method();
            case 1:
                return $this->_ref->$method($args[0]);
            case 2:
                return $this->_ref->$method($args[0], $args[1]);
            case 3:
                return $this->_ref->$method($args[0], $args[1], $args[2]);
            case 4:
                return $this->_ref->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                throw new Zend_Pdf_Exception('Unsupported number of arguments');
        }
    }

    /**
     * Clean up resources
     */
    public function cleanUp()
    {
        $this->_ref = null;
    }

    /**
     * Convert PDF element to PHP type.
     *
     * @return mixed
     */
    public function toPhp()
    {
        if ($this->_ref === null) {
            $this->_dereference();
        }

        return $this->_ref->toPhp();
    }
}
