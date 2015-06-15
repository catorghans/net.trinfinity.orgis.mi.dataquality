<?php
function civicrm_api3_pu_contact_getoverview ($params) {
  $sql = "select count(*) as total, p.pu_overview_1 as value, v.label as name, p.pu_action_3 as action, v2.label as actionname
         from civicrm_contact c left join civicrm_value_pu_fields_1 p ON c.id = p.entity_id
         left join civicrm_option_value v on p.pu_overview_1 = v.value
         left join civicrm_option_value v2 on p.pu_action_3 = v2.value
         WHERE (v.option_group_id IS NULL OR v.option_group_id = (select id from civicrm_option_group g where g.name =  'pu_overview_20150601084519'))
         AND (v2.option_group_id IS NULL OR v2.option_group_id = (select id from civicrm_option_group g2 where g2.name =  'pu_action_20150601084519'))
         group by p.pu_overview_1, p.pu_action_3 order by p.pu_overview_1 desc";

  return _civicrm_api3_basic_getsql ($params,$sql);
}
