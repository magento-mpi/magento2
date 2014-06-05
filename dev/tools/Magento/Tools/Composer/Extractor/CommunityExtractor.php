<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Model\Package;

class CommunityExtractor extends AbstractExtractor
{
    protected $_name = "Magento/Community";
    protected $_version = "0.1.0";

    public function getSubPath()
    {
        return '';
    }

    public function getType()
    {
        return "project";
    }

    public function getParser($filename)
    {
        return null;
    }

    public function extract($collection = array(), &$count = 0)
    {
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);

        $product = new Package($this->_name, $this->_version, BP, $this->getType());
        $product->addDependencies($collection);
        $this->addToCollection(array($product));

        return $this->_collection;
    }

}