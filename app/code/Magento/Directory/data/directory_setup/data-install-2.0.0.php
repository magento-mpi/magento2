<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Directory\Model\Resource\Setup */
$installer = $this;

/**
 * Fill table directory/country
 */
$data = [
    ['AD', 'AD', 'AND'],
    ['AE', 'AE', 'ARE'],
    ['AF', 'AF', 'AFG'],
    ['AG', 'AG', 'ATG'],
    ['AI', 'AI', 'AIA'],
    ['AL', 'AL', 'ALB'],
    ['AM', 'AM', 'ARM'],
    ['AN', 'AN', 'ANT'],
    ['AO', 'AO', 'AGO'],
    ['AQ', 'AQ', 'ATA'],
    ['AR', 'AR', 'ARG'],
    ['AS', 'AS', 'ASM'],
    ['AT', 'AT', 'AUT'],
    ['AU', 'AU', 'AUS'],
    ['AW', 'AW', 'ABW'],
    ['AX', 'AX', 'ALA'],
    ['AZ', 'AZ', 'AZE'],
    ['BA', 'BA', 'BIH'],
    ['BB', 'BB', 'BRB'],
    ['BD', 'BD', 'BGD'],
    ['BE', 'BE', 'BEL'],
    ['BF', 'BF', 'BFA'],
    ['BG', 'BG', 'BGR'],
    ['BH', 'BH', 'BHR'],
    ['BI', 'BI', 'BDI'],
    ['BJ', 'BJ', 'BEN'],
    ['BL', 'BL', 'BLM'],
    ['BM', 'BM', 'BMU'],
    ['BN', 'BN', 'BRN'],
    ['BO', 'BO', 'BOL'],
    ['BR', 'BR', 'BRA'],
    ['BS', 'BS', 'BHS'],
    ['BT', 'BT', 'BTN'],
    ['BV', 'BV', 'BVT'],
    ['BW', 'BW', 'BWA'],
    ['BY', 'BY', 'BLR'],
    ['BZ', 'BZ', 'BLZ'],
    ['CA', 'CA', 'CAN'],
    ['CC', 'CC', 'CCK'],
    ['CD', 'CD', 'COD'],
    ['CF', 'CF', 'CAF'],
    ['CG', 'CG', 'COG'],
    ['CH', 'CH', 'CHE'],
    ['CI', 'CI', 'CIV'],
    ['CK', 'CK', 'COK'],
    ['CL', 'CL', 'CHL'],
    ['CM', 'CM', 'CMR'],
    ['CN', 'CN', 'CHN'],
    ['CO', 'CO', 'COL'],
    ['CR', 'CR', 'CRI'],
    ['CU', 'CU', 'CUB'],
    ['CV', 'CV', 'CPV'],
    ['CX', 'CX', 'CXR'],
    ['CY', 'CY', 'CYP'],
    ['CZ', 'CZ', 'CZE'],
    ['DE', 'DE', 'DEU'],
    ['DJ', 'DJ', 'DJI'],
    ['DK', 'DK', 'DNK'],
    ['DM', 'DM', 'DMA'],
    ['DO', 'DO', 'DOM'],
    ['DZ', 'DZ', 'DZA'],
    ['EC', 'EC', 'ECU'],
    ['EE', 'EE', 'EST'],
    ['EG', 'EG', 'EGY'],
    ['EH', 'EH', 'ESH'],
    ['ER', 'ER', 'ERI'],
    ['ES', 'ES', 'ESP'],
    ['ET', 'ET', 'ETH'],
    ['FI', 'FI', 'FIN'],
    ['FJ', 'FJ', 'FJI'],
    ['FK', 'FK', 'FLK'],
    ['FM', 'FM', 'FSM'],
    ['FO', 'FO', 'FRO'],
    ['FR', 'FR', 'FRA'],
    ['GA', 'GA', 'GAB'],
    ['GB', 'GB', 'GBR'],
    ['GD', 'GD', 'GRD'],
    ['GE', 'GE', 'GEO'],
    ['GF', 'GF', 'GUF'],
    ['GG', 'GG', 'GGY'],
    ['GH', 'GH', 'GHA'],
    ['GI', 'GI', 'GIB'],
    ['GL', 'GL', 'GRL'],
    ['GM', 'GM', 'GMB'],
    ['GN', 'GN', 'GIN'],
    ['GP', 'GP', 'GLP'],
    ['GQ', 'GQ', 'GNQ'],
    ['GR', 'GR', 'GRC'],
    ['GS', 'GS', 'SGS'],
    ['GT', 'GT', 'GTM'],
    ['GU', 'GU', 'GUM'],
    ['GW', 'GW', 'GNB'],
    ['GY', 'GY', 'GUY'],
    ['HK', 'HK', 'HKG'],
    ['HM', 'HM', 'HMD'],
    ['HN', 'HN', 'HND'],
    ['HR', 'HR', 'HRV'],
    ['HT', 'HT', 'HTI'],
    ['HU', 'HU', 'HUN'],
    ['ID', 'ID', 'IDN'],
    ['IE', 'IE', 'IRL'],
    ['IL', 'IL', 'ISR'],
    ['IM', 'IM', 'IMN'],
    ['IN', 'IN', 'IND'],
    ['IO', 'IO', 'IOT'],
    ['IQ', 'IQ', 'IRQ'],
    ['IR', 'IR', 'IRN'],
    ['IS', 'IS', 'ISL'],
    ['IT', 'IT', 'ITA'],
    ['JE', 'JE', 'JEY'],
    ['JM', 'JM', 'JAM'],
    ['JO', 'JO', 'JOR'],
    ['JP', 'JP', 'JPN'],
    ['KE', 'KE', 'KEN'],
    ['KG', 'KG', 'KGZ'],
    ['KH', 'KH', 'KHM'],
    ['KI', 'KI', 'KIR'],
    ['KM', 'KM', 'COM'],
    ['KN', 'KN', 'KNA'],
    ['KP', 'KP', 'PRK'],
    ['KR', 'KR', 'KOR'],
    ['KW', 'KW', 'KWT'],
    ['KY', 'KY', 'CYM'],
    ['KZ', 'KZ', 'KAZ'],
    ['LA', 'LA', 'LAO'],
    ['LB', 'LB', 'LBN'],
    ['LC', 'LC', 'LCA'],
    ['LI', 'LI', 'LIE'],
    ['LK', 'LK', 'LKA'],
    ['LR', 'LR', 'LBR'],
    ['LS', 'LS', 'LSO'],
    ['LT', 'LT', 'LTU'],
    ['LU', 'LU', 'LUX'],
    ['LV', 'LV', 'LVA'],
    ['LY', 'LY', 'LBY'],
    ['MA', 'MA', 'MAR'],
    ['MC', 'MC', 'MCO'],
    ['MD', 'MD', 'MDA'],
    ['ME', 'ME', 'MNE'],
    ['MF', 'MF', 'MAF'],
    ['MG', 'MG', 'MDG'],
    ['MH', 'MH', 'MHL'],
    ['MK', 'MK', 'MKD'],
    ['ML', 'ML', 'MLI'],
    ['MM', 'MM', 'MMR'],
    ['MN', 'MN', 'MNG'],
    ['MO', 'MO', 'MAC'],
    ['MP', 'MP', 'MNP'],
    ['MQ', 'MQ', 'MTQ'],
    ['MR', 'MR', 'MRT'],
    ['MS', 'MS', 'MSR'],
    ['MT', 'MT', 'MLT'],
    ['MU', 'MU', 'MUS'],
    ['MV', 'MV', 'MDV'],
    ['MW', 'MW', 'MWI'],
    ['MX', 'MX', 'MEX'],
    ['MY', 'MY', 'MYS'],
    ['MZ', 'MZ', 'MOZ'],
    ['NA', 'NA', 'NAM'],
    ['NC', 'NC', 'NCL'],
    ['NE', 'NE', 'NER'],
    ['NF', 'NF', 'NFK'],
    ['NG', 'NG', 'NGA'],
    ['NI', 'NI', 'NIC'],
    ['NL', 'NL', 'NLD'],
    ['NO', 'NO', 'NOR'],
    ['NP', 'NP', 'NPL'],
    ['NR', 'NR', 'NRU'],
    ['NU', 'NU', 'NIU'],
    ['NZ', 'NZ', 'NZL'],
    ['OM', 'OM', 'OMN'],
    ['PA', 'PA', 'PAN'],
    ['PE', 'PE', 'PER'],
    ['PF', 'PF', 'PYF'],
    ['PG', 'PG', 'PNG'],
    ['PH', 'PH', 'PHL'],
    ['PK', 'PK', 'PAK'],
    ['PL', 'PL', 'POL'],
    ['PM', 'PM', 'SPM'],
    ['PN', 'PN', 'PCN'],
    ['PS', 'PS', 'PSE'],
    ['PT', 'PT', 'PRT'],
    ['PW', 'PW', 'PLW'],
    ['PY', 'PY', 'PRY'],
    ['QA', 'QA', 'QAT'],
    ['RE', 'RE', 'REU'],
    ['RO', 'RO', 'ROU'],
    ['RS', 'RS', 'SRB'],
    ['RU', 'RU', 'RUS'],
    ['RW', 'RW', 'RWA'],
    ['SA', 'SA', 'SAU'],
    ['SB', 'SB', 'SLB'],
    ['SC', 'SC', 'SYC'],
    ['SD', 'SD', 'SDN'],
    ['SE', 'SE', 'SWE'],
    ['SG', 'SG', 'SGP'],
    ['SH', 'SH', 'SHN'],
    ['SI', 'SI', 'SVN'],
    ['SJ', 'SJ', 'SJM'],
    ['SK', 'SK', 'SVK'],
    ['SL', 'SL', 'SLE'],
    ['SM', 'SM', 'SMR'],
    ['SN', 'SN', 'SEN'],
    ['SO', 'SO', 'SOM'],
    ['SR', 'SR', 'SUR'],
    ['ST', 'ST', 'STP'],
    ['SV', 'SV', 'SLV'],
    ['SY', 'SY', 'SYR'],
    ['SZ', 'SZ', 'SWZ'],
    ['TC', 'TC', 'TCA'],
    ['TD', 'TD', 'TCD'],
    ['TF', 'TF', 'ATF'],
    ['TG', 'TG', 'TGO'],
    ['TH', 'TH', 'THA'],
    ['TJ', 'TJ', 'TJK'],
    ['TK', 'TK', 'TKL'],
    ['TL', 'TL', 'TLS'],
    ['TM', 'TM', 'TKM'],
    ['TN', 'TN', 'TUN'],
    ['TO', 'TO', 'TON'],
    ['TR', 'TR', 'TUR'],
    ['TT', 'TT', 'TTO'],
    ['TV', 'TV', 'TUV'],
    ['TW', 'TW', 'TWN'],
    ['TZ', 'TZ', 'TZA'],
    ['UA', 'UA', 'UKR'],
    ['UG', 'UG', 'UGA'],
    ['UM', 'UM', 'UMI'],
    ['US', 'US', 'USA'],
    ['UY', 'UY', 'URY'],
    ['UZ', 'UZ', 'UZB'],
    ['VA', 'VA', 'VAT'],
    ['VC', 'VC', 'VCT'],
    ['VE', 'VE', 'VEN'],
    ['VG', 'VG', 'VGB'],
    ['VI', 'VI', 'VIR'],
    ['VN', 'VN', 'VNM'],
    ['VU', 'VU', 'VUT'],
    ['WF', 'WF', 'WLF'],
    ['WS', 'WS', 'WSM'],
    ['YE', 'YE', 'YEM'],
    ['YT', 'YT', 'MYT'],
    ['ZA', 'ZA', 'ZAF'],
    ['ZM', 'ZM', 'ZMB'],
    ['ZW', 'ZW', 'ZWE'],
];

