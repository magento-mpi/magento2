<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @var \Magento\Directory\Model\Resource\Setup $installer
 */
$installer = $this;

$data = array(
    array('BR', 'AC', 'Acre'),
    array('BR', 'AL', 'Alagoas'),
    array('BR', 'AP', 'Amapá'),
    array('BR', 'AM', 'Amazonas'),
    array('BR', 'BA', 'Bahia'),
    array('BR', 'CE', 'Ceará'),
    array('BR', 'ES', 'Espírito Santo'),
    array('BR', 'GO', 'Goiás'),
    array('BR', 'MA', 'Maranhão'),
    array('BR', 'MT', 'Mato Grosso'),
    array('BR', 'MS', 'Mato Grosso do Sul'),
    array('BR', 'MG', 'Minas Gerais'),
    array('BR', 'PA', 'Pará'),
    array('BR', 'PB', 'Paraíba'),
    array('BR', 'PR', 'Paraná'),
    array('BR', 'PE', 'Pernambuco'),
    array('BR', 'PI', 'Piauí'),
    array('BR', 'RJ', 'Rio de Janeiro'),
    array('BR', 'RN', 'Rio Grande do Norte'),
    array('BR', 'RS', 'Rio Grande do Sul'),
    array('BR', 'RO', 'Rondônia'),
    array('BR', 'RR', 'Roraima'),
    array('BR', 'SC', 'Santa Catarina'),
    array('BR', 'SP', 'São Paulo'),
    array('BR', 'SE', 'Sergipe'),
    array('BR', 'TO', 'Tocantins'),
    array('BR', 'DF', 'Distrito Federal')
);

foreach ($data as $row) {
    $bind = array('country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]);
    $installer->getConnection()->insert($installer->getTable('directory_country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory_country_region'));

    $bind = array('locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]);
    $installer->getConnection()->insert($installer->getTable('directory_country_region_name'), $bind);
}
