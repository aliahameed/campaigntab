<?php

require_once 'CRM/Core/Page.php';

class CRM_Campaignstab_Page_PCList extends CRM_Core_Page {


  /**
   * The action links that we need to display for the browse screen.
   *
   * @var array
   */
  static $_links = NULL;

  /**
   * Get BAO Name.
   *
   * @return string
   *   Classname of BAO.
   */
  public function getBAOName() {
    return 'CRM_PCP_BAO_PCP';
  }

  /**
   * Get action Links.
   *
   * @return array
   *   (reference) of action links
   */
  public function &links() {
    if (!(self::$_links)) {
      // helper variable for nicer formatting
      $deleteExtra = ts('Are you sure you want to delete this Campaign Page ?');

      self::$_links = array(
        CRM_Core_Action::UPDATE => array(
          'name' => ts('Edit'),
          'url' => 'civicrm/pcp/info',
          'qs' => 'action=update&reset=1&id=%%id%%&context=dashboard',
          'title' => ts('Edit Personal Campaign Page'),
        ),
        CRM_Core_Action::RENEW => array(
          'name' => ts('Approve'),
          'url' => 'civicrm/admin/pcp',
          'qs' => 'action=renew&id=%%id%%',
          'title' => ts('Approve Personal Campaign Page'),
        ),
        CRM_Core_Action::REVERT => array(
          'name' => ts('Reject'),
          'url' => 'civicrm/admin/pcp',
          'qs' => 'action=revert&id=%%id%%',
          'title' => ts('Reject Personal Campaign Page'),
        ),
        CRM_Core_Action::DELETE => array(
          'name' => ts('Delete'),
          'url' => 'civicrm/admin/pcp',
          'qs' => 'action=delete&id=%%id%%',
          'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
          'title' => ts('Delete Personal Campaign Page'),
        ),
        CRM_Core_Action::ENABLE => array(
          'name' => ts('Enable'),
          'url' => 'civicrm/admin/pcp',
          'qs' => 'action=enable&id=%%id%%',
          'title' => ts('Enable'),
        ),
        CRM_Core_Action::DISABLE => array(
          'name' => ts('Disable'),
          'url' => 'civicrm/admin/pcp',
          'qs' => 'action=disable&id=%%id%%',
          'title' => ts('Disable'),
        ),
      );
    }
    return self::$_links;
  }


  public function run() {
    CRM_Core_Error::debug_log_message('pclist.php');

    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('PCList'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));

    $this->browse();

