<?php

$conn->query("update `core_config_field` set `frontend_type`='multiselect', `source_model`='adminhtml/system_config_source_store' where `path` like 'advanced/datashare/%'");