$columns = ['country_id', 'iso2_code', 'iso3_code'];
$installer->getConnection()->insertArray($installer->getTable('directory_country'), $columns, $data);

/**
 * Fill table directory/country_region
 * Fill table directory/country_region_name for en_US locale
 */
$data = array(
    array('US', 'AL', 'Alabama'),
    array('US', 'AK', 'Alaska'),
    array('US', 'AS', 'American Samoa'),
    array('US', 'AZ', 'Arizona'),
    array('US', 'AR', 'Arkansas'),
    array('US', 'AE', 'Armed Forces Africa'),
    array('US', 'AA', 'Armed Forces Americas'),
    array('US', 'AE', 'Armed Forces Canada'),
    array('US', 'AE', 'Armed Forces Europe'),
    array('US', 'AE', 'Armed Forces Middle East'),
    array('US', 'AP', 'Armed Forces Pacific'),
    array('US', 'CA', 'California'),
    array('US', 'CO', 'Colorado'),
    array('US', 'CT', 'Connecticut'),
    array('US', 'DE', 'Delaware'),
    array('US', 'DC', 'District of Columbia'),
    array('US', 'FM', 'Federated States Of Micronesia'),
    array('US', 'FL', 'Florida'),
    array('US', 'GA', 'Georgia'),
    array('US', 'GU', 'Guam'),
    array('US', 'HI', 'Hawaii'),
    array('US', 'ID', 'Idaho'),
    array('US', 'IL', 'Illinois'),
    array('US', 'IN', 'Indiana'),
    array('US', 'IA', 'Iowa'),
    array('US', 'KS', 'Kansas'),
    array('US', 'KY', 'Kentucky'),
    array('US', 'LA', 'Louisiana'),
    array('US', 'ME', 'Maine'),
    array('US', 'MH', 'Marshall Islands'),
    array('US', 'MD', 'Maryland'),
    array('US', 'MA', 'Massachusetts'),
    array('US', 'MI', 'Michigan'),
    array('US', 'MN', 'Minnesota'),
    array('US', 'MS', 'Mississippi'),
    array('US', 'MO', 'Missouri'),
    array('US', 'MT', 'Montana'),
    array('US', 'NE', 'Nebraska'),
    array('US', 'NV', 'Nevada'),
    array('US', 'NH', 'New Hampshire'),
    array('US', 'NJ', 'New Jersey'),
    array('US', 'NM', 'New Mexico'),
    array('US', 'NY', 'New York'),
    array('US', 'NC', 'North Carolina'),
    array('US', 'ND', 'North Dakota'),
    array('US', 'MP', 'Northern Mariana Islands'),
    array('US', 'OH', 'Ohio'),
    array('US', 'OK', 'Oklahoma'),
    array('US', 'OR', 'Oregon'),
    array('US', 'PW', 'Palau'),
    array('US', 'PA', 'Pennsylvania'),
    array('US', 'PR', 'Puerto Rico'),
    array('US', 'RI', 'Rhode Island'),
    array('US', 'SC', 'South Carolina'),
    array('US', 'SD', 'South Dakota'),
    array('US', 'TN', 'Tennessee'),
    array('US', 'TX', 'Texas'),
    array('US', 'UT', 'Utah'),
    array('US', 'VT', 'Vermont'),
    array('US', 'VI', 'Virgin Islands'),
    array('US', 'VA', 'Virginia'),
    array('US', 'WA', 'Washington'),
    array('US', 'WV', 'West Virginia'),
    array('US', 'WI', 'Wisconsin'),
    array('US', 'WY', 'Wyoming'),
    array('CA', 'AB', 'Alberta'),
    array('CA', 'BC', 'British Columbia'),
    array('CA', 'MB', 'Manitoba'),
    array('CA', 'NL', 'Newfoundland and Labrador'),
    array('CA', 'NB', 'New Brunswick'),
    array('CA', 'NS', 'Nova Scotia'),
    array('CA', 'NT', 'Northwest Territories'),
    array('CA', 'NU', 'Nunavut'),
    array('CA', 'ON', 'Ontario'),
    array('CA', 'PE', 'Prince Edward Island'),
    array('CA', 'QC', 'Quebec'),
    array('CA', 'SK', 'Saskatchewan'),
    array('CA', 'YT', 'Yukon Territory'),
    array('DE', 'NDS', 'Niedersachsen'),
    array('DE', 'BAW', 'Baden-Württemberg'),
    array('DE', 'BAY', 'Bayern'),
    array('DE', 'BER', 'Berlin'),
    array('DE', 'BRG', 'Brandenburg'),
    array('DE', 'BRE', 'Bremen'),
    array('DE', 'HAM', 'Hamburg'),
    array('DE', 'HES', 'Hessen'),
    array('DE', 'MEC', 'Mecklenburg-Vorpommern'),
    array('DE', 'NRW', 'Nordrhein-Westfalen'),
    array('DE', 'RHE', 'Rheinland-Pfalz'),
    array('DE', 'SAR', 'Saarland'),
    array('DE', 'SAS', 'Sachsen'),
    array('DE', 'SAC', 'Sachsen-Anhalt'),
    array('DE', 'SCN', 'Schleswig-Holstein'),
    array('DE', 'THE', 'Thüringen'),
    array('AT', 'WI', 'Wien'),
    array('AT', 'NO', 'Niederösterreich'),
    array('AT', 'OO', 'Oberösterreich'),
    array('AT', 'SB', 'Salzburg'),
    array('AT', 'KN', 'Kärnten'),
    array('AT', 'ST', 'Steiermark'),
    array('AT', 'TI', 'Tirol'),
    array('AT', 'BL', 'Burgenland'),
    array('AT', 'VB', 'Vorarlberg'),
    array('CH', 'AG', 'Aargau'),
    array('CH', 'AI', 'Appenzell Innerrhoden'),
    array('CH', 'AR', 'Appenzell Ausserrhoden'),
    array('CH', 'BE', 'Bern'),
    array('CH', 'BL', 'Basel-Landschaft'),
    array('CH', 'BS', 'Basel-Stadt'),
    array('CH', 'FR', 'Freiburg'),
    array('CH', 'GE', 'Genf'),
    array('CH', 'GL', 'Glarus'),
    array('CH', 'GR', 'Graubünden'),
    array('CH', 'JU', 'Jura'),
    array('CH', 'LU', 'Luzern'),
    array('CH', 'NE', 'Neuenburg'),
    array('CH', 'NW', 'Nidwalden'),
    array('CH', 'OW', 'Obwalden'),
    array('CH', 'SG', 'St. Gallen'),
    array('CH', 'SH', 'Schaffhausen'),
    array('CH', 'SO', 'Solothurn'),
    array('CH', 'SZ', 'Schwyz'),
    array('CH', 'TG', 'Thurgau'),
    array('CH', 'TI', 'Tessin'),
    array('CH', 'UR', 'Uri'),
    array('CH', 'VD', 'Waadt'),
    array('CH', 'VS', 'Wallis'),
    array('CH', 'ZG', 'Zug'),
    array('CH', 'ZH', 'Zürich'),
    array('ES', 'A Coruсa', 'A Coruña'),
    array('ES', 'Alava', 'Alava'),
    array('ES', 'Albacete', 'Albacete'),
    array('ES', 'Alicante', 'Alicante'),
    array('ES', 'Almeria', 'Almeria'),
    array('ES', 'Asturias', 'Asturias'),
    array('ES', 'Avila', 'Avila'),
    array('ES', 'Badajoz', 'Badajoz'),
    array('ES', 'Baleares', 'Baleares'),
    array('ES', 'Barcelona', 'Barcelona'),
    array('ES', 'Burgos', 'Burgos'),
    array('ES', 'Caceres', 'Caceres'),
    array('ES', 'Cadiz', 'Cadiz'),
    array('ES', 'Cantabria', 'Cantabria'),
    array('ES', 'Castellon', 'Castellon'),
    array('ES', 'Ceuta', 'Ceuta'),
    array('ES', 'Ciudad Real', 'Ciudad Real'),
    array('ES', 'Cordoba', 'Cordoba'),
    array('ES', 'Cuenca', 'Cuenca'),
    array('ES', 'Girona', 'Girona'),
    array('ES', 'Granada', 'Granada'),
    array('ES', 'Guadalajara', 'Guadalajara'),
    array('ES', 'Guipuzcoa', 'Guipuzcoa'),
    array('ES', 'Huelva', 'Huelva'),
    array('ES', 'Huesca', 'Huesca'),
    array('ES', 'Jaen', 'Jaen'),
    array('ES', 'La Rioja', 'La Rioja'),
    array('ES', 'Las Palmas', 'Las Palmas'),
    array('ES', 'Leon', 'Leon'),
    array('ES', 'Lleida', 'Lleida'),
    array('ES', 'Lugo', 'Lugo'),
    array('ES', 'Madrid', 'Madrid'),
    array('ES', 'Malaga', 'Malaga'),
    array('ES', 'Melilla', 'Melilla'),
    array('ES', 'Murcia', 'Murcia'),
    array('ES', 'Navarra', 'Navarra'),
    array('ES', 'Ourense', 'Ourense'),
    array('ES', 'Palencia', 'Palencia'),
    array('ES', 'Pontevedra', 'Pontevedra'),
    array('ES', 'Salamanca', 'Salamanca'),
    array('ES', 'Santa Cruz de Tenerife', 'Santa Cruz de Tenerife'),
    array('ES', 'Segovia', 'Segovia'),
    array('ES', 'Sevilla', 'Sevilla'),
    array('ES', 'Soria', 'Soria'),
    array('ES', 'Tarragona', 'Tarragona'),
    array('ES', 'Teruel', 'Teruel'),
    array('ES', 'Toledo', 'Toledo'),
    array('ES', 'Valencia', 'Valencia'),
    array('ES', 'Valladolid', 'Valladolid'),
    array('ES', 'Vizcaya', 'Vizcaya'),
    array('ES', 'Zamora', 'Zamora'),
    array('ES', 'Zaragoza', 'Zaragoza'),
    array('FR', 1, 'Ain'),
    array('FR', 2, 'Aisne'),
    array('FR', 3, 'Allier'),
    array('FR', 4, 'Alpes-de-Haute-Provence'),
    array('FR', 5, 'Hautes-Alpes'),
    array('FR', 6, 'Alpes-Maritimes'),
    array('FR', 7, 'Ardèche'),
    array('FR', 8, 'Ardennes'),
    array('FR', 9, 'Ariège'),
    array('FR', 10, 'Aube'),
    array('FR', 11, 'Aude'),
    array('FR', 12, 'Aveyron'),
    array('FR', 13, 'Bouches-du-Rhône'),
    array('FR', 14, 'Calvados'),
    array('FR', 15, 'Cantal'),
    array('FR', 16, 'Charente'),
    array('FR', 17, 'Charente-Maritime'),
    array('FR', 18, 'Cher'),
    array('FR', 19, 'Corrèze'),
    array('FR', '2A', 'Corse-du-Sud'),
    array('FR', '2B', 'Haute-Corse'),
    array('FR', 21, 'Côte-d\'Or'),
    array('FR', 22, 'Côtes-d\'Armor'),
    array('FR', 23, 'Creuse'),
    array('FR', 24, 'Dordogne'),
    array('FR', 25, 'Doubs'),
    array('FR', 26, 'Drôme'),
    array('FR', 27, 'Eure'),
    array('FR', 28, 'Eure-et-Loir'),
    array('FR', 29, 'Finistère'),
    array('FR', 30, 'Gard'),
    array('FR', 31, 'Haute-Garonne'),
    array('FR', 32, 'Gers'),
    array('FR', 33, 'Gironde'),
    array('FR', 34, 'Hérault'),
    array('FR', 35, 'Ille-et-Vilaine'),
    array('FR', 36, 'Indre'),
    array('FR', 37, 'Indre-et-Loire'),
    array('FR', 38, 'Isère'),
    array('FR', 39, 'Jura'),
    array('FR', 40, 'Landes'),
    array('FR', 41, 'Loir-et-Cher'),
    array('FR', 42, 'Loire'),
    array('FR', 43, 'Haute-Loire'),
    array('FR', 44, 'Loire-Atlantique'),
    array('FR', 45, 'Loiret'),
    array('FR', 46, 'Lot'),
    array('FR', 47, 'Lot-et-Garonne'),
    array('FR', 48, 'Lozère'),
    array('FR', 49, 'Maine-et-Loire'),
    array('FR', 50, 'Manche'),
    array('FR', 51, 'Marne'),
    array('FR', 52, 'Haute-Marne'),
    array('FR', 53, 'Mayenne'),
    array('FR', 54, 'Meurthe-et-Moselle'),
    array('FR', 55, 'Meuse'),
    array('FR', 56, 'Morbihan'),
    array('FR', 57, 'Moselle'),
    array('FR', 58, 'Nièvre'),
    array('FR', 59, 'Nord'),
    array('FR', 60, 'Oise'),
    array('FR', 61, 'Orne'),
    array('FR', 62, 'Pas-de-Calais'),
    array('FR', 63, 'Puy-de-Dôme'),
    array('FR', 64, 'Pyrénées-Atlantiques'),
    array('FR', 65, 'Hautes-Pyrénées'),
    array('FR', 66, 'Pyrénées-Orientales'),
    array('FR', 67, 'Bas-Rhin'),
    array('FR', 68, 'Haut-Rhin'),
    array('FR', 69, 'Rhône'),
    array('FR', 70, 'Haute-Saône'),
    array('FR', 71, 'Saône-et-Loire'),
    array('FR', 72, 'Sarthe'),
    array('FR', 73, 'Savoie'),
    array('FR', 74, 'Haute-Savoie'),
    array('FR', 75, 'Paris'),
    array('FR', 76, 'Seine-Maritime'),
    array('FR', 77, 'Seine-et-Marne'),
    array('FR', 78, 'Yvelines'),
    array('FR', 79, 'Deux-Sèvres'),
    array('FR', 80, 'Somme'),
    array('FR', 81, 'Tarn'),
    array('FR', 82, 'Tarn-et-Garonne'),
    array('FR', 83, 'Var'),
    array('FR', 84, 'Vaucluse'),
    array('FR', 85, 'Vendée'),
    array('FR', 86, 'Vienne'),
    array('FR', 87, 'Haute-Vienne'),
    array('FR', 88, 'Vosges'),
    array('FR', 89, 'Yonne'),
    array('FR', 90, 'Territoire-de-Belfort'),
    array('FR', 91, 'Essonne'),
    array('FR', 92, 'Hauts-de-Seine'),
    array('FR', 93, 'Seine-Saint-Denis'),
    array('FR', 94, 'Val-de-Marne'),
    array('FR', 95, 'Val-d\'Oise'),
    array('RO', 'AB', 'Alba'),
    array('RO', 'AR', 'Arad'),
    array('RO', 'AG', 'Argeş'),
    array('RO', 'BC', 'Bacău'),
    array('RO', 'BH', 'Bihor'),
    array('RO', 'BN', 'Bistriţa-Năsăud'),
    array('RO', 'BT', 'Botoşani'),
    array('RO', 'BV', 'Braşov'),
    array('RO', 'BR', 'Brăila'),
    array('RO', 'B', 'Bucureşti'),
    array('RO', 'BZ', 'Buzău'),
    array('RO', 'CS', 'Caraş-Severin'),
    array('RO', 'CL', 'Călăraşi'),
    array('RO', 'CJ', 'Cluj'),
    array('RO', 'CT', 'Constanţa'),
    array('RO', 'CV', 'Covasna'),
    array('RO', 'DB', 'Dâmboviţa'),
    array('RO', 'DJ', 'Dolj'),
    array('RO', 'GL', 'Galaţi'),
    array('RO', 'GR', 'Giurgiu'),
    array('RO', 'GJ', 'Gorj'),
    array('RO', 'HR', 'Harghita'),
    array('RO', 'HD', 'Hunedoara'),
    array('RO', 'IL', 'Ialomiţa'),
    array('RO', 'IS', 'Iaşi'),
    array('RO', 'IF', 'Ilfov'),
    array('RO', 'MM', 'Maramureş'),
    array('RO', 'MH', 'Mehedinţi'),
    array('RO', 'MS', 'Mureş'),
    array('RO', 'NT', 'Neamţ'),
    array('RO', 'OT', 'Olt'),
    array('RO', 'PH', 'Prahova'),
    array('RO', 'SM', 'Satu-Mare'),
    array('RO', 'SJ', 'Sălaj'),
    array('RO', 'SB', 'Sibiu'),
    array('RO', 'SV', 'Suceava'),
    array('RO', 'TR', 'Teleorman'),
    array('RO', 'TM', 'Timiş'),
    array('RO', 'TL', 'Tulcea'),
    array('RO', 'VS', 'Vaslui'),
    array('RO', 'VL', 'Vâlcea'),
    array('RO', 'VN', 'Vrancea'),
    array('FI', 'Lappi', 'Lappi'),
    array('FI', 'Pohjois-Pohjanmaa', 'Pohjois-Pohjanmaa'),
    array('FI', 'Kainuu', 'Kainuu'),
    array('FI', 'Pohjois-Karjala', 'Pohjois-Karjala'),
    array('FI', 'Pohjois-Savo', 'Pohjois-Savo'),
    array('FI', 'Etelä-Savo', 'Etelä-Savo'),
    array('FI', 'Etelä-Pohjanmaa', 'Etelä-Pohjanmaa'),
    array('FI', 'Pohjanmaa', 'Pohjanmaa'),
    array('FI', 'Pirkanmaa', 'Pirkanmaa'),
    array('FI', 'Satakunta', 'Satakunta'),
    array('FI', 'Keski-Pohjanmaa', 'Keski-Pohjanmaa'),
    array('FI', 'Keski-Suomi', 'Keski-Suomi'),
    array('FI', 'Varsinais-Suomi', 'Varsinais-Suomi'),
    array('FI', 'Etelä-Karjala', 'Etelä-Karjala'),
    array('FI', 'Päijät-Häme', 'Päijät-Häme'),
    array('FI', 'Kanta-Häme', 'Kanta-Häme'),
    array('FI', 'Uusimaa', 'Uusimaa'),
    array('FI', 'Itä-Uusimaa', 'Itä-Uusimaa'),
    array('FI', 'Kymenlaakso', 'Kymenlaakso'),
    array('FI', 'Ahvenanmaa', 'Ahvenanmaa'),
    array('EE', 'EE-37', 'Harjumaa'),
    array('EE', 'EE-39', 'Hiiumaa'),
    array('EE', 'EE-44', 'Ida-Virumaa'),
    array('EE', 'EE-49', 'Jõgevamaa'),
    array('EE', 'EE-51', 'Järvamaa'),
    array('EE', 'EE-57', 'Läänemaa'),
    array('EE', 'EE-59', 'Lääne-Virumaa'),
    array('EE', 'EE-65', 'Põlvamaa'),
    array('EE', 'EE-67', 'Pärnumaa'),
    array('EE', 'EE-70', 'Raplamaa'),
    array('EE', 'EE-74', 'Saaremaa'),
    array('EE', 'EE-78', 'Tartumaa'),
    array('EE', 'EE-82', 'Valgamaa'),
    array('EE', 'EE-84', 'Viljandimaa'),
    array('EE', 'EE-86', 'Võrumaa'),
    array('LV', 'LV-DGV', 'Daugavpils'),
    array('LV', 'LV-JEL', 'Jelgava'),
    array('LV', 'Jēkabpils', 'Jēkabpils'),
    array('LV', 'LV-JUR', 'Jūrmala'),
    array('LV', 'LV-LPX', 'Liepāja'),
    array('LV', 'LV-LE', 'Liepājas novads'),
    array('LV', 'LV-REZ', 'Rēzekne'),
    array('LV', 'LV-RIX', 'Rīga'),
    array('LV', 'LV-RI', 'Rīgas novads'),
    array('LV', 'Valmiera', 'Valmiera'),
    array('LV', 'LV-VEN', 'Ventspils'),
    array('LV', 'Aglonas novads', 'Aglonas novads'),
    array('LV', 'LV-AI', 'Aizkraukles novads'),
    array('LV', 'Aizputes novads', 'Aizputes novads'),
    array('LV', 'Aknīstes novads', 'Aknīstes novads'),
    array('LV', 'Alojas novads', 'Alojas novads'),
    array('LV', 'Alsungas novads', 'Alsungas novads'),
    array('LV', 'LV-AL', 'Alūksnes novads'),
    array('LV', 'Amatas novads', 'Amatas novads'),
    array('LV', 'Apes novads', 'Apes novads'),
    array('LV', 'Auces novads', 'Auces novads'),
    array('LV', 'Babītes novads', 'Babītes novads'),
    array('LV', 'Baldones novads', 'Baldones novads'),
    array('LV', 'Baltinavas novads', 'Baltinavas novads'),
    array('LV', 'LV-BL', 'Balvu novads'),
    array('LV', 'LV-BU', 'Bauskas novads'),
    array('LV', 'Beverīnas novads', 'Beverīnas novads'),
    array('LV', 'Brocēnu novads', 'Brocēnu novads'),
    array('LV', 'Burtnieku novads', 'Burtnieku novads'),
    array('LV', 'Carnikavas novads', 'Carnikavas novads'),
    array('LV', 'Cesvaines novads', 'Cesvaines novads'),
    array('LV', 'Ciblas novads', 'Ciblas novads'),
    array('LV', 'LV-CE', 'Cēsu novads'),
    array('LV', 'Dagdas novads', 'Dagdas novads'),
    array('LV', 'LV-DA', 'Daugavpils novads'),
    array('LV', 'LV-DO', 'Dobeles novads'),
    array('LV', 'Dundagas novads', 'Dundagas novads'),
    array('LV', 'Durbes novads', 'Durbes novads'),
    array('LV', 'Engures novads', 'Engures novads'),
    array('LV', 'Garkalnes novads', 'Garkalnes novads'),
    array('LV', 'Grobiņas novads', 'Grobiņas novads'),
    array('LV', 'LV-GU', 'Gulbenes novads'),
    array('LV', 'Iecavas novads', 'Iecavas novads'),
    array('LV', 'Ikšķiles novads', 'Ikšķiles novads'),
    array('LV', 'Ilūkstes novads', 'Ilūkstes novads'),
    array('LV', 'Inčukalna novads', 'Inčukalna novads'),
    array('LV', 'Jaunjelgavas novads', 'Jaunjelgavas novads'),
    array('LV', 'Jaunpiebalgas novads', 'Jaunpiebalgas novads'),
    array('LV', 'Jaunpils novads', 'Jaunpils novads'),
    array('LV', 'LV-JL', 'Jelgavas novads'),
    array('LV', 'LV-JK', 'Jēkabpils novads'),
    array('LV', 'Kandavas novads', 'Kandavas novads'),
    array('LV', 'Kokneses novads', 'Kokneses novads'),
    array('LV', 'Krimuldas novads', 'Krimuldas novads'),
    array('LV', 'Krustpils novads', 'Krustpils novads'),
    array('LV', 'LV-KR', 'Krāslavas novads'),
    array('LV', 'LV-KU', 'Kuldīgas novads'),
    array('LV', 'Kārsavas novads', 'Kārsavas novads'),
    array('LV', 'Lielvārdes novads', 'Lielvārdes novads'),
    array('LV', 'LV-LM', 'Limbažu novads'),
    array('LV', 'Lubānas novads', 'Lubānas novads'),
    array('LV', 'LV-LU', 'Ludzas novads'),
    array('LV', 'Līgatnes novads', 'Līgatnes novads'),
    array('LV', 'Līvānu novads', 'Līvānu novads'),
    array('LV', 'LV-MA', 'Madonas novads'),
    array('LV', 'Mazsalacas novads', 'Mazsalacas novads'),
    array('LV', 'Mālpils novads', 'Mālpils novads'),
    array('LV', 'Mārupes novads', 'Mārupes novads'),
    array('LV', 'Naukšēnu novads', 'Naukšēnu novads'),
    array('LV', 'Neretas novads', 'Neretas novads'),
    array('LV', 'Nīcas novads', 'Nīcas novads'),
    array('LV', 'LV-OG', 'Ogres novads'),
    array('LV', 'Olaines novads', 'Olaines novads'),
    array('LV', 'Ozolnieku novads', 'Ozolnieku novads'),
    array('LV', 'LV-PR', 'Preiļu novads'),
    array('LV', 'Priekules novads', 'Priekules novads'),
    array('LV', 'Priekuļu novads', 'Priekuļu novads'),
    array('LV', 'Pārgaujas novads', 'Pārgaujas novads'),
    array('LV', 'Pāvilostas novads', 'Pāvilostas novads'),
    array('LV', 'Pļaviņu novads', 'Pļaviņu novads'),
    array('LV', 'Raunas novads', 'Raunas novads'),
    array('LV', 'Riebiņu novads', 'Riebiņu novads'),
    array('LV', 'Rojas novads', 'Rojas novads'),
    array('LV', 'Ropažu novads', 'Ropažu novads'),
    array('LV', 'Rucavas novads', 'Rucavas novads'),
    array('LV', 'Rugāju novads', 'Rugāju novads'),
    array('LV', 'Rundāles novads', 'Rundāles novads'),
    array('LV', 'LV-RE', 'Rēzeknes novads'),
    array('LV', 'Rūjienas novads', 'Rūjienas novads'),
    array('LV', 'Salacgrīvas novads', 'Salacgrīvas novads'),
    array('LV', 'Salas novads', 'Salas novads'),
    array('LV', 'Salaspils novads', 'Salaspils novads'),
    array('LV', 'LV-SA', 'Saldus novads'),
    array('LV', 'Saulkrastu novads', 'Saulkrastu novads'),
    array('LV', 'Siguldas novads', 'Siguldas novads'),
    array('LV', 'Skrundas novads', 'Skrundas novads'),
    array('LV', 'Skrīveru novads', 'Skrīveru novads'),
    array('LV', 'Smiltenes novads', 'Smiltenes novads'),
    array('LV', 'Stopiņu novads', 'Stopiņu novads'),
    array('LV', 'Strenču novads', 'Strenču novads'),
    array('LV', 'Sējas novads', 'Sējas novads'),
    array('LV', 'LV-TA', 'Talsu novads'),
    array('LV', 'LV-TU', 'Tukuma novads'),
    array('LV', 'Tērvetes novads', 'Tērvetes novads'),
    array('LV', 'Vaiņodes novads', 'Vaiņodes novads'),
    array('LV', 'LV-VK', 'Valkas novads'),
    array('LV', 'LV-VM', 'Valmieras novads'),
    array('LV', 'Varakļānu novads', 'Varakļānu novads'),
    array('LV', 'Vecpiebalgas novads', 'Vecpiebalgas novads'),
    array('LV', 'Vecumnieku novads', 'Vecumnieku novads'),
    array('LV', 'LV-VE', 'Ventspils novads'),
    array('LV', 'Viesītes novads', 'Viesītes novads'),
    array('LV', 'Viļakas novads', 'Viļakas novads'),
    array('LV', 'Viļānu novads', 'Viļānu novads'),
    array('LV', 'Vārkavas novads', 'Vārkavas novads'),
    array('LV', 'Zilupes novads', 'Zilupes novads'),
    array('LV', 'Ādažu novads', 'Ādažu novads'),
    array('LV', 'Ērgļu novads', 'Ērgļu novads'),
    array('LV', 'Ķeguma novads', 'Ķeguma novads'),
    array('LV', 'Ķekavas novads', 'Ķekavas novads'),
    array('LT', 'LT-AL', 'Alytaus Apskritis'),
    array('LT', 'LT-KU', 'Kauno Apskritis'),
    array('LT', 'LT-KL', 'Klaipėdos Apskritis'),
    array('LT', 'LT-MR', 'Marijampolės Apskritis'),
    array('LT', 'LT-PN', 'Panevėžio Apskritis'),
    array('LT', 'LT-SA', 'Šiaulių Apskritis'),
    array('LT', 'LT-TA', 'Tauragės Apskritis'),
    array('LT', 'LT-TE', 'Telšių Apskritis'),
    array('LT', 'LT-UT', 'Utenos Apskritis'),
    array('LT', 'LT-VL', 'Vilniaus Apskritis'),
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
    $bind = ['country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]];
    $installer->getConnection()->insert($installer->getTable('directory_country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory_country_region'));

    $bind = ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]];
    $installer->getConnection()->insert($installer->getTable('directory_country_region_name'), $bind);
}

/**
 * Fill table directory/currency_rate
 */
$data = [
    ['EUR', 'EUR', 1],
    ['EUR', 'USD', 1.415000000000],
    ['USD', 'EUR', 0.706700000000],
    ['USD', 'USD', 1],
];

$columns = ['currency_from', 'currency_to', 'rate'];
$installer->getConnection()->insertArray($installer->getTable('directory_currency_rate'), $columns, $data);

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'),
    array(
        'scope' => 'default',
        'scope_id' => 0,
        'path' => \Magento\Directory\Helper\Data::XML_PATH_DISPLAY_ALL_STATES,
        'value' => 1
    )
);

/**
 * @var $countries array
 */
$countries = array();
foreach ($installer->getDirectoryData()->getCountryCollection() as $country) {
    if ($country->getRegionCollection()->getSize() > 0) {
        $countries[] = $country->getId();
    }
}

$installer->getConnection()->insert(
    $installer->getTable('core_config_data'),
    array(
        'scope' => 'default',
        'scope_id' => 0,
        'path' => \Magento\Directory\Helper\Data::XML_PATH_STATES_REQUIRED,
        'value' => implode(',', $countries)
    )
);
