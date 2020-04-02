<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account extends CI_Controller
{

    public function index()
    {

        redirect('Account/login', 'refresh');

    }
    // public function verify()
    // {
    //     print_r($_REQUEST);
    // }
    public function login()
    {
        $this->utilities->SetPageTitle("Login - ");
        $this->load->view('Account/Partials/login');
    }
    public function register()
    {
        try {
            $this->load->model('users');
            $request = (object) $_POST;
            $IsExist = $this->users->CheckExist($request->EmailAddress, $request->PhoneNumber);
            if ($IsExist) {
                echo $this->utilities->outputMessage("error", "user already exists");
                return;
            }
            $modelResponse = $this->users->Insert($_POST);
            if ($modelResponse > 0) {
                echo $this->utilities->outputMessage("success", "user created Successfully");
                return;
            }
        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;
        }
        echo $this->utilities->outputMessage("error", "Your request cannot be processed at this moment. Please try again later");
        return;

    }
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('');
    }
    public function changepassword()
    {
        $this->ConfirmSession();
        $this->utilities->SetPageTitle("Change your password - ");
        $this->load->view('Account/Partials/forgot-password');

    }
     public function changemypassword()
    {
        $this->ConfirmSession();
        $this->utilities->SetPageTitle("Change your password - ");
        $this->load->view('Account/Partials/change-password');

    }
    private function ConfirmSession()
    {
        $userId = $this->utilities->GetSessionId();
        if (empty($userId)) {
            redirect('Account/login', 'refresh');
        }

    }

}

/* End of file Account.php */
