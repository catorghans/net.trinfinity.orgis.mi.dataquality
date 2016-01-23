<?php
function civicrm_api3_pu_contact_getemptystat ($params) {

  $sql = "select count(*) as total, p.new_pu_value as id, v.label as name
  from    civicrm_contact c
            left join civicrm_activity_contact ac ON c.id = ac.contact_id
            left join civicrm_activity a ON a.id=ac.activity_id
            left join civicrm_value_pu_history_fields p ON p.entity_id = a.id
              left join civicrm_option_value v on p.new_pu_value = v.value
              WHERE v.option_group_id IS NULL
              and ac.record_type_id = 3
            and a.status_id = 1
            group by p.new_pu_value order by p.new_pu_value desc;
  ";

  return _civicrm_api3_basic_getsql ($params,$sql);
}
