<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Gdata
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 *
 * @category    Magento
 * @package     Magento_Gdata
 */
namespace Magento\Gdata\Gshopping\Extension;

class Tax extends \Zend_Gdata_App_Extension_Element
{
    /**
     * @var string The XML namespace prefix
     */
    protected $_rootNamespace = 'scp';

    /**
     * Key-value pair of tax information
     *
     * @var array
     */
    protected $_taxInfo;

    /**
     * Creates instance of class
     *
     * @param array $taxInfo as described in product requirements
     * @see http://code.google.com/intl/ru/apis/shopping/content/getting-started/requirements-products.html#tax
     */
    public function __construct(array $taxInfo = array())
    {
        $this->registerAllNamespaces(\Magento\Gdata\Gshopping\Content::$namespaces);
        parent::__construct('tax', $this->_rootNamespace, $this->lookupNamespace($this->_rootNamespace));
        $this->_taxInfo = $taxInfo;
        foreach ($taxInfo as $key => $value) {
            $this->_extensionElements[] = new \Zend_Gdata_App_Extension_Element(
                $key,
                $this->_rootNamespace,
                $this->_rootNamespaceURI,
                $value
            );
        }
    }

    /**
     * Magic getter to add access to _taxInfo data
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_taxInfo[$name]) ? $this->_taxInfo[$name] : parent::__get($name);
    }

    /**
     * Given a child \DOMNode, tries to determine how to map the data into
     * object instance members.  If no mapping is defined, Extension_Element
     * objects are created and stored in an array.
     *
     * @param \DOMNode $child The \DOMNode needed to be handled
     */
    protected function takeChildFromDOM($child)
    {
        if ($child->nodeType == XML_ELEMENT_NODE) {
            $name = ('attribute' == $child->localName) ? $child->getAttribute('name') : $child->localName;
            $this->_taxInfo[$name] = $child->textContent;
        }
        parent::takeChildFromDOM($child);
    }
}
