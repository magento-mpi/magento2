<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Model\Project;

/**
 * Extractor for Community Product
 */
class CommunityExtractor extends ExtractorAbstract
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
    public function getPath()
    {
        return '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "project";
    }

    /**
     * Create Package directly for Magento Community
     *
     * @param array $collection
     * @param int &$count
     * @return array Collection Array
     */
    public function extract(array $collection = array(), &$count = 0)
    {
        $this->_counter = &$count;
        $this->_counter = 0;
        $this->addToCollection($collection);
        $excludes = array(
            realpath($this->_rootDir) . "/app/design/adminhtml/Magento",
            realpath($this->_rootDir) . "/app/design/frontend/Magento",
            realpath($this->_rootDir) . "/app/code/Magento",
            realpath($this->_rootDir) . "/dev/tools/Magento/Tools/Composer/_packages",
            realpath($this->_rootDir) . '/lib',
            realpath($this->_rootDir) . '/.git',
            realpath($this->_rootDir) . '/.idea',
            realpath($this->_rootDir) . '/app/i18n'
        );
        $product = new Project($this->_name, $this->_version, $this->getPath(), $this->getType(), $excludes);
        $product->addDependencies($collection);
        $this->addToCollection(array($product));

        return $this->_collection;
    }
}