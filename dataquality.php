<?php

require_once 'dataquality.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dataquality_civicrm_config(&$config) {
  _dataquality_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function dataquality_civicrm_xmlMenu(&$files) {
  _dataquality_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function dataquality_civicrm_install() {
  _dataquality_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dataquality_civicrm_uninstall() {
  _dataquality_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dataquality_civicrm_enable() {
  _dataquality_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dataquality_civicrm_disable() {
  _dataquality_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function dataquality_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _dataquality_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function dataquality_civicrm_managed(&$entities) {
  _dataquality_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dataquality_civicrm_caseTypes(&$caseTypes) {
  _dataquality_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dataquality_civicrm_angularModules(&$angularModules) {
_dataquality_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function dataquality_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _dataquality_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *
*/


function dataquality_civicrm_pre($op, $objectName, $id, &$params){
  // print " pre:".$op." ".$objectName;

}


/**
 * Implements hook_civicrm_custom().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_custom
 *
 * create an history activity whenever Pu data is changed
*/

function dataquality_civicrm_custom( $op, $groupID, $entityID, &$params ) {
    if ( $op != 'create' && $op != 'edit' ) {
        return;
    }

   try {
     $result = civicrm_api3('CustomGroup', 'getsingle', array(
       'return' => "name",
       'id' => $groupID,
     ));
   } catch (Exception $e){
     CRM_Core_Error::debug_log_message("MI CUSTOM error:".$e->getMessage().$op." ".$groupID." ".$entityID." ".print_r($params,true));
     return;
   }


   if ($result["name"] == "Pu_fields"){
     $pu_value=-1;
     $pu_desc="";
     $pu_action=-1;
     $pu_how="";
     foreach ($params as $field){
        if ($field["column_name"] == "pu_overview_1") {
           $pu_value = $field["value"];
        }
        if ($field["column_name"] == "pu_contact_2") {
           $pu_desc = $field["value"];
        }
        if ($field["column_name"] == "pu_action_3") {
           $pu_action = $field["value"];
        }
        if ($field["column_name"] == "pu_how_4") {
           $pu_how = $field["value"];
        }

     }
     if ($pu_value >= 0){
        $cid = $entityID;
        $subject = "";

   //get pu value name
        try {
          $resultov = civicrm_api3('OptionValue', 'getsingle', array(
            'sequential' => 1,
            'option_group_id' => "pu_overview_20150601084519",
            'value' => $pu_value,
          ));
        } catch (Exception $e){}

        if ($resultov["label"]){
            $subject .= $resultov["label"];
        }

        $pu_value_field = Null;
        $pu_description_field = Null;
        $pu_action_field = Null;
        $pu_how_field = Null;

        $pu_old_value_field = Null;
        $pu_old_description_field = Null;
        $pu_old_action_field = Null;
        $pu_old_how_field = Null;

        $pu_value_old = Null;
        $pu_description_old = "";
        $pu_action_old = Null;
        $pu_how_old = "";

         //get field names

        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "old_pu_value",
        ));
        if ($result["id"]){ $pu_old_value_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "old_pu_description",
        ));
        if ($result["id"]){ $pu_old_description_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "old_pu_action",
        ));
        if ($result["id"]){ $pu_old_action_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "old_pu_how",
        ));
        if ($result["id"]){ $pu_old_how_field = "custom_".$result["id"]; }


        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "new_pu_value",
        ));
        if ($result["id"]){ $pu_value_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "new_pu_description",
        ));
        if ($result["id"]){ $pu_description_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "new_pu_action",
        ));
        if ($result["id"]){ $pu_action_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
          'name' => "new_pu_how",
        ));
        if ($result["id"]){ $pu_how_field = "custom_".$result["id"]; }



        if (isset($pu_value_field) && isset($pu_description_field)){


          $error = Null;
          $is_error = 0;
          $values = Null;
          $parameters = array();

          $sql = "select h.*
            from civicrm_value_pu_history_fields h inner join civicrm_activity a ON h.entity_id = a.id 
              left join civicrm_activity_contact c on a.id=c.activity_id 
            where c.contact_id = ".$cid."
            and c.record_type_id = 3
            order by id desc limit 1;";

          try{
            $errorScope = CRM_Core_TemporaryErrorScope::useException();
            $dao = CRM_Core_DAO::executeQuery($sql,$parameters);
            $values = array();
            while ($dao->fetch()) {
                $values[] = $dao->toArray();
            }
          }
          catch(Exception $e){
            $is_error=1;
            $error = "crmAPI: ".$e->getMessage();
            $values="";
          }

          if ($is_error == 0){
              foreach ($values as $previous){ //1 expected
                   $pu_value_old = $previous["new_pu_value"];
                   $pu_description_old = $previous["new_pu_description"];
                   $pu_action_old = $previous["new_pu_action"];
                   $pu_how_old = $previous["new_pu_how"];
              }
          }

          if ($pu_value != $pu_value_old || $pu_desc != $pu_description_old || $pu_action != $pu_action_old || $pu_how != $pu_how_old) {
      /*      $result = civicrm_api3('Activity', 'create', array(
              'activity_type_id' => "puChanges",
              $pu_old_value_field => $pu_value_old,
              $pu_old_description_field => $pu_description_old,
              $pu_old_action_field => $pu_action_old,
              $pu_old_how_field => $pu_how_old,

              $pu_value_field => $pu_value,
              $pu_description_field => $pu_desc,
              $pu_action_field => $pu_action,
              $pu_how_field => $pu_how,

              'target_id' => $cid,
              'subject' => $subject,
            ));*/
         }
        }
     }

   }

}

