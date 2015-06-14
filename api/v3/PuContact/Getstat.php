<?php
function civicrm_api3_pu_contact_getstat ($params) {
  $sql = "select count(*) as total, p.pu_overview_1 as id, v.label as name
         from civicrm_contact c left join civicrm_value_pu_fields_1 p ON c.id = p.entity_id
         left join civicrm_option_value v on p.pu_overview_1 = v.value
         WHERE v.option_group_id = (select id from civicrm_option_group g where g.name =  'pu_overview_20150601084519')
         group by p.pu_overview_1 order by p.pu_overview_1 desc";

  return _civicrm_api3_basic_getsql ($params,$sql);
}
