<?php


/**
 * @param $params
 * @return array
 */
function civicrm_api3_pu_contact_synccontacts ($params) {

  //get enabled contacts
    $parameters = array();
    $sql = "select id from civicrm_contact where is_deleted = 0";

    $lock = new CRM_Core_Lock('civimail.job.pusync');
    if (!$lock->isAcquired()) {
        throw new API_Exception('Could not acquire lock, another pu sync process is running');
    }

    $limit = CRM_Utils_Array::value('limit', $params, 0);




    if (isset($limit) && $limit > 0){


    }


    try{
        $dpu = new CRM_Dataquality_PuAutomation();
        $errorScope = CRM_Core_TemporaryErrorScope::useException();
        $dao = CRM_Core_DAO::executeQuery($sql,$parameters);
        $values = array();
        while ($dao->fetch()) {
            $values = $dao->toArray();

            $dpu->setAutomationContact($values["id"]);

        }
    }
    catch(Exception $e){
        $error = "crmAPI: ".$e->getMessage();
        $values="";
    }

    $lock->release();

    return civicrm_api3_create_success();

}