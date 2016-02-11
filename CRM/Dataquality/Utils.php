<?php
/**
 * Class with extension specific util functions
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 13 Jan 2016
 * @license AGPL-3.0
 */

class CRM_Dataquality_Utils {

  /**
   * Method to get custom field with name and custom_group_id
   *
   * @param string $customFieldName
   * @param int $customGroupId
   * @return array|bool
   * @access public
   * @static
   */
  public static function getCustomFieldWithNameCustomGroupId($customFieldName, $customGroupId) {
    try {
      $customField = civicrm_api3('CustomField', 'Getsingle', array('name' => $customFieldName, 'custom_group_id' => $customGroupId));
      return $customField;
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to get custom group with name
   *
   * @param string $customGroupName
   * @return array|bool
   * @access public
   * @static
   */
  public static function getCustomGroupWithName($customGroupName) {
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $customGroupName));
      return $customGroup;
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Function to get relationship type with name_a_b
   *
   * @param string $nameAB
   * @return array|bool
   * @access public
   * @static
   */
  public static function getRelationshipTypeWithName($nameAB) {
    try {
      $relationshipType = civicrm_api3('RelationshipType', 'Getsingle', array('name_a_b' => $nameAB));
      return $relationshipType;
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Function to get the group with a name
   *
   * @param string $groupName
   * @return array|bool
   * @access public
   * @static
   */
  public static function getGroupWithName($groupName) {
    try {
      $group = civicrm_api3('Group', 'Getsingle', array('name' => $groupName));
      return $group;
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Function to get the tag with a name
   *
   * @param string $tagName
   * @return array|bool
   * @access public
   * @static
   */
  public static function getTagWithName($tagName) {
    try {
      $tag = civicrm_api3('Tag', 'Getsingle', array('name' => $tagName));
      return $tag;
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Function to get the option group id
   *
   * @param string $optionGroupName
   * @return int $optionGroupId
   * @access public
   * @static
   */
  public static function getOptionGroupWithName($optionGroupName) {
    $params = array(
      'name' => $optionGroupName,
      'is_active' => 1);
    try {
      $optionGroup = civicrm_api3('OptionGroup', 'Getsingle', $params);
      return $optionGroup;
    } catch (CiviCRM_API3_Exception $ex) {
      return array();
    }
  }

  /**
   * Method to create option group if not exists yet
   *
   * @param $params
   * @return array
   * @throws Exception when error from API or when mandatory param name is missing
   * @access public
   */
  public static function createOptionGroup($params) {
    if (!isset($params['name']) || empty($params['name'])) {
      throw new Exception('Missing mandatory param name in CRM_Dataquality_Utils::createOptionGroup');
    }
    $existingOptionGroup = self::getOptionGroupWithName($params['name']);
    if (isset($existingOptionGroup['id'])) {
      $params['id'] = $existingOptionGroup['id'];
    }
    $params['is_active'] = 1;
    $params['is_reserved'] = 1;
    if (!isset($params['title'])) {
      $params['title'] = ucfirst($params['name']);
    }
    try {
      $optionGroup = civicrm_api3('OptionGroup', 'Create', $params);
      $optionGroupData = $optionGroup['values'];
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update option_group type with name'
        .$params['name'].', error from API OptionGroup Create: ' . $ex->getMessage());
    }
    return $optionGroupData;
  }

  /**
   * Function to create group if not exists yet
   *
   * @param array $params
   * @return array $groupData
   * @throws Exception when error in API Group Create or when missing mandatory param name
   * @access public
   * @static
   */
  public static function createGroup($params) {
    if (!isset($params['name']) || empty($params['name'])) {
      throw new Exception('Missing mandatory param name in CRM_Dataquality_Utils::createGroup');
    }
    $existingGroup = self::getGroupWithName($params['name']);
    if (isset($existingGroup['id'])) {
      $params['id'] = $existingGroup['id'];
    }
    if (!isset($params['is_active'])) {
      $params['is_active'] = 1;
    }
    if (empty($params['title']) || !isset($params['title'])) {
      $params['title'] = self::buildLabelFromName($params['name']);
    }
    try {
      $group = civicrm_api3('Group', 'Create', $params);

      /*
       * correct group name directly in database because creating with API causes
       * id to be added at the end of name which kind of defeats the idea of
       * having the same name in each install
       * Core bug https://issues.civicrm.org/jira/browse/CRM-14062, resolved in 4.4.4
       */
      if (CRM_Core_BAO_Domain::version() < 4.5) {
        $query = 'UPDATE civicrm_group SET name = %1 WHERE id = %2';
        $queryParams = array(
          1 => array($params['name'], 'String'),
          2 => array($group['id'], 'Integer'));
        CRM_Core_DAO::executeQuery($query, $queryParams);
      }
      $groupData = $group['values'];
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update group type with name'
        .$params['name'].', error from API Group Create: ' . $ex->getMessage());
    }
    return $groupData;
  }

  /**
   * Function to create tag if not exists yet
   *
   * @param array $params
   * @return array $tagData
   * @throws Exception when error in API Tag Create or when missing mandatory param name
   * @access public
   * @static
   */
  public static function createTag($params) {
    if (!isset($params['name']) || empty($params['name'])) {
      throw new Exception('Missing mandatory param name in CRM_Dataquality_Utils::createTag');
    }
    $existingTag = self::getTagWithName($params['name']);
    if (isset($existingTag['id'])) {
      $params['id'] = $existingTag['id'];
    }
    if (!isset($params['is_active'])) {
      $params['is_active'] = 1;
      if (empty($params['description']) || !isset($params['description'])) {
        $params['description'] = self::buildLabelFromName($params['name']);
      }
    }
    try {
      $tag = civicrm_api3('Tag', 'Create', $params);
      $tagData = $tag['values'];
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update tag type with name'
        .$params['name'].', error from API Tag Create: ' . $ex->getMessage());
    }
    return $tagData;
  }

  /**
   * Method to create relationship type
   *
   * @param $params
   * @return array
   * @throws Exception when params invalid
   * @throws Exception when error from API ContactType Create
   * @access public
   * @static
   */
  public static function createRelationshipType($params) {
    $relationshipType = array();
    if (!isset($params['name_a_b']) || empty($params['name_a_b']) || !isset($params['name_b_a']) || empty($params['name_b_a'])) {
      throw new Exception('When trying to create a Relationship Type name_a_b and name_b_a are mandatory parameter and
      can not be empty');
    }
    $existingRelationshipType = self::getRelationshipTypeWithName($params['name_a_b']);
    if (isset($existingRelationshipType['id'])) {
      $params['id'] = $existingRelationshipType['id'];
    }
    if (!isset($params['label_a_b']) || empty($params['label_a_b'])) {
      $params['label_a_b'] = self::buildLabelFromName($params['name_a_b']);
    }
    if (!isset($params['label_b_a']) || empty($params['label_b_a'])) {
      $params['label_b_a'] = self::buildLabelFromName($params['name_b_a']);
    }
    try {
      $relationshipType = civicrm_api3('RelationshipType', 'Create', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update relationship type with name '.$params['name_a_b']
        .', error from API RelationshipType Create: '.$ex->getMessage());
    }
    return $relationshipType['values'][$relationshipType['id']];
  }


  /**
   * Method to create custom group
   *
   * @param $params
   * @return array
   * @throws Exception when params invalid
   * @throws Exception when error from API CustomGroup Create or when no name in params
   * @access public
   * @static
   */
  public static function createCustomGroup($params) {
    if (!isset($params['name']) || empty($params['name']) || !isset($params['extends']) || empty($params['extends'])) {
      throw new Exception('When trying to create a Custom Group name and extends are mandatory parameters and can not be empty');
    }
    $existingCustomGroup = self::getCustomGroupWithName($params['name']);
    if (isset($existingCustomGroup['id'])) {
      $params['id'] = $existingCustomGroup['id'];
    }
    if (!isset($params['title']) || empty($params['title'])) {
      $params['title'] = self::buildLabelFromName($params['name']);
    }
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Create', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update custom group with name '.$params['name']
        .' to extend '.$params['extends'].', error from API CustomGroup Create: '.
        $ex->getMessage().", parameters : ".implode(";", $params));
    }
    return $customGroup['values'][$customGroup['id']];
  }

  /**
   * Method to create custom field
   *
   * @param $params
   * @return array
   * @throws Exception when params invalid
   * @throws Exception when error from API CustomField Create
   * @access public
   * @static
   */
  public static function createCustomField($params) {
    CRM_Core_Error::debug_log_message(print_r($params,true));
    if (!isset($params['name']) || empty($params['name']) || !isset($params['custom_group_id']) || empty($params['custom_group_id'])) {
      throw new Exception('When trying to create a Custom Field name and custom_group_id are mandatory parameters and can not be empty');
    }
    $existingCustomField = self::getCustomFieldWithNameCustomGroupId($params['name'], $params['custom_group_id']);
    if (isset($existingCustomField['id'])) {
      $params['id'] = $existingCustomField['id'];
    }
    if (!isset($params['label']) || empty($params['label'])) {
      $params['label'] = self::buildLabelFromName($params['name']);
    }
    try {
      $customField = civicrm_api3('CustomField', 'Create', $params);
      CRM_Core_Error::debug_log_message(print_r($params,true).print_r($customField,true));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('could not create or update custom field with name '.$params['name']
        .' in custom group '.$params['custom_group_id'].' error from API CustomField Create: '.$ex->getMessage());
    }
    return $customField['values'][$customField['id']];
  }

  /**
   * Public function to generate label from name
   *
   * @param $name
   * @return string
   * @access public
   * @static
   */
  public static function buildLabelFromName($name) {
    $nameParts = explode('_', strtolower($name));
    foreach ($nameParts as $key => $value) {
      $nameParts[$key] = ucfirst($value);
    }
    return implode(' ', $nameParts);
  }

  /**
   * Method to get list of active option values for select lists
   *
   * @param string $optionGroupName
   * @return array
   * @throws Exception when no option group found
   * @access public
   * @static
   */
  public static function getOptionGroupList($optionGroupName) {
    $valueList = array();
    $optionGroupParams = array(
      'name' => $optionGroupName,
      'return' => 'id');
    try {
      $optionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $optionGroupParams);
      $optionValueParams = array(
        'option_group_id' => $optionGroupId,
        'is_active' => 1,
        'options' => array('limit' => 99999));
      $optionValues = civicrm_api3('OptionValue', 'Get', $optionValueParams);
      foreach ($optionValues['values'] as $optionValue) {
        $valueList[$optionValue['value']] = $optionValue['label'];
      }
      $valueList[0] = ts('- select -');
      asort($valueList);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find an option group with name '.$optionGroupName
        .' contact your system administrator. Error from API OptionGroup Getvalue: '.$ex->getMessage());
    }
    return $valueList;
  }

  /**
   * Method to remove custom fields for custom group in params that are not in $customGroup['custom_fields']
   *
   * @param int $customGroupId
   * @param array $configCustomGroupData
   * @return boolean
   * @access public
   * @static
   */
  public static function removeUnwantedCustomFields($customGroupId, $configCustomGroupData) {
    if (empty($customGroupId)) {
      return FALSE;
    }
    // first get all existing custom fields from the custom group
    try {
      $existingCustomFields = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $customGroupId));
      foreach ($existingCustomFields['values'] as $existingId => $existingField) {
        // if existing field not in config custom data, delete custom field
        if (!isset($configCustomGroupData['fields'][$existingField['name']])) {
          civicrm_api3('CustomField', 'Delete', array('id' => $existingId));
        }
      }
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
    return TRUE;
  }


}