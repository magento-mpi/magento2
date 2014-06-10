<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Model\Package;

/**
 * Extractor for Community Product
 */
class CommunityExtractor extends AbstractExtractor
{
    /**
     * Name of Package
     *
     * @var string
     */
    protected $_name = "Magento/Community";

    /**
     * Version of Package
     *
     * @var string
     */
    protected $_version = "0.1.0";

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "project";
    }

    /**
     * Parser not required
     * @param string $filename
     * @return null
     */
    public function getParser($filename)
    {
        return null;
    }


    /**
     * Create Package directly for Magento Community
     *
     * @param array $collection
     * @param int &$count
     * @return array
     */
    public function extract(array $collection = array(), &$count = 0)
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
