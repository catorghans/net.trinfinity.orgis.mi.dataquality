<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Dataquality_Upgrader extends CRM_Dataquality_Upgrader_Base
{

    // By convention, functions that look like "function upgrade_NNNN()" are
    // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

    /**
     * Example: Run an external SQL script when the module is installed.
     *
     * public function install() {
     * $this->executeSqlFile('sql/myinstall.sql');
     * }
     *
     * /**
     * Example: Run an external SQL script when the module is uninstalled.
     *
     * public function uninstall() {
     * $this->executeSqlFile('sql/myuninstall.sql');
     * }
     *
     * /**
     * Example: Run a simple query when a module is enabled.
     *
     * public function enable() {
     * CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
     * }
     *
     * /**
     * Example: Run a simple query when a module is disabled.
     *
     * public function disable() {
     * CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
     * }
     *
     * /**
     * Example: Run a couple simple queries.
     *
     * @return TRUE on success
     * @throws Exception
     *
     * public function upgrade_4200() {
     * $this->ctx->log->info('Applying update 4200');
     * CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
     * CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
     * return TRUE;
     * } // */

    public function enable(){
        CRM_Dataquality_Config::singleton();
    }

    public function upgrade_1001()
    {
        CRM_Dataquality_Config::singleton();


        //move pu contact fields to activity fields
        $pu_value_field = Null;
        $pu_description_field = Null;
        $pu_action_field = Null;
        $pu_how_field = Null;


        try {
            $result = civicrm_api3('CustomField', 'get', array(
                'name' => "new_pu_value",
            ));
            if ($result["id"]) {
                $pu_value_field = "custom_" . $result["id"];

                //set label

            }
        } catch (Exception $e) {
            // if it fails then for some reason this old table likely does not exist, not harm done.
        }
        try {
            $result = civicrm_api3('CustomField', 'get', array(
                'name' => "new_pu_description",
            ));
            if ($result["id"]) {
                $pu_description_field = "custom_" . $result["id"];


            }
        } catch (Exception $e) {
            // if it fails then for some reason this old table likely does not exist, not harm done.
        }
        try {
            $result = civicrm_api3('CustomField', 'get', array(
                'name' => "new_pu_action",

            ));
            if ($result["id"]){
                $pu_action_field = "custom_".$result["id"];

            }
        } catch (Exception $e) {
            // if it fails then for some reason this old table likely does not exist, not harm done.
        }
        try {
            $result = civicrm_api3('CustomField', 'get', array(
                'name' => "new_pu_how",
            ));
            if ($result["id"]){
                $pu_how_field = "custom_".$result["id"];

            }
        } catch (Exception $e) {
            // if it fails then for some reason this old table likely does not exist, not harm done.
        }
        try {
            $parameters = array();
            $sql = "select p.pu_overview_1 as value, p.pu_contact_2 as description, p.pu_action_3 as action, p.pu_how_4 as how, c.id
         from civicrm_contact c left join civicrm_value_pu_fields_1 p ON c.id = p.entity_id
         where p.pu_overview_1 > 1;
         ";
            $errorScope = CRM_Core_TemporaryErrorScope::useException();
            $dao = CRM_Core_DAO::executeQuery($sql, $parameters);
            $value = array();
            while ($dao->fetch()) {
                $value = $dao->toArray();

                $pu_old_value = intval($value["pu_value"]);

                if ($pu_old_value < 5) {
                    $pu_value = 1;
                }
                elseif ($pu_old_value< 8){
                    $pu_value = 2;
                }
                else {
                    $pu_value = 3;
                }

                $result = civicrm_api3('Activity', 'create', array(
                    "activity_type_id" => "puChanges",
                    $pu_value_field =>$pu_value,
                    $pu_description_field => $value["description"],
                    $pu_action_field => $value["action"],
                    $pu_how_field => $value["how"],
                    "status_id" => "Scheduled",
                    'target_contact_id' => $value["id"],
                    'subject' => $value["description"],
                ));
            }

        } catch (Exception $e) {
            // if it fails then for some reason this old table likely does not exist, no harm done.
        }

        //remove existing Report.

        try {
            $result = civicrm_api3('ReportInstance', 'get', array(
                'sequential' => 1,
                'report_id' => "net.trinfinity.orgis.mi.dataquality/pureport",
                'options' => array('limit' => 1000),
            ));

            if (isset($result["values"])) foreach ($result["values"] as $report) {
                $id = $report["id"];
                civicrm_api3("ReportInstance" , "delete" , array("id" => $id));
            }

            $result = civicrm_api3('ReportTemplate', 'get', array(
                'sequential' => 1,
                'value' => "net.trinfinity.orgis.mi.dataquality/pureport",
            ));
            if (isset($result["values"])) foreach ($result["values"] as $report) {
                $id = $report["id"];
                civicrm_api3("ReportTemplate" , "delete" , array("id" => $id));
            }

            //remove custom fields;
            $result = civicrm_api3('CustomGroup', 'getsingle', array(
                'sequential' => 1,
                'return' => "id",
                'name' => "Pu_fields",
            ));
            if (isset($result["id"]) && is_numeric($result["id"])){
                $groupid = $result["id"];

                $fieldresults = civicrm_api3('CustomField', 'get', array(
                    'sequential' => 1,
                    'return' => "id",
                    'custom_group_id' => $groupid,
                ));
                if (isset($fieldresults["values"])) foreach($fieldresults["values"] as $field){
                    civicrm_api3("CustomField","delete", array("id" => $field["id"]));
                }
                civicrm_api3("CustomGroup", "delete", array("id" => $groupid));
            }
            } catch (Exception $e) {
            // if it fails then for some reason this old table likely does not exist, no harm done.
        }

        //Pu_Overview


        //2Pu_Contact

        //3Pu_Action

        //4Pu_How


        //remove fieldset
        //Pu_fields

        return true;

    }
    /**
     * Example: Run an external SQL script.
     *
     * @return TRUE on success
     * @throws Exception
    public function upgrade_4201() {
     * $this->ctx->log->info('Applying update 4201');
     * // this path is relative to the extension base dir
     * $this->executeSqlFile('sql/upgrade_4201.sql');
     * return TRUE;
     * } // */


    /**
     * Example: Run a slow upgrade process by breaking it up into smaller chunk.
     *
     * @return TRUE on success
     * @throws Exception
    public function upgrade_4202() {
     * $this->ctx->log->info('Planning update 4202'); // PEAR Log interface
     *
     * $this->addTask(ts('Process first step'), 'processPart1', $arg1, $arg2);
     * $this->addTask(ts('Process second step'), 'processPart2', $arg3, $arg4);
     * $this->addTask(ts('Process second step'), 'processPart3', $arg5);
     * return TRUE;
     * }
     * public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
     * public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
     * public function processPart3($arg5) { sleep(10); return TRUE; }
     * // */


    /**
     * Example: Run an upgrade with a query that touches many (potentially
     * millions) of records by breaking it up into smaller chunks.
     *
     * @return TRUE on success
     * @throws Exception
    public function upgrade_4203() {
     * $this->ctx->log->info('Planning update 4203'); // PEAR Log interface
     *
     * $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
     * $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
     * for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
     * $endId = $startId + self::BATCH_SIZE - 1;
     * $title = ts('Upgrade Batch (%1 => %2)', array(
     * 1 => $startId,
     * 2 => $endId,
     * ));
     * $sql = '
     * UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
     * WHERE id BETWEEN %1 and %2
     * ';
     * $params = array(
     * 1 => array($startId, 'Integer'),
     * 2 => array($endId, 'Integer'),
     * );
     * $this->addTask($title, 'executeSql', $sql, $params);
     * }
     * return TRUE;
     * } // */

}
