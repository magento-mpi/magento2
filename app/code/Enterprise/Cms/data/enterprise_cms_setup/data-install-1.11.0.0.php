<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/*
 * Creating initial versions and revisions
 */
$attributes = array(
    'root_template',
    'meta_keywords',
    'meta_description',
    'content',
    'layout_update_xml',
    'custom_theme',
    'custom_theme_from',
    'custom_theme_to'
);
$adapter = $installer->getConnection();
$select  = $adapter->select();

$select->from(array('p' => $installer->getTable('cms_page')))
    ->joinLeft(array('v' => $installer->getTable('enterprise_cms_page_version')), 'v.page_id = p.page_id', array())
    ->where('v.page_id IS NULL');

$resource = $adapter->query($select);

while ($page = $resource->fetch(Zend_Db::FETCH_ASSOC)) {
    $adapter->insert($installer->getTable('enterprise_cms_increment'), array(
        'increment_type'    => 0,
        'increment_node'    => $page['page_id'],
        'increment_level'   => 0,
        'last_id'           => 1
    ));

    $adapter->insert($installer->getTable('enterprise_cms_page_version'), array(
        'version_number'  => 1,
        'page_id'         => $page['page_id'],
        'access_level'    => Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC,
        'user_id'         => new Zend_Db_Expr('NULL'),
        'revisions_count' => 1,
        'label'           => $page['title'],
        'created_at'      => Mage::getSingleton('Magento_Core_Model_Date')->gmtDate()
    ));

    $versionId = $adapter->lastInsertId($installer->getTable('enterprise_cms_page_version'), 'version_id');

    $adapter->insert($installer->getTable('enterprise_cms_increment'), array(
        'increment_type'    => 0,
        'increment_node'    => $versionId,
        'increment_level'   => 1,
        'last_id'           => 1
    ));

    /**
     * Prepare revision data
     */
    $_data = array();

    foreach ($attributes as $attr) {
        $_data[$attr] = $page[$attr];
    }

    $_data['created_at']      = Mage::getSingleton('Magento_Core_Model_Date')->gmtDate();
    $_data['user_id']         = new Zend_Db_Expr('NULL');
    $_data['revision_number'] = 1;
    $_data['version_id']      = $versionId;
    $_data['page_id']         = $page['page_id'];

    $adapter->insert($installer->getTable('enterprise_cms_page_revision'), $_data);
}

$adapter->query($select);

$installer->endSetup();
