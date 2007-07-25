/**
 Tier prices update
*/

REPLACE INTO `eav_attribute` (entity_type_id, attribute_name, backend_model, backend_type, frontend_model, frontend_label) VALUES (10, 'tier_price', 'catalog/entity_product_attribute_backend_tierprice', 'decimal', 'catalog/entity_product_attribute_frontend_tierprice', 'Tier Price');