function dataquality_civicrm_permission(&$permissions) {
  $version = CRM_Utils_System::version();
  if (version_compare($version, '4.6.1') >= 0) {
    $permissions += array(
      'access Pu Fields' => array(
        ts('Access Pu Fields', array('domain' => 'net.trinfinity.orgis.mi.dataquality')),
        ts('Grants the necessary permissions to access the Pu Fields', array('domain' => 'net.trinfinity.orgis.mi.dataquality')),
      ),
    );
  }
  else {
    $permissions += array(
      'access Pu Fields' => ts('Access Pu Fields', array('domain' => 'net.trinfinity.orgis.mi.dataquality')),
    );
  }
}

/**
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function dataquality_civicrm_post($op, $objectName, $objectId, &$objectRef ){
  if ($objectName == "Individual" || $objectName == "Household" || $objectName == "Organization"){
      if ($op == "create" || $op == "edit" || $op == "restore"){
          _dataquality_pu_automation($objectId);
      }
  }

  if ($objectName == "GroupContact"){
    if ($op == "create"){
     CRM_Core_Error::debug_log_message("MI group:".$op." ".$objectId);
       // CRM_Core_Error::debug_log_message("MI group".print_r(CRM_Contact_BAO_SavedSearch::contactIDsSQL(2),true));
       // CRM_Contact_BAO_GroupContactCache::contactGroup()
    }
    elseif ($op == "delete") {
     CRM_Core_Error::debug_log_message("MI group:".$op." ".$objectId);
    }

  }

}

/**
 * @param $objectName
 * @param $contactid
 * @param $objectRef
 */
