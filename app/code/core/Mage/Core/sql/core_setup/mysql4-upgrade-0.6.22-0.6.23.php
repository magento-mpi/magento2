<?php

$this->startSetup()->run("

alter table core_convert_profile add column gui_data text;

")->endSetup();