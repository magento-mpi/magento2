<?php
$conn->multi_query(<<<EOT

UPDATE eav_attribute SET default_value = '0.00', is_required = 0 where attribute_code = 'store_balance';

EOT
);
