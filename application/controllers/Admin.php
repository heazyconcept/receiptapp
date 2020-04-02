<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->ConfirmSession();

    }
    public function index()
    {

        redirect('admin/dashboard');

    }
    public function dashboard()
    {
        $passwordStatus = $this->utilities->GetPasswordStatus();
        if ($passwordStatus == 0) {
            redirect('account/changepassword');
        }
        $this->load->model('statistics');
        $target = array();
        $this->utilities->SetPageTitle('Admin - Dashboard');
        $sessionRole = $this->utilities->GetSessionRole();
        if ($sessionRole == 'Agent') {
            $target = array("CreatedBy" => $this->utilities->GetSessionId());
        }

        $data['clientCount'] = $this->statistics->GetTotalClients($target);
        $data['receiptCount'] = $this->statistics->GetTotalReceipt($target);
        $data['pendingReceiptCount'] = $this->statistics->GetPendingReceipt($target);
        $this->load->view('Admin/Partials/admin-dashboard', $data);

    }
    public function clients()
    {
        $this->utilities->SetPageTitle('Admin - Clients');
        $this->load->view('Admin/Partials/clients');
    }
    public function users()
    {
        $this->utilities->SetPageTitle('Admin - Users');
        $this->load->view('Admin/Partials/users');
    }
    public function departments()
    {
        $this->load->model("departments");
        $data["allDepartments"] = $this->departments->ListAll();
        $this->utilities->SetPageTitle('Admin - Department');
        $this->load->view('Admin/Partials/department', $data);
    }
    public function receipts()
    {
        $this->load->model("receiptCategories");
        $this->load->model("departments");
        $this->load->model("receiptTags");
        $data["receiptCategories"] = $this->receiptCategories->ListAll();
        $data["departments"] = $this->departments->ListAll();
        $data["receiptTags"] = $this->receiptTags->ListAll();
        $this->utilities->SetPageTitle('Admin - Receipts');
        $this->load->view('Admin/Partials/receipts', $data);
    }
    public function report()
    {
        $this->load->model("receiptCategories");
        $this->load->model("departments");
        $data["receiptCategories"] = $this->receiptCategories->ListAll();
        $data["departments"] = $this->departments->ListAll();
        $this->utilities->SetPageTitle('Admin - Reports');
        $this->load->view('Admin/Partials/report', $data);
    }
    public function newuser()
    {
        $this->utilities->SetPageTitle('Admin - New User');
        $this->load->view('Admin/Partials/newuser');

    }
    public function viewreceipt($receiptId)
    {
       
        $this->load->model('receipts');
        $this->load->model('clients');
        $this->load->library('emailservices');
        $this->utilities->SetPageTitle('Admin - View Receipt');
        $receiptData = $data['receiptData'] = $this->receipts->Get($receiptId);
        $data['clientData'] = $this->clients->Get($receiptData->ClientId);
        $this->emailservices->processReceiptHtml($data['clientData'], $receiptData);
        $this->load->view('Admin/Partials/viewreceipt', $data);

    }

    private function ConfirmSession()
    {
        $userId = $this->utilities->GetSessionId();
        if (empty($userId)) {
            redirect('Account/login', 'refresh');
        }

    }

}

/* End of file Admin.php */
