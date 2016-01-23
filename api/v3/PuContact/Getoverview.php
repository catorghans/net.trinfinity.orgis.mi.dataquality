<?php
function civicrm_api3_pu_contact_getoverview ($params) {
 /* $sql = "select p.pu_overview_1 as value, v.label as name, p.pu_action_3 as action, v2.label as actionname,c.id,c.display_name
         from civicrm_contact c left join civicrm_value_pu_fields_1 p ON c.id = p.entity_id
         left join civicrm_option_value v on p.pu_overview_1 = v.value
         left join civicrm_option_value v2 on p.pu_action_3 = v2.value
         WHERE (v.option_group_id = (select id from civicrm_option_group g where g.name =  'pu_overview_20150601084519'))
         AND (v2.option_group_id IS NULL OR v2.option_group_id = (select id from civicrm_option_group g2 where g2.name =  'pu_action_20150601084519'))";
//         group by p.pu_overview_1, p.pu_action_3 order by p.pu_overview_1 desc";
*/
  $sql = "select a.id, p.new_pu_value as value, v.label as name, p.new_pu_action as action, v2.label as actionname, c.id as id, c.display_name as display_name, p.pu_automation_73 as automation
  from    civicrm_contact c
            inner join civicrm_activity_contact ac ON c.id = ac.contact_id
            inner join civicrm_activity a ON a.id=ac.activity_id
            inner join civicrm_value_pu_history_fields p ON p.entity_id = a.id
              inner join civicrm_option_value v on p.new_pu_value = v.value
               left join civicrm_option_value v2 on p.new_pu_action = v2.value
               WHERE v.option_group_id = (select id from civicrm_option_group g where g.name =  'pu_value_addition_20151228190036')
                   AND (v2.option_group_id IS NULL OR v2.option_group_id = (select id from civicrm_option_group g2 where g2.name =  'pu_action_20150601084519'))
  and ac.record_type_id = 3
  and a.status_id = 1";


  return _civicrm_api3_basic_getsql ($params,$sql);
}
