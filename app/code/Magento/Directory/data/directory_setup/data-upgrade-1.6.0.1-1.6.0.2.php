<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * @var $installer \Magento\Directory\Model\Resource\Setup
 */
$installer = $this;

$data = [
    ['BR', 'AC', 'Acre'],
    ['BR', 'AL', 'Alagoas'],
    ['BR', 'AP', 'Amapá'],
    ['BR', 'AM', 'Amazonas'],
    ['BR', 'BA', 'Bahia'],
    ['BR', 'CE', 'Ceará'],
    ['BR', 'ES', 'Espírito Santo'],
    ['BR', 'GO', 'Goiás'],
    ['BR', 'MA', 'Maranhão'],
    ['BR', 'MT', 'Mato Grosso'],
    ['BR', 'MS', 'Mato Grosso do Sul'],
    ['BR', 'MG', 'Minas Gerais'],
    ['BR', 'PA', 'Pará'],
    ['BR', 'PB', 'Paraíba'],
    ['BR', 'PR', 'Paraná'],
    ['BR', 'PE', 'Pernambuco'],
    ['BR', 'PI', 'Piauí'],
    ['BR', 'RJ', 'Rio de Janeiro'],
    ['BR', 'RN', 'Rio Grande do Norte'],
    ['BR', 'RS', 'Rio Grande do Sul'],
    ['BR', 'RO', 'Rondônia'],
    ['BR', 'RR', 'Roraima'],
    ['BR', 'SC', 'Santa Catarina'],
    ['BR', 'SP', 'São Paulo'],
    ['BR', 'SE', 'Sergipe'],
    ['BR', 'TO', 'Tocantins'],
    ['BR', 'DF', 'Distrito Federal'],
];

foreach ($data as $row) {
    $bind = ['country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]];
    $installer->getConnection()->insert($installer->getTable('directory_country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory_country_region'));

    $bind = ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]];
    $installer->getConnection()->insert($installer->getTable('directory_country_region_name'), $bind);
}
