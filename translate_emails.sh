#php -f generate_emailtemplates.php -- --locale en_US --output ./saas/en_US-translate.csv
#php -f generate_emailtemplates.php -- --locale de_DE --output ./saas/de_DE-translate.csv
#php -f generate_emailtemplates.php -- --locale en_GB --output ./saas/en_GB-translate.csv
#php -f generate_emailtemplates.php -- --locale es_ES --output ./saas/es_ES-translate.csv
#php -f generate_emailtemplates.php -- --locale fr_FR --output ./saas/fr_FR-translate.csv
#php -f generate_emailtemplates.php -- --locale nl_NL --output ./saas/nl_NL-translate.csv

#php -f generate_emailtemplates.php -- --locale en_US --output ./saas/en_US-translate/
#php -f generate_emailtemplates.php -- --locale de_DE --output ./saas/de_DE-translate/
#php -f generate_emailtemplates.php -- --locale en_GB --output ./saas/en_GB-translate/
#php -f generate_emailtemplates.php -- --locale es_ES --output ./saas/es_ES-translate/
#php -f generate_emailtemplates.php -- --locale fr_FR --output ./saas/fr_FR-translate/
#php -f generate_emailtemplates.php -- --locale nl_NL --output ./saas/nl_NL-translate/

#mkdir ./merge
#cp ./saas/en_US-translate.csv ./merge/
#cp ./saas/de_DE-translate.csv ./merge/
#cp ./saas/en_GB-translate.csv ./merge/
#cp ./saas/es_ES-translate.csv ./merge/
#cp ./saas/fr_FR-translate.csv ./merge/
#cp ./saas/nl_NL-translate.csv ./merge/

#php -f generate_emailtemplates.php -- --merge-locales merge/en_US-translate.csv merge/de_DE-translate.csv merge/de_DE.csv
#php -f generate_emailtemplates.php -- --merge-locales merge/en_US-translate.csv merge/en_GB-translate.csv merge/en_GB.csv
#php -f generate_emailtemplates.php -- --merge-locales merge/en_US-translate.csv merge/es_ES-translate.csv merge/es_ES.csv
#php -f generate_emailtemplates.php -- --merge-locales merge/en_US-translate.csv merge/fr_FR-translate.csv merge/fr_FR.csv
#php -f generate_emailtemplates.php -- --merge-locales merge/en_US-translate.csv merge/nl_NL-translate.csv merge/nl_NL.csv

#cp ./merge/en_US-translate.csv ./saas_merged/en_US.csv
#cp ./merge/de_DE.csv ./saas_merged/de_DE.csv
#cp ./merge/en_GB.csv ./saas_merged/en_GB.csv
#cp ./merge/es_ES.csv ./saas_merged/es_ES.csv
#cp ./merge/fr_FR.csv ./saas_merged/fr_FR.csv
#cp ./merge/nl_NL.csv ./saas_merged/nl_NL.csv

#php -f generate_emailtemplates.php -- --split saas_merged/en_US.csv
#php -f generate_emailtemplates.php -- --split saas_merged/de_DE.csv
#php -f generate_emailtemplates.php -- --split saas_merged/en_GB.csv
#php -f generate_emailtemplates.php -- --split saas_merged/es_ES.csv
#php -f generate_emailtemplates.php -- --split saas_merged/fr_FR.csv
#php -f generate_emailtemplates.php -- --split saas_merged/nl_NL.csv

#To assemble back translation to template put translated CSVs into ./saas/de_DE-translate/*.csv and run
#php -f generate_emailtemplates.php -- --locale de_DE --translate ./saas/de_DE-translate/ --output ./saas/de_DE-translated/