<?php
/**
 * Class following Singleton pattern for specific extension configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 13 Jan 2016
 * @license AGPL-3.0
 */
class CRM_Dataquality_Config {

  private static $_singleton;

  protected $_resourcesPath = null;

  /**
   * CRM_Dataquality_Config constructor.
   */
  function __construct() {

    $settings = civicrm_api3('Setting', 'Getsingle', array());
    $this->resourcesPath = $settings['extensionsDir'].'/net.trinfinity.orgis.mi.dataquality/resources/';
    $this->setRelationshipTypes();
    $this->setOptionGroups();
    $this->setGroups();
    $this->setTags();
    // customData as almost last one because it might need one of the previous ones (option group, relationship types)
    $this->setCustomData();

    // customProfiles as last one because it might need customData;
    $this->setProfiles();
  }

  /**
   * Singleton method
   *
   * @return CRM_Dataquality_Config
   * @access public
   * @static
   */
  public static function singleton() {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Dataquality_Config();
    }
    return self::$_singleton;
  }

  /**
   * Method to create or get relationship types
   *
   * @throws Exception when resource file could not be loaded
   */
  protected function setRelationshipTypes() {
    $jsonFile = $this->resourcesPath.'relationship_types.json';
    if (!file_exists($jsonFile)) {
      throw new Exception('Could not load relationship types configuration file for extension,
      contact your system administrator!'.$jsonFile);
    }
    $relationshipTypesJson = file_get_contents($jsonFile);
    $relationshipTypes = json_decode($relationshipTypesJson, true);
    foreach ($relationshipTypes as $relationName => $params) {
      CRM_Dataquality_Utils::createRelationshipType($params);
    }
  }

  /**
   * Method to create option groups
   *
   * @throws Exception when resource file not found
   * @access protected
   */
  protected function setOptionGroups() {
    $jsonFile = $this->resourcesPath.'option_groups.json';
    if (!file_exists($jsonFile)) {
      throw new Exception('Could not load option_groups configuration file for extension,
      contact your system administrator!');
    }
    $optionGroupsJson = file_get_contents($jsonFile);
    $optionGroups = json_decode($optionGroupsJson, true);
    foreach ($optionGroups as $name => $optionGroupParams) {
      CRM_Dataquality_Utils::createOptionGroup($optionGroupParams);
    }
  }

  /**
   * Method to create or get groups
   *
   * @throws Exception when resource file could not be loaded
   */
  protected function setGroups() {
    $jsonFile = $this->resourcesPath . 'groups.json';
    if (!file_exists($jsonFile)) {
      throw new Exception('Could not load groups configuration file for extension,
      contact your system administrator!');
    }
    $groupJson = file_get_contents($jsonFile);
    $groups = json_decode($groupJson, true);
    foreach ($groups as $params) {
      CRM_Dataquality_Utils::createGroup($params);
    }
  }

  /**
   * Method to create or get tags
   *
   * @throws Exception when resource file could not be loaded
   */
  protected function setTags() {
    $jsonFile = $this->resourcesPath . 'tags.json';
    if (!file_exists($jsonFile)) {
      throw new Exception('Could not load tags configuration file for extension,
      contact your system administrator!');
    }
    $tagsJson = file_get_contents($jsonFile);
    $tags = json_decode($tagsJson, true);
    foreach ($tags as $params) {
      CRM_Dataquality_Utils::createTag($params);
    }
  }

  /**
   * Method to set the custom data groups and fields
   *
   * @throws Exception when config json could not be loaded
   * @access protected
   */
  protected function setCustomData() {
    $jsonFile = $this->resourcesPath.'custom_data.json';
    if (!file_exists($jsonFile)) {
      throw new Exception('Could not load custom data configuration file for extension, contact your system administrator!');
    }
    $customDataJson = file_get_contents($jsonFile);
    $customData = json_decode($customDataJson, true);
    CRM_Core_Error::debug_log_message($jsonFile." ".json_last_error_msg());

    foreach ($customData as $customGroupName => $customGroupData) {
      $customGroupParams = $this->buildCustomGroupParams($customGroupData);
      $customGroup = CRM_Dataquality_Utils::createCustomGroup($customGroupParams);
      foreach ($customGroupData['fields'] as $customFieldName => $customFieldData) {
        $customFieldData['custom_group_id'] = $customGroup['id'];
        $customFieldParams = $customFieldData;
        $customField = CRM_Dataquality_Utils::createCustomField($customFieldParams);
        // weird fix because api does not treat option groups kindly
        if (isset($customFieldParams['option_group'])) {
     //     $this->fixCustomFieldOptionGroups($customField, $customFieldData['option_group']);
        }
      }
      // remove custom fields that are still on install but no longer in config
      CRM_Dataquality_Utils::removeUnwantedCustomFields($customGroup['id'], $customGroupData);
    }
  }

  /**
   * Method to create or get tags
   *
   * @throws Exception when resource file could not be loaded
   */
  protected function setProfiles() {
    $jsonFile = $this->resourcesPath . 'profiles.json';
    if (!file_exists($jsonFile)) {
      throw new Exception('Could not load profiles configuration file for extension,
     contact your system administrator!');
    }
    $groupsJson = file_get_contents($jsonFile);
    $groups = json_decode($groupsJson, true);
    foreach ($groups as $params) {
      $group = new CRM_Dataquality_ProfileGroup($params);
      $customGroup = $group->create();
      CRM_Core_Error::debug_log_message("setProfiles - Config.php:".print_r($customGroup,true));
      foreach ($params["fields"] as $profilefieldname => $profilefield){
        $profilefield["uf_group_id"] = $customGroup["id"];
        $field = new CRM_Dataquality_ProfileField($profilefield);
        $customfield = $field->create();
      }
    }
  }

  /**
   * Method to fix option group in custom field because API always creates an option group whatever you do
   * so change option group to the one we created and then remove the one api created
   *
   * @param $customField
   * @param $customFieldOptionGroupName
   * @throws CiviCRM_API3_Exception
   */
  protected function fixCustomFieldOptionGroups($customField, $customFieldOptionGroupName) {
    $optionGroup = CRM_Dataquality_Utils::getOptionGroupWithName($customFieldOptionGroupName);
    $qry = 'UPDATE civicrm_custom_field SET option_group_id = %1 WHERE id = %2';
    $params = array(
      1 => array($optionGroup['id'], 'Integer'),
      2 => array($customField['id'], 'Integer')
    );
    CRM_Core_DAO::executeQuery($qry, $params);
    civicrm_api3('OptionGroup', 'Delete', array('id' => $customField['option_group_id']));
  }

  /**
   * Method to build param list for custom group creation
   *
   * @param array $customGroupData
   * @return array $customGroupParams
   * @access protected
   */
  protected function buildCustomGroupParams($customGroupData) {
    $customGroupParams = array();
    foreach ($customGroupData as $name => $value) {
      if ($name != 'fields') {
        $customGroupParams[$name] = $value;
      }
    }
    // get relationship_type_id if extends relationship and extends_entity_column_value is set
    if ($customGroupParams['extends'] == 'Relationship') {
      if (isset($customGroupParams['extends_entity_column_value'])
        && !empty($customGroupParams['extends_entity_column_value'])) {
        $relationshipType = CRM_Dataquality_Utils::getRelationshipTypeWithName($customGroupParams['extends_entity_column_value']);
        if (!empty($relationshipType)) {
          $customGroupParams['extends_entity_column_value'] = $relationshipType['id'];
        }
      }
    }
    return $customGroupParams;
  }

  /**
   * Method to build param list for custom field creation
   *
   * @param array $customFieldData
   * @return array $customFieldParams
   * @access protected
   */
  protected function buildCustomFieldParams($customFieldData) {
    $customFieldParams = array();
    foreach ($customFieldData as $name => $value) {
      if ($name == "option_group") {
        $optionGroup = CRM_Dataquality_Utils::getOptionGroupWithName($value);
        if (empty($optionGroup)) {
          $optionGroup = CRM_Dataquality_Utils::createOptionGroup(array('name' => $value));
        }
        $customFieldParams['option_group_id'] = $optionGroup['id'];
      } else {
        $customFieldParams[$name] = $value;
      }
    }
    return $customFieldParams;
  }
}