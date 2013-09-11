<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms page revision collection
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Model\Resource\Page\Revision;

class Collection
    extends \Magento\VersionsCms\Model\Resource\Page\Collection\AbstractCollection
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('\Magento\VersionsCms\Model\Page\Revision', '\Magento\VersionsCms\Model\Resource\Page\Revision');
    }

    /**
     * Joining version data to each revision.
     * Columns which should be joined determined by parameter $cols.
     *
     * @param mixed $cols
     * @return \Magento\VersionsCms\Model\Resource\Page\Revision\Collection
     */
    public function joinVersions($cols = '')
    {
        if (!$this->getFlag('versions_joined')) {
            $this->_map['fields']['version_id'] = 'ver_table.version_id';
            $this->_map['fields']['versionuser_user_id'] = 'ver_table.user_id';

            $columns = array(
                'version_id' => 'ver_table.version_id',
                'access_level',
                'version_user_id' => 'ver_table.user_id',
                'label',
                'version_number'
            );

            if (is_array($cols)) {
                $columns = array_merge($columns, $cols);
            } else if ($cols) {
                $columns[] = $cols;
            }

            $this->getSelect()->joinInner(
                array('ver_table' => $this->getTable('magento_versionscms_page_version')),
                'ver_table.version_id = main_table.version_id',
                $columns
            );

            $this->setFlag('versions_joined');
        }
        return $this;
    }

    /**
     * Add filtering by version id.
     * Parameter $version can be int or object.
     *
     * @param int|\Magento\VersionsCms\Model\Page\Version $version
     * @return \Magento\VersionsCms\Model\Resource\Page\Revision\Collection
     */
    public function addVersionFilter($version)
    {
        if ($version instanceof \Magento\VersionsCms\Model\Page\Version) {
            $version = $version->getId();
        }

        if (is_array($version)) {
            $version = array('in' => $version);
        }

        $this->addFieldTofilter('version_id', $version);

        return $this;
    }

    /**
     * Add order by revision number in specified direction.
     *
     * @param string $dir
     * @return \Magento\VersionsCms\Model\Resource\Page\Revision\Collection
     */
    public function addNumberSort($dir = \Magento\DB\Select::SQL_DESC)
    {
        $this->setOrder('revision_number', $dir);
        return $this;
    }
}
