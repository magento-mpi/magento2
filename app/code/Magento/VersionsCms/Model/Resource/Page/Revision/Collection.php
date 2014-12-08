<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Resource\Page\Revision;

/**
 * Cms page revision collection
 */
class Collection extends \Magento\VersionsCms\Model\Resource\Page\Collection\AbstractCollection
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\VersionsCms\Model\Page\Revision', 'Magento\VersionsCms\Model\Resource\Page\Revision');
    }

    /**
     * Joining version data to each revision.
     * Columns which should be joined determined by parameter $cols.
     *
     * @param mixed $cols
     * @return $this
     */
    public function joinVersions($cols = '')
    {
        if (!$this->getFlag('versions_joined')) {
            $this->_map['fields']['version_id'] = 'ver_table.version_id';
            $this->_map['fields']['versionuser_user_id'] = 'ver_table.user_id';

            $columns = [
                'version_id' => 'ver_table.version_id',
                'access_level',
                'version_user_id' => 'ver_table.user_id',
                'label',
                'version_number',
            ];

            if (is_array($cols)) {
                $columns = array_merge($columns, $cols);
            } elseif ($cols) {
                $columns[] = $cols;
            }

            $this->getSelect()->joinInner(
                ['ver_table' => $this->getTable('magento_versionscms_page_version')],
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
     * @return $this
     */
    public function addVersionFilter($version)
    {
        if ($version instanceof \Magento\VersionsCms\Model\Page\Version) {
            $version = $version->getId();
        }

        if (is_array($version)) {
            $version = ['in' => $version];
        }

        $this->addFieldTofilter('version_id', $version);

        return $this;
    }

    /**
     * Add order by revision number in specified direction.
     *
     * @param string $dir
     * @return $this
     */
    public function addNumberSort($dir = \Magento\Framework\DB\Select::SQL_DESC)
    {
        $this->setOrder('revision_number', $dir);
        return $this;
    }
}
