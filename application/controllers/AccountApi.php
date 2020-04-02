<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AccountApi extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('users');
    }

    public function login()
    {
        try {
            $request = (object) $_POST;
            $userData = $this->users->GetByEmail($request->EmailAddress);
            if (empty((array) $userData)) {
                echo $this->utilities->outputMessage("error", "user does not exist");
                return;
            }
            $isCorrectPassword = $this->users->ConfirmPassword($request->Password, $userData->Id);
            if (!$isCorrectPassword) {
                echo $this->utilities->outputMessage("error", "your password is incorrect");
                return;
            }
            $userSession = $this->utilities->PrepareUserSession($userData);
            $this->utilities->SetSession($userSession);
            echo $this->utilities->outputMessage("success", "Login Successful", base_url('Admin/dashboard'));
            return;
        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;
        }
        echo $this->utilities->outputMessage("error", "Your request cannot be processed at this moment. Please try again later");
        return;
    }
    public function ChangePassword()
    {
        try {
            $this->load->model('users');
            $request = (object) $_POST;
            $userId = $this->utilities->GetSessionId();
            $modelResponse = $this->users->ChangePassword($request->Password, $userId);
            if ($modelResponse > 0) {
                $userData = $this->users->Get($userId);
                $userSession = $this->utilities->PrepareUserSession($userData);
                $this->utilities->SetSession($userSession);
                echo $this->utilities->outputMessage("success", "Password changed successfully", base_url('admin/dashboard'));
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
    public function PasswordChange()
    {
        try {
            $this->load->model('users');
            $request = (object) $_POST;
            $userId = $this->utilities->GetSessionId();
            $isRightPassword = $this->users->ConfirmPassword($request->OldPassword, $userId);
            if ($isRightPassword) {
                $modelResponse = $this->users->ChangePassword($request->Password, $userId);
                if ($modelResponse > 0) {
                    $userData = $this->users->Get($userId);
                    $userSession = $this->utilities->PrepareUserSession($userData);
                    $this->utilities->SetSession($userSession);
                    echo $this->utilities->outputMessage("success", "Password changed successfully", base_url('admin/dashboard'));
                    return;
                }

            } else {
                echo $this->utilities->outputMessage("error", "your old password is not correct");
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

}

/* End of file AccountApi.php */
