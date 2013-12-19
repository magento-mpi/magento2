<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Customer\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();

//get all duplicated emails
$select  = $connection->select()
    ->from($installer->getTable('customer_entity'), array('email', 'website_id', 'cnt' => 'COUNT(*)'))
    ->group('email')
    ->group('website_id')
    ->having('cnt > 1');
$emails = $connection->fetchAll($select);

foreach ($emails as $data) {
    $email = $data['email'];
    $websiteId = $data['website_id'];

    $select = $connection->select()
        ->from($installer->getTable('customer_entity'), array('entity_id'))
        ->where('email = ?', $email)
        ->where('website_id = ?', $websiteId);
    $activeId = $connection->fetchOne($select);

    //receive all other duplicated customer ids
    $select = $connection->select()
        ->from($installer->getTable('customer_entity'), array('entity_id', 'email'))
        ->where('email = ?', $email)
        ->where('website_id = ?', $websiteId)
        ->where('entity_id <> ?', $activeId);

    $result = $connection->fetchAll($select);

    //change email to unique value
    foreach ($result as $row) {
        $changedEmail = $connection->getConcatSql(array('"(duplicate"', $row['entity_id'], '")"', '"' . $row['email'] . '"'));
        $connection->update(
            $installer->getTable('customer_entity'),
            array('email' => $changedEmail),
            array('entity_id =?' => $row['entity_id'])
        );
    }
}

/**
 * Add unique index for customer_entity table
 */
$connection->addIndex(
    $installer->getTable('customer_entity'),
    $installer->getIdxName('customer_entity', array('email', 'website_id')),
    array('email', 'website_id'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);