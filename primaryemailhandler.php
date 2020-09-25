<?php

require_once 'primaryemailhandler.civix.php';
use CRM_Primaryemailhandler_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function primaryemailhandler_civicrm_config(&$config) {
  _primaryemailhandler_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function primaryemailhandler_civicrm_xmlMenu(&$files) {
  _primaryemailhandler_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function primaryemailhandler_civicrm_install() {
  _primaryemailhandler_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function primaryemailhandler_civicrm_postInstall() {
  _primaryemailhandler_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function primaryemailhandler_civicrm_uninstall() {
  _primaryemailhandler_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function primaryemailhandler_civicrm_enable() {
  _primaryemailhandler_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function primaryemailhandler_civicrm_disable() {
  _primaryemailhandler_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function primaryemailhandler_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _primaryemailhandler_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function primaryemailhandler_civicrm_managed(&$entities) {
  _primaryemailhandler_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function primaryemailhandler_civicrm_caseTypes(&$caseTypes) {
  _primaryemailhandler_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function primaryemailhandler_civicrm_angularModules(&$angularModules) {
  _primaryemailhandler_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function primaryemailhandler_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _primaryemailhandler_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function primaryemailhandler_civicrm_entityTypes(&$entityTypes) {
  _primaryemailhandler_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function primaryemailhandler_civicrm_themes(&$themes) {
  _primaryemailhandler_civix_civicrm_themes($themes);
}

function primaryemailhandler_civicrm_pre($op, $objectName, $id, &$params) {
  if ($objectName == 'Email' && $op != 'delete') {
    $contactID = $params['contact_id'] ?? NULL;
    $primary = $params['is_primary'] ?? 0;

    //If the updated email is on hold, unhold it if the value is updated.
    if (!empty($params['id']) && empty($params['on_hold'])) {
      $existingEmailValues = civicrm_api3('Email', 'getsingle', [
        'id' => $params['id'],
      ]);
      if (!$primary && !empty($existingEmailValues['is_primary'])) {
        $primary = $existingEmailValues['is_primary'];
      }
      if (!$contactID && !empty($existingEmailValues['contact_id'])) {
        $contactID = $existingEmailValues['contact_id'];
      }
      if (!empty($existingEmailValues['on_hold']) && !empty($existingEmailValues['email'])
        && !empty($params['email']) && $existingEmailValues['email'] != $params['email']) {

        $params['on_hold'] = 0;
      }
    }


    if (!empty($contactID)) {
      //If the email is being set on hold, Get Non primary Valid emails and make
      //the latest added value as primary
      if (!empty($params['on_hold']) && $primary) {
        $contactEmails = civicrm_api3('Email', 'get', [
          'sequential' => 1,
          'contact_id' => $contactID,
          'on_hold' => ['NOT IN' => [1, 2]],
          'is_primary' => 0,
          'options' => ['sort' => "id DESC"],
        ]);
        if (!empty($contactEmails['count']) && !empty($contactEmails['values'][0]['id'])) {
          $params['is_primary'] = 0;
          civicrm_api3('Email', 'create', [
            'id' => $contactEmails['values'][0]['id'],
            'is_primary' => 1,
          ]);
        }
      }


      //Primary Email is on hold. Make the new value as primary
      //if it is different from the onhold primary email address.
      $primaryEmail = civicrm_api3('Email', 'get', [
        'sequential' => 1,
        'contact_id' => $contactID,
        'is_primary' => 1,
        'on_hold' => ['IN' => [1, 2]],
      ]);
      if (!empty($primaryEmail['count']) && empty($params['is_primary'])) {
        $email = $primaryEmail['values'][0];
        if (!empty($email['email']) && $email['email'] != $params['email']) {
          $params['is_primary'] = 1;

          //In some cases, the existing on hold email does not update its primary flag to 0
          //which ends up in 2 primary emails for a single conact.
          //Remove it manually.
          // civicrm_api3('Email', 'create', [
          //   'id' => $email['id'],
          //   'is_primary' => 0,
          // ]);
          if (!empty($email['id'])) {
            $updateParams = [1 => [$email['id'], 'Integer']];
            CRM_Core_DAO::singleValueQuery("UPDATE civicrm_email SET is_primary = 0 WHERE id = %1", $updateParams);
          }
        }
      }
    }
  }
}


// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function primaryemailhandler_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function primaryemailhandler_civicrm_navigationMenu(&$menu) {
  _primaryemailhandler_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _primaryemailhandler_civix_navigationMenu($menu);
} // */
