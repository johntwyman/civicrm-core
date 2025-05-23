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
 * Test for CRM_Financial_Page_Ajax class.
 * @group headless
 */
class CRM_Financial_Page_AjaxTest extends CiviUnitTestCase {

  public function tearDown(): void {
    $this->quickCleanUpFinancialEntities();
    $this->quickCleanUp([
      'civicrm_entity_batch',
      'civicrm_batch',
    ]);
    parent::tearDown();
  }

  /**
   * Test the ajax function to get financial transactions.
   *
   * Test focus is on ensuring changes to how labels are retrieved does not cause regression.
   */
  public function testGetFinancialTransactionsList(): void {
    $individualID = $this->individualCreate();
    $this->contributionCreate(['contact_id' => $individualID, 'trxn_id' => 12345]);
    $batch = $this->callAPISuccess('Batch', 'create', ['title' => 'test', 'status_id' => 'Open']);
    CRM_Core_DAO::executeQuery("
     INSERT INTO civicrm_entity_batch (entity_table, entity_id, batch_id)
     values('civicrm_financial_trxn', 1, 1)
   ");
    $_REQUEST['sEcho'] = 1;
    $_REQUEST['entityID'] = $batch['id'];
    $_REQUEST['return'] = TRUE;
    $json = CRM_Financial_Page_AJAX::getFinancialTransactionsList();
    $json = str_replace(rtrim(CIVICRM_UF_BASEURL, '/'), 'http://FIX ME', $json);
    $this->assertEquals('{"sEcho": 1, "iTotalRecords": 1, "iTotalDisplayRecords": 1, "aaData": [ ["","<a href=\"/index.php?q=civicrm/contact/view&amp;reset=1&amp;cid=3\" data-tooltip-url=\"/index.php?q=civicrm/profile/view&amp;reset=1&amp;gid=7&amp;id=3&amp;snippet=4&amp;is_show_email_task=1\" class=\"crm-summary-link\" aria-labelledby=\"crm-contactname-content\">'
    . '<i class=\"crm-i fa-fw fa-user\" title=\"\"></i></a>","<a href=\"/index.php?q=civicrm/contact/view&amp;reset=1&amp;cid=3\">Anderson, Anthony II</a>","$100.00","12345","' . CRM_Utils_Date::customFormat(date('Ymd')) . ' 12:00 AM","' . CRM_Utils_Date::customFormat(date('Ymd')) . ' 12:00 AM",'
    . '"Credit Card","Completed","Donation","<span><a href=\"/index.php?q=civicrm/contact/view/contribution&amp;reset=1&amp;id=1&amp;cid=3&amp;action=view&amp;context=contribution&amp;'
    . 'selectedChild=contribute\" class=\"action-item crm-hover-button\" title=\'View Contribution\' >View</a></span>"]] }', $json);
  }

  /**
   * Test getting open batch.
   */
  public function testGetFinancialTransactionsListOpenBatch(): void {
    $individualID = $this->individualCreate();
    $this->contributionCreate(['contact_id' => $individualID, 'trxn_id' => 12345]);
    $batch = $this->callAPISuccess('Batch', 'create', ['title' => 'test', 'status_id' => 'Open']);
    CRM_Core_DAO::executeQuery("
     INSERT INTO civicrm_entity_batch (entity_table, entity_id, batch_id)
     values('civicrm_financial_trxn', 1, 1)
   ");
    $_REQUEST['sEcho'] = 1;
    $_REQUEST['entityID'] = $batch['id'];
    $_REQUEST['statusID'] = 1;
    $_REQUEST['notPresent'] = 1;
    $_REQUEST['return'] = TRUE;
    $json = CRM_Financial_Page_AJAX::getFinancialTransactionsList();
    $json = str_replace(rtrim(CIVICRM_UF_BASEURL, '/'), 'http://FIX ME', $json);
    $this->assertEquals('{"sEcho": 1, "iTotalRecords": 1, "iTotalDisplayRecords": 1, "aaData": [ ["<input type=\'checkbox\' id=\'mark_x_2\' name=\'mark_x_2\' value=\'1\' onclick=enableActions(\'x\')></input>","<a href=\"/index.php?q=civicrm/contact/view&amp;reset=1&amp;cid=3\" data-tooltip-url=\"/index.php?q=civicrm/profile/view&amp;reset=1&amp;gid=7&amp;id=3&amp;snippet=4&amp;is_show_email_task=1\" class=\"crm-summary-link\" aria-labelledby=\"crm-contactname-content\">'
    . '<i class=\"crm-i fa-fw fa-user\" title=\"\"></i></a>","<a href=\"/index.php?q=civicrm/contact/view&amp;reset=1&amp;cid=3\">Anderson, Anthony II</a>","$5.00","12345","' . CRM_Utils_Date::customFormat(date('Ymd')) . ' 12:00 AM","' . CRM_Utils_Date::customFormat(date('Ymd')) . ' 12:00 AM",'
    . '"Credit Card","Completed","Donation","<span><a href=\"/index.php?q=civicrm/contact/view/contribution&amp;reset=1&amp;id=1&amp;cid=3&amp;action=view&amp;context=contribution&amp;'
    . 'selectedChild=contribute\" class=\"action-item crm-hover-button\" title=\'View Contribution\' >View</a><a href=\"#\" class=\"action-item crm-hover-button disable-action\" title=\'Assign Transaction\' onclick = \"assignRemove( 2,\'assign\' );\">Assign</a></span>"]] }', $json);
  }

}
