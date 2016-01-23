<?php
function civicrm_api3_pu_contact_getstat ($params) {

  $sql = "select count(*) as total, p.new_pu_value as id, v.label as name
  from civicrm_value_pu_history_fields p inner join civicrm_activity a ON p.entity_id = a.id
              left join civicrm_activity_contact c on a.id=c.activity_id
              left join civicrm_option_value v on p.new_pu_value = v.value
              WHERE v.option_group_id = (select id from civicrm_option_group g where g.name =  'pu_value_addition_20151228190036')
              and c.record_type_id = 3
            and a.status_id = 1
            group by p.new_pu_value order by p.new_pu_value desc;
  ";
  return _civicrm_api3_basic_getsql ($params,$sql);
}
