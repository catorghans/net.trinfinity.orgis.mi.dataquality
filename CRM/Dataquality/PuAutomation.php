<?php

/**
 * Created by PhpStorm.
 * User: hans
 * Date: 20-1-16
 * Time: 11:33
 */
class CRM_Dataquality_PuAutomation
{
     protected $pu_automation_smart_groups = Null;
     protected $pu_automation_type_start = "G";

    //Fields
    protected $pu_value_field = Null;
    protected $pu_description_field = Null;
    protected $pu_action_field = Null;
    protected $pu_how_field = Null;
    protected $pu_automationtype_field = Null;
    protected $pu_automation_field = Null;
    protected $pu_value_addition_field = Null;
    protected $pu_automation_action_field = Null;
    protected $pu_automation_description_field = Null;
    protected $pu_automation_how_field = Null;

    /**
     * CRM_Dataquality_PuAutomation constructor.
     */
    public function __construct()
    {
        $result = civicrm_api3('CustomField', 'get', array(
            'name' => "new_pu_value",
        ));
        if ($result["id"]){ $this->pu_value_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' => "new_pu_description",
        ));
        if ($result["id"]){ $this->pu_description_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' => "new_pu_action",
        ));
        if ($result["id"]){ $this->pu_action_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' => "new_pu_how",
        ));
        if ($result["id"]){ $this->pu_how_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' => "Automation_Type",
        ));
        if ($result["id"]){ $this->pu_automationtype_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' =>  "Pu_Automation",
        ));
        if ($result["id"]){ $this->pu_automation_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' =>  "Pu_value_addition",
        ));
        if ($result["id"]){ $this->pu_value_addition_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' =>  "Automated_Pu_Description",
        ));
        if ($result["id"]){ $this->pu_automation_description_field = "custom_".$result["id"]; }

        $result = civicrm_api3('CustomField', 'get', array(
            'name' =>  "Automated_Pu_Action",
        ));
        if ($result["id"]){ $this->pu_automation_action_field = "custom_".$result["id"]; }
        $result = civicrm_api3('CustomField', 'get', array(
            'name' =>  "Automated_Pu_How",
        ));
        if ($result["id"]){ $this->pu_automation_how_field = "custom_".$result["id"]; }
    }



    public function setAutomationGroups($enforce = false){
        //Get pu Groups

        if ($this->pu_automation_smart_groups == Null || $enforce){
            $this->pu_automation_smart_groups = array();

            $resultgroups = civicrm_api3('Group', 'get', array(
                'sequential' => 1,
                'return' => "id,name,saved_search_id,".$this->pu_value_addition_field.",title,".$this->pu_automation_action_field.",".$this->pu_automation_how_field,
                $this->pu_value_addition_field => array('>=' => 1),
                'is_active' => 1,
                'options' => array('limit' => 100000),
            ));
            if ($resultgroups["is_error"] == 0) {
                foreach ($resultgroups["values"] as $group) {
                    if ($group[$this->pu_value_addition_field] > 0) {
                        //if smart group
                        if (isset($group["saved_search_id"]) && $group["saved_search_id"]) {
                            $this->pu_automation_smart_groups[] = $group;
                        }
                    }
                }
            }

        }
    }

    public function setAutomationContact($contactid){
        $this->setAutomationGroups();

        //get open pu activities
        $error = Null;
        $is_error = 0;
        $pu_activities = Null;
        $parameters = array();

        $sql = "select h.*,a.status_id
            from civicrm_value_pu_history_fields h inner join civicrm_activity a ON h.entity_id = a.id
              left join civicrm_activity_contact c on a.id=c.activity_id
            where c.contact_id = ".$contactid."
            and c.record_type_id = 3
            and a.status_id in (1,2)
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


        //smart groups

        $contactgroups = CRM_Contact_BAO_GroupContactCache::contactGroup($contactid);

        foreach ($this->pu_automation_smart_groups as $group){
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
                foreach ($pu_activities as $punr => $pu_activity) {
                    if ($pu_activity["automation_type_74"] == $this->pu_automation_type_start.$group["id"]) {
                        $activity_found = true;
                        $pu_activities[$punr]["found"] = true;
                        break;
                    }
                }
                if (!$activity_found){
                    //create activity
                    $pu_value = $group[$this->pu_value_addition_field];
                    $pu_desc = isset($group[$this->pu_automation_description_field])?$group[$this->pu_automation_description_field]:"";
                    $pu_action = isset($group[$this->pu_automation_action_field])?$group[$this->pu_automation_action_field]:"";
                    $pu_how = isset($group[$this->pu_automation_how_field])?$group[$this->pu_automation_how_field]:"";
                    $subject = $pu_desc;
                    $params = array("activity_type_id" => "puChanges",
                        "status_id" => 1,
                        $this->pu_value_field => $pu_value,
                        $this->pu_description_field => $pu_desc,
                        $this->pu_action_field => $pu_action,
                        $this->pu_how_field => $pu_how,
                        'target_id' => $contactid,
                        'subject' => $subject,
                        $this->pu_automationtype_field => $this->pu_automation_type_start.$group["id"],
                        $this->pu_automation_field => 1,
                    );
                    $resultactivity = civicrm_api3('Activity', 'create', $params);
                //    CRM_Core_Error::debug_log_message("pu activity created:".print_r($resultactivity,true));


                }
            }
            //if user is not group member
            else {
                //if open activity exists, close it
                $activity_found = false;
                foreach ($pu_activities as $pu_activity) {
                    if ($pu_activity["automation_type_74"] == $this->pu_automation_type_start.$group["id"]) {
                        $activity_found = true;
                        $pu_activity["found"] = true;
                        //close it
                        if ($pu_activity["status_id"] == "1") {
                            $params = array("id" => $pu_activity["entity_id"],
                                "status_id" => 2);
                            $resultactivity = civicrm_api3('Activity', 'create', $params);
                            //          CRM_Core_Error::debug_log_message("pu activity updated:".print_r($resultactivity,true));
                            break;
                        }
                    }
                }
            }


        }
        //automated pu activities from groups where there is no group anymore
        //close activity
        CRM_Core_Error::debug_log_message('activities array'.print_r($pu_activities,true));
        foreach ($pu_activities as $pu_activity) {
            if (!isset($pu_activity["found"]) || ($pu_activity["found"] == false)){

                if (substr($pu_activity["automation_type_74"],0, strlen($this->pu_automation_type_start)) === $this->pu_automation_type_start) {

                    //close it
                    $params = array("id" => $pu_activity["entity_id"],
                        "status_id" => 2);
                    $resultactivity = civicrm_api3('Activity', 'create', $params);
          //          CRM_Core_Error::debug_log_message("pu activity closed, no group:" . print_r($pu_activity, true) . print_r($resultactivity, true));
                }
            }
        }
    }
}