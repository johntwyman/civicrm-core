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
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */
class CRM_Utils_Check_Component_Mailing extends CRM_Utils_Check_Component {

  /**
   * @return CRM_Utils_Check_Message[]
   */
  public function checkUnsubscribeMethods() {
    $messages = [];

    $methods = Civi::settings()->get('civimail_unsubscribe_methods');

    if (\CRM_Core_Component::isEnabled('CiviMail') && !in_array('oneclick', $methods)) {
      $p = fn($text) => "<p>$text</p>";

      $message = new CRM_Utils_Check_Message(
        __FUNCTION__,
        $p(ts('Beginning in February 2024, major email hosts will require that large mailing-lists support another unsubscribe method: "HTTP One-Click" (RFC 8058).'))
        . $p(ts('CiviCRM recommends but does not require enabling "HTTP One-Click". The need to enable will depend on your specific mailing-lists. Some email customizations may require updates for compatibility.')),
        ts('CiviMail: Enable One-Click Unsubscribe'),
        \Psr\Log\LogLevel::NOTICE,
        'fa-server'
      );
      $message->addAction(ts('Learn more'), FALSE, 'href', ['url' => 'https://lab.civicrm.org/dev/core/-/issues/4641'], 'fa-info-circle');
      $message->addAction(ts('Update settings'), FALSE, 'href', ['path' => 'civicrm/admin/mail', 'query' => 'reset=1'], 'fa-wrench');

      $messages[] = $message;
    }

    return $messages;
  }

}
