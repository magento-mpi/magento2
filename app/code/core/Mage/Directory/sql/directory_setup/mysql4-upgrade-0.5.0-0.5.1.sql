alter table directory_country add address_template_plain text, add address_template_html text;

update directory_country set 

address_template_plain='{{firstname}} {{lastname}}
{{company}}
{{street1}}
{{street2}}
{{city}}, {{region}} {{postcode}}', 

address_template_html='<b>{{firstname}} {{lastname}}</b><br/>
{{street}}<br/>
{{city}}, {{region}} {{postcode}}<br/>
T: {{telephone}}' 

where country_id=223;