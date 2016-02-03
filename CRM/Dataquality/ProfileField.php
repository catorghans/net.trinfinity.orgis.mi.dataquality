<?php

/**
 * Created by PhpStorm.
 * User: hans
 * Date: 3-2-16
 * Time: 17:01
 */
class CRM_Dataquality_ProfileField
{
    protected $_apiParams = array();

    /**
     * CRM_Dataquality_ProfileField constructor.
     *
     * @param array $apiParams
     * @throws Exception when name not in apiParams
     */
    public function __construct($apiParams) {
        if (!isset($apiParams['field_name']) || empty($apiParams['field_name'])) {
            throw new Exception('Missing mandatory param name when constructing class CRM_Dataquality_ProfileField');
        }
        $this->_apiParams = $apiParams;
    }

    /**
     * Method to create or update a tag
     * @return mixed
     * @throws Exception when error from API Tag Create
     */

    public function create() {
        $existingProfileField = $this->getProfileFieldWithName();
        if (isset($existingProfileField['id'])) {
            $this->_apiParams['id'] = $existingProfileField['id'];
        }
        if (!isset($this->_apiParams['is_active'])) {
            $this->_apiParams['is_active'] = 1;
            if (empty($this->_apiParams['description']) || !isset($this->_apiParams['description'])) {
                $this->_apiParams['description'] = CRM_Civiconfig_Utils::buildLabelFromName($this->_apiParams['name']);
            }
        }
        try {
            $field = civicrm_api3('UFField', 'Create', $this->_apiParams);
            $fieldData = $field['values'];
        } catch (CiviCRM_API3_Exception $ex) {
            throw new Exception('Could not create or update profile group with name'
                .$this->_apiParams['name'].', error from API UFField Create: ' . $ex->getMessage());
        }
        return $fieldData[$field["id"]];

    }

    public function getProfileFieldWithName() {
        try {
            //if custom get custom field id
            if (substr($this->_apiParams['field_name'],0, strlen("custom."))==0){
                $name_array = explode(".", $this->_apiParams['field_name']);
                if (count($name_array) > 2){
                    $table = $name_array[1];
                    $fieldname = $name_array[2];

                    $customfieldgroup = civicrm_api3('CustomGroup', 'getsingle', array(
                        'sequential' => 1,
                        'return' => "id",
                        'table_name' => "civicrm_value_pu_history_fields",
                    ));
                    $customfieldgroupid = $customfieldgroup["id"];

                    if ($customfieldgroupid){

                        $customfield = civicrm_api3('CustomField', 'getsingle', array(
                            'sequential' => 1,
                            'custom_group_id' => $customfieldgroupid,
                            'name' => $fieldname,
                        ));



                        if ($customfield["id"]){
                            $customfieldname = "custom_".$customfield["id"];
                            $this->_apiParams['field_name'] = "$customfieldname";
                        }
                    }
                }
            }

            $tag = civicrm_api3('UFField', 'Getsingle', array('field_name' => $this->_apiParams['field_name']));
            return $tag;
        } catch (CiviCRM_API3_Exception $ex) {
            return FALSE;
        }
    }

}