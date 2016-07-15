<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Postcodenl_UpdaterTask {

  public static function UpdateFromQueue(CRM_Queue_TaskContext $ctx, $serializedParams, $offset, $count) {
    $params = unserialize($serializedParams);
    list($contacts, $_) = CRM_Contact_BAO_Query::apiQuery($params, array('contact_id'), NULL, NULL, $offset, $count, TRUE, FALSE, FALSE);
    $contact_ids = array();
    foreach($contacts as $contact) {
      $contact_ids[] = $contact['contact_id'];
    }

    $dao = CRM_Core_DAO::executeQuery("SELECT * FROM `civicrm_address` WHERE `contact_id` IN (".implode(", ", $contact_ids).")", array(), true, 'CRM_Core_DAO_Address');
    while ($dao->fetch()) {
      $params = array();
      CRM_Core_DAO::storeValues($dao, $params);
      CRM_Postcodenl_Updater::checkAddress($dao->id, $params, false);
    }

    return true;
  }
  
}