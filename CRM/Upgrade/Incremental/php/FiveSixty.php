<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 * Upgrade logic for the 5.60.x series.
 *
 * Each minor version in the series is handled by either a `5.60.x.mysql.tpl` file,
 * or a function in this class named `upgrade_5_60_x`.
 * If only a .tpl file exists for a version, it will be run automatically.
 * If the function exists, it must explicitly add the 'runSql' task if there is a corresponding .mysql.tpl.
 *
 * This class may also implement `setPreUpgradeMessage()` and `setPostUpgradeMessage()` functions.
 */
class CRM_Upgrade_Incremental_php_FiveSixty extends CRM_Upgrade_Incremental_Base {

  /**
   * Upgrade step; adds tasks including 'runSql'.
   *
   * @param string $rev
   *   The version number matching this function name
   */
  public function upgrade_5_60_alpha1($rev): void {
    $this->addTask(ts('Upgrade DB to %1: SQL', [1 => $rev]), 'runSql', $rev);
    $this->addTask('Add scheduled_reminder_smarty setting', 'addScheduledReminderSmartySetting');
  }

  public function setPostUpgradeMessage(&$postUpgradeMessage, $rev): void {
    if ($rev === '5.60.alpha1') {
      $postUpgradeMessage .= '<p>' . ts('You can now choose whether to use Smarty in Scheduled Reminders at <em>Administer >> CiviMail >> CiviMail Component Settings</em>. The setting is disabled by default on new installations but we have enabled it during this upgrade to preserve the existing behavior. More information <a %1>in this lab ticket</a>.', [1 => 'href="https://lab.civicrm.org/dev/core/-/issues/4100" target="_blank"']) . '<p>';
    }
  }

  public static function addScheduledReminderSmartySetting(): bool {
    Civi::settings()->set('scheduled_reminder_smarty', TRUE);
    return TRUE;
  }

}