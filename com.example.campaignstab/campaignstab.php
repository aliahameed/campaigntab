<?php

require_once 'campaignstab.civix.php';

/*
 * Implements hook_civicrm_tabset().
 *
 */
function campaignstab_civicrm_tabset($tabsetName, &$tabs, $context) {
  // check if the tab set is Contact View
  if ($tabsetName == 'civicrm/contact/view') {
    $contactId = $context['contact_id'];
    // let's add a new "contribution" tab with a different name and put it last
    // this is just a demo, in the real world, you would create a url which would
    // return an html snippet etc.
    $url = CRM_Utils_System::url( 'civicrm/pclist',
                                  "cid=$contactId" );
    // $url should return in 4.4 and prior an HTML snippet e.g. '<div><p>....';
    // in 4.5 and higher this needs to be encoded in json. E.g. json_encode(array('content' => <html form snippet as previously provided>));
    // or CRM_Core_Page_AJAX::returnJsonResponse($content) where $content is the html code
    // in the first cases you need to echo the return and then exit, if you use CRM_Core_Page method you do not need to worry about this.
    $tabs[] = array( 'id'    => 'mySupercoolTab',
      'url'   => $url,
      'title' => 'Personal Campaign Pages',
      'weight' => 300,
    );
  }
}

/**
* Implements CiviCRM 'pageRun' hook.
*
* @param CRM_Core_Page $page Current page.
*/
function campaignstab_civicrm_pageRun(&$page) {
  // Manage PCP pages
  if($page instanceof CRM_PCP_Page_PCP) {
    CRM_Core_Error::debug_log_message('instance of');
  }


  // if($page instanceof CRM_PCP_Page_PCP) {
  //  $worker = RelationshipContributionACLWorker::getInstance();
  //  $worker->manageContributionsPageRunHook($page);
  //}

  //else {
  //  $worker = RelationshipContributionACLWorker::getInstance();
  //  $worker->contributionPageRunHook($page);
  //} 
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function campaignstab_civicrm_config(&$config) {
  _campaignstab_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function campaignstab_civicrm_xmlMenu(&$files) {
  _campaignstab_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function campaignstab_civicrm_install() {
  _campaignstab_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function campaignstab_civicrm_uninstall() {
  _campaignstab_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function campaignstab_civicrm_enable() {
  _campaignstab_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function campaignstab_civicrm_disable() {
  _campaignstab_civix_civicrm_disable();
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
function campaignstab_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _campaignstab_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function campaignstab_civicrm_managed(&$entities) {
  _campaignstab_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function campaignstab_civicrm_caseTypes(&$caseTypes) {
  _campaignstab_civix_civicrm_caseTypes($caseTypes);
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
function campaignstab_civicrm_angularModules(&$angularModules) {
_campaignstab_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function campaignstab_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _campaignstab_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function campaignstab_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function campaignstab_civicrm_navigationMenu(&$menu) {
  _campaignstab_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'com.example.campaignstab')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _campaignstab_civix_navigationMenu($menu);
} // */