function _dataquality_pu_automation($contactid){
   //Get open pu activities

    //get field names
    $pu_value_field = Null;
    $pu_description_field = Null;
    $pu_action_field = Null;
    $pu_how_field = Null;
    $pu_automationtype_field = Null;
    $pu_automation_field = Null;
    $pu_value_addition_field = Null;

    $result = civicrm_api3('CustomField', 'get', array(
        'name' => "new_pu_value",
    ));
    if ($result["id"]){ $pu_value_field = "custom_".$result["id"]; }

    $result = civicrm_api3('CustomField', 'get', array(
        'name' => "new_pu_description",
    ));
    if ($result["id"]){ $pu_description_field = "custom_".$result["id"]; }

    $result = civicrm_api3('CustomField', 'get', array(
        'name' => "new_pu_action",
    ));
    if ($result["id"]){ $pu_action_field = "custom_".$result["id"]; }

    $result = civicrm_api3('CustomField', 'get', array(
        'name' => "new_pu_how",
    ));
    if ($result["id"]){ $pu_how_field = "custom_".$result["id"]; }

    $result = civicrm_api3('CustomField', 'get', array(
        'name' => "Automation_Type",
    ));
    if ($result["id"]){ $pu_automationtype_field = "custom_".$result["id"]; }

    $result = civicrm_api3('CustomField', 'get', array(
        'name' =>  "Pu_Automation",
    ));
    if ($result["id"]){ $pu_automation_field = "custom_".$result["id"]; }

    $result = civicrm_api3('CustomField', 'get', array(
        'name' =>  "Pu_value_addition",
    ));
    if ($result["id"]){ $pu_value_addition_field = "custom_".$result["id"]; }

    $result = civicrm_api3('CustomField', 'get', array(
        'name' =>  "Automated_Pu_Description",
    ));
    if ($result["id"]){ $pu_automation_description_field = "custom_".$result["id"]; }


    /*
     $result = civicrm_api3('Activity', 'get', array(
        'sequential' => 1,
        'return' => $pu_value_field.",".$pu_action_field.",".$pu_automationtype_field.",".$pu_automation_field,
        'activity_type_id' => "puChanges",
        'status_id' => "Scheduled",
        'api.ActivityContact.get' => array('record_type_id' => "Activity Targets", 'contact_id' => $objectId),
        'options' => array('limit' => 1000),
    ));
    CRM_Core_Error::debug_log_message(print_r($result,true));
    */
    //get open pu activities
    $error = Null;
    $is_error = 0;
    $pu_activities = Null;
    $parameters = array();

    $sql = "select h.*
            from civicrm_value_pu_history_fields h inner join civicrm_activity a ON h.entity_id = a.id
              left join civicrm_activity_contact c on a.id=c.activity_id
            where c.contact_id = ".$contactid."
            and c.record_type_id = 3
            and a.status_id = 1
            order by id desc;";

    try{
        $errorScope = CRM_Core_TemporaryErrorScope::useException();
        $dao = CRM_Core_DAO::executeQuery($sql,$parameters);
        $pu_activities = array();
        while ($dao->fetch()) {
            $pu_activities[] = $dao->toArray();
        }
    }
    catch(Exception $e){
        $is_error=1;
        $error = "crmAPI: ".$e->getMessage();
        $pu_activities="";
    }

    if ($is_error == 0) {
      /*  foreach ($pu_activities as $previous) { //1 expected

        }
        */
     //   CRM_Core_Error::debug_log_message(print_r($pu_activities,true));
    }



    //Get pu Groups

    $resultgroups = civicrm_api3('Group', 'get', array(
        'sequential' => 1,
        'return' => "id,name,saved_search_id,".$pu_value_addition_field.",title",
         $pu_value_addition_field => array('>=' => 1),
        'is_active' => 1,
        'options' => array('limit' => 100000),
    ));
//    CRM_Core_Error::debug_log_message(print_r($resultgroups,true));

    //get all smart groups this contact is member from
    $contactgroups = CRM_Contact_BAO_GroupContactCache::contactGroup($contactid);
//    CRM_Core_Error::debug_log_message("contactgroups:".print_r($contactgroups,true));

    $pu_automation_type_start = "G"; //pu automation of type G (group)

    //foreach group
    if ($resultgroups["is_error"] == 0){
        foreach ($resultgroups["values"] as $group){
            if ($group[$pu_value_addition_field] > 0){
                //if smart group
                if (isset($group["saved_search_id"]) && $group["saved_search_id"]){
                    //is current user part of group?
                    $currentusergroupmember = false;

                    if (isset($contactgroups["group"])) foreach($contactgroups["group"] as $contactgroup){
                        if ($group["id"] == $contactgroup["id"]){
                            $currentusergroupmember = true;
                            break;
                        }

                    }

                    //if user is group member
                    if ($currentusergroupmember == true) {
                        //if no activity exists, create it
                        $activity_found = false;
                        foreach ($pu_activities as $pu_activity) {
                            if ($pu_activity["automation_type_74"] == $pu_automation_type_start.$group["id"]) {
                                $activity_found = true;
                                break;
                            }
                        }
                        if (!$activity_found){
                            //create activity
                            $pu_value = $group[$pu_value_addition_field];
                            $pu_desc = $group[$pu_automation_description_field]?$group[$pu_automation_description_field]:"";
                            $pu_action = "";
                            $pu_how = "";
                            $subject = $pu_desc;
                            $params = array("activity_type_id" => "puChanges",
                                "status_id" => 1,
                                 $pu_value_field => $pu_value,
                                 $pu_description_field => $pu_desc,
                                 $pu_action_field => $pu_action,
                                 $pu_how_field => $pu_how,
                                 'target_id' => $contactid,
                                 'subject' => $subject,
                                 $pu_automationtype_field => $pu_automation_type_start.$group["id"],
                                 $pu_automation_field => 1,
                            );
                            $resultactivity = civicrm_api3('Activity', 'create', $params);
                            CRM_Core_Error::debug_log_message("pu activity created:".print_r($resultactivity,true));


                        }
                    }


                    //if user is not group member
                    else {
                        //if activity exists, delete it
                        $activity_found = false;
                        foreach ($pu_activities as $pu_activity) {
                            if ($pu_activity["automation_type_74"] == $pu_automation_type_start.$group["id"]) {
                                $activity_found = true;
                                //close it
                                $params = array("id" => $pu_activity["entity_id"],
                                    "status_id" => 2);
                                $resultactivity = civicrm_api3('Activity', 'create', $params);
                                CRM_Core_Error::debug_log_message("pu activity updated:".print_r($resultactivity,true));
                                break;
                            }
                        }
                    }


                }
                //if normal group
                else {
                    //is current user part of group?

                    //is there an open pu activity with this group

                }






            }
        }

    }



    //recalc pu

}

/**
 * @param $dao
 */
function dataquality_civicrm_postSave_civicrm_group_contact_cache ($dao) {
     CRM_Core_Error::debug_log_message("MI group contact cache:".print_r($dao,true));

}

/**
 * @param $dao
 */
function dataquality_civicrm_postSave_civicrm_group_contact ($dao) {
     CRM_Core_Error::debug_log_message("MI group contact:".print_r($dao,true));



}

