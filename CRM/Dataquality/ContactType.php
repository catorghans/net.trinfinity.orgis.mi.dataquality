<?php
/**
 * Class for ContactType configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Feb 2016
 * @license AGPL-3.0
 */
class CRM_Dataquality_ContactType {

  protected $_apiParams = array();

  /**
   * Method to validate params for create
   *
   * @param $params
   * @throws Exception when missing mandatory params
   */
  protected function validateCreateParams($params) {
    if (!isset($params['name']) || empty($params['name'])) {
      throw new Exception('Missing mandatory param name in class CRM_Dataquality_ContactType');
    }
    $this->_apiParams = $params;
  }

  /**
   * Method to create contact type
   *
   * @param array $params
   * @return mixed
   * @throws Exception when error from API ContactType Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithName($this->_apiParams['name']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    if (!isset($this->_apiParams['label']) || empty($this->_apiParams['label'])) {
      $this->_apiParams['label'] = CRM_Dataquality_Utils::buildLabelFromName($this->_apiParams['name']);
    }
    try {
      civicrm_api3('ContactType', 'Create', $this->_apiParams);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update contact type with name '.$this->_apiParams['name']
        .', error from API ContactType Create: '.$ex->getMessage());
    }
  }

  /**
   * Method to get contact sub type with name
   *
   * @param string $contactTypeName
   * @return array|bool
   * @access public
   * @static
   */
  public function getWithName($contactTypeName) {
    try {
      return civicrm_api3('ContactType', 'Getsingle', array('name' => $contactTypeName));
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

}