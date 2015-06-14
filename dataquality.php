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

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function dataquality_civicrm_preProcess($formName, &$form) {

}

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
          
          $result = civicrm_api3('Activity', 'get', array(
            'activity_type_id' => "puChanges",
            'api.ActivityContact.get' => array(
               'record_type_id' => "Activity Targets",
               'contact_id' => $cid,
            ),
            'options' => array('sort' => "id DESC", 'limit' => 1),
            'return' => $pu_value_field.",".$pu_description_field.",".$pu_action_field.",".$pu_how_field,
          ));
//          CRM_Core_Error::debug_log_message("MI CUSTOM curr activities:".print_r($result,true));
        
          if ($result["count"] ==1){
              foreach ($result["values"] as $previous){ //1 expected
                   $pu_value_old = $previous[$pu_value_field];
                   $pu_description_old = $previous[$pu_description_field];
                   $pu_action_old = $previous[$pu_action_field];
                   $pu_how_old = $previous[$pu_how_field];
              }
          }

  //        CRM_Core_Error::debug_log_message("MI CUSTOM v old:".$pu_value_old." v new:".$pu_value." d old: ". $pu_description_old. " d new:".$pu_desc);
         
          if ($pu_value != $pu_value_old || $pu_desc != $pu_description_old || $pu_action != $pu_action_old || $pu_how != $pu_how_old) {
            $result = civicrm_api3('Activity', 'create', array(
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
            ));        
         }
        }
     }
  
   }
   
}
