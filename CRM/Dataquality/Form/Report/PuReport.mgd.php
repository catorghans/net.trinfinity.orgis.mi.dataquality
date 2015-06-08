<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Dataquality_Form_Report_PuReport',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'PuReport',
      'description' => 'PuReport (net.trinfinity.orgis.mi.dataquality)',
      'class_name' => 'CRM_Dataquality_Form_Report_PuReport',
      'report_url' => 'net.trinfinity.orgis.mi.dataquality/pureport',
      'component' => '',
    ),
  ),
);