    parent::run();
  }


  /**
   * Browse all custom data groups.
   *
   *
   * @param null $action
   *
   * @return void
   */
  public function browse($action = NULL) {

    CRM_Core_Resources::singleton()->addStyleFile('civicrm', 'css/searchForm.css', 1, 'html-header');

    $this->_sortByCharacter = CRM_Utils_Request::retrieve('sortByCharacter',
      'String',
      $this
    );
    if ($this->_sortByCharacter == 1 ||
      !empty($_POST)
    ) {
      $this->_sortByCharacter = '';
    }

    $status = CRM_PCP_BAO_PCP::buildOptions('status_id', 'create');

    $pcpSummary = $params = array();
    $whereClause = NULL;

    if (!empty($_POST) || !empty($_GET['page_type'])) {
      if (!empty($_POST['status_id'])) {
        $whereClause = ' AND cp.status_id = %1';
        $params['1'] = array($_POST['status_id'], 'Integer');
      }

      if (!empty($_POST['page_type'])) {
        $whereClause .= ' AND cp.page_type = %2';
        $params['2'] = array($_POST['page_type'], 'String');
      }
      elseif (!empty($_GET['page_type'])) {
        $whereClause .= ' AND cp.page_type = %2';
        $params['2'] = array($_GET['page_type'], 'String');
      }

      if (!empty($_POST['page_id'])) {
        $whereClause .= ' AND cp.page_id = %4 AND cp.page_type = "contribute"';
        $params['4'] = array($_POST['page_id'], 'Integer');
      }

      if (!empty($_POST['event_id'])) {
        $whereClause .= ' AND cp.page_id = %5 AND cp.page_type = "event"';
        $params['5'] = array($_POST['event_id'], 'Integer');
      }

      if ($whereClause) {
        $this->set('whereClause', $whereClause);
        $this->set('params', $params);
      }
      else {
        $this->set('whereClause', NULL);
        $this->set('params', NULL);
      }
    }

    $approvedId = CRM_Core_OptionGroup::getValue('pcp_status', 'Approved', 'name');

    //check for delete CRM-4418
    $allowToDelete = CRM_Core_Permission::check('delete in CiviContribute');

    // get all contribution pages
    $query = "SELECT id, title, start_date, end_date FROM civicrm_contribution_page WHERE (1)";
    $cpages = CRM_Core_DAO::executeQuery($query);
    while ($cpages->fetch()) {
      $pages['contribute'][$cpages->id]['id'] = $cpages->id;
      $pages['contribute'][$cpages->id]['title'] = $cpages->title;
      $pages['contribute'][$cpages->id]['start_date'] = $cpages->start_date;
      $pages['contribute'][$cpages->id]['end_date'] = $cpages->end_date;
    }

    // get all event pages. pcp campaign start and end dates for event related pcp's use the online registration start and end dates,
    $query = "SELECT id, title, start_date, end_date, registration_start_date, registration_end_date
                  FROM civicrm_event
                  WHERE is_template IS NULL OR is_template != 1";
    $epages = CRM_Core_DAO::executeQuery($query);
    while ($epages->fetch()) {
      $pages['event'][$epages->id]['id'] = $epages->id;
      $pages['event'][$epages->id]['title'] = $epages->title;
      $pages['event'][$epages->id]['start_date'] = $epages->registration_start_date;
      $pages['event'][$epages->id]['end_date'] = $epages->registration_end_date;
    }

    $params = $this->get('params') ? $this->get('params') : array();

    $query = "
        SELECT count(cs.id) as count, cp.id, cp.contact_id , cp.status_id, cp.title, cp.is_active, cp.page_type, cp.page_id, cp.goal_amount, COALESCE(SUM(cc.total_amount),0) as amount_raised 
        FROM civicrm_pcp cp LEFT JOIN civicrm_contribution_soft cs ON ( cp.id = cs.pcp_id ) LEFT JOIN civicrm_contribution cc ON ( cs.contribution_id = cc.id)
        WHERE cp.contact_id = " . CRM_Utils_Request::retrieve('cid', 'Positive') . $this->get('whereClause') . " GROUP BY cp.id ORDER BY cp.status_id";

    $pcp = CRM_Core_DAO::executeQuery($query, $params);

    while ($pcp->fetch()) {

      $action = array_sum(array_keys($this->links()));
      $contact = CRM_Contact_BAO_Contact::getDisplayAndImage($pcp->contact_id);

      $class = '';

      if ($pcp->status_id != $approvedId || $pcp->is_active != 1) {
        $class = 'disabled';
      }

      switch ($pcp->status_id) {
        case 2:
          $action -= CRM_Core_Action::RENEW;
          break;

        case 3:
          $action -= CRM_Core_Action::REVERT;
          break;
      }

      switch ($pcp->is_active) {
        case 1:
          $action -= CRM_Core_Action::ENABLE;
          break;

        case 0:
          $action -= CRM_Core_Action::DISABLE;
          break;
      }

      if (!$allowToDelete) {
        $action -= CRM_Core_Action::DELETE;
      }

      $page_type = $pcp->page_type;
      $page_id = (int) $pcp->page_id;
      if ($pages[$page_type][$page_id]['title'] == '' || $pages[$page_type][$page_id]['title'] == NULL) {
        $title = '(no title found for ' . $page_type . ' id ' . $page_id . ')';
      }
      else {
        $title = $pages[$page_type][$page_id]['title'];
      }

      if ($pcp->page_type == 'contribute') {
        $pageUrl = CRM_Utils_System::url('civicrm/' . $page_type . '/transact', 'reset=1&id=' . $pcp->page_id);
      }
      else {
        $pageUrl = CRM_Utils_System::url('civicrm/' . $page_type . '/register', 'reset=1&id=' . $pcp->page_id);
      }

      $pcpSummary[$pcp->id] = array(
        'id' => $pcp->id,
        'start_date' => $pages[$page_type][$page_id]['start_date'],
        'end_date' => $pages[$page_type][$page_id]['end_date'],
        'supporter' => $contact['0'],
        'supporter_id' => $pcp->contact_id,
        'status_id' => $status[$pcp->status_id],
        'page_id' => $page_id,
        'page_title' => $title,
        'page_url' => $pageUrl,
        'page_type' => $page_type,
        'action' => CRM_Core_Action::formLink(self::links(), $action,
          array('id' => $pcp->id), ts('more'), FALSE, 'contributionpage.pcp.list', 'PCP', $pcp->id
        ),
        'title' => $pcp->title,
        'class' => $class,
        'goal_amount' => $pcp->goal_amount,
        'amount_raised' => $pcp->amount_raised,
        'count' => $pcp->count,
      );

    } // while

    $this->search();

    $this->assign('rows', $pcpSummary);

    // Let template know if user has run a search or not
    if ($this->get('whereClause')) {
      $this->assign('isSearch', 1);
    }
    else {
      $this->assign('isSearch', 0);
    }
  }

  public function search() {

    if ($this->_action & CRM_Core_Action::DELETE) {
      return;
    }

    $form = new CRM_Core_Controller_Simple('CRM_PCP_Form_PCP', ts('Search Campaign Pages'), CRM_Core_Action::ADD);
    $form->setEmbedded(TRUE);
    $form->setParent($this);
    $form->process();
    $form->run();
  }

  /**
   * Get name of edit form.
   *
   * @return string
   *   Classname of edit form.
   */
  public function editForm() {
    return 'CRM_PCP_Form_PCP';
  }

  /**
   * Get edit form name.
   *
   * @return string
   *   name of this page.
   */
  public function editName() {
    return ts('Personal Campaign Page');
  }

  /**
   * Get user context.
   *
   * @param null $mode
   *
   * @return string
   *   user context.
   */
  public function userContext($mode = NULL) {
    return 'civicrm/admin/pcp';
  }

}
