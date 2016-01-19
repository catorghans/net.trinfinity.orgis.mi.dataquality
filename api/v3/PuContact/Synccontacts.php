<?php


/**
 * @param $params
 * @return array
 */
function civicrm_api3_pu_contact_synccontacts ($params) {

  //get enabled contacts
    $parameters = array();

    $sql = "select id from civicrm_contact where is_deleted = 0";

    try{
        $errorScope = CRM_Core_TemporaryErrorScope::useException();
        $dao = CRM_Core_DAO::executeQuery($sql,$parameters);
        $values = array();
        while ($dao->fetch()) {
            $values = $dao->toArray();
            _dataquality_pu_automation($values["id"]);
        }
    }
    catch(Exception $e){
        $error = "crmAPI: ".$e->getMessage();
        $values="";
    }


    return civicrm_api3_create_success();

}