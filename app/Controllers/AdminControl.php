<?php

namespace App\Controllers;

class AdminControl extends BaseController
{

    public function showAdminAuth()
    {
        return view('_admin/adminAuth');
    }

    public function validateAdminAuth()
    {
        if (!$this->validate(["aEmail" => ["label" => "Email", "rules" => "trim|required|valid_email"], "aPass" => ["label" => "Password", "rules" => "trim|required"]])) {

            $this->session->setFlashdata(['msg' => $this->validation->listErrors(), 'alert' => 'alert-danger']);
            return redirect()->back()->withInput();
        } else {
            $aEmail = $this->request->getVar('aEmail');
            $aPass = $this->request->getVar('aPass');
            $data = $this->admin->where('aEmail', $aEmail)->first();
            if ($data) {
                $verify_pass = password_verify($aPass, $data['aPass']);
                if ($verify_pass) {
                    $ses_data = ['aEmail' => $data['aEmail']];
                    $this->session->set($ses_data);
                    return redirect()->route('admin');
                } else {
                    $this->session->setFlashdata(['msg' => 'Wrong Password Entered!', 'alert' => 'alert-danger']);
                    return redirect()->back()->withInput();
                }
            } else {
                $this->session->setFlashdata(['msg' => 'No User Exist with this Email!', 'alert' => 'alert-danger']);
                return redirect()->back()->withInput();
            }
        }
    }

    public function showAdminDash()
    {
        $data = ['panData' => $this->pan->where('pStatus', '0')->find(), 'totalPending' => $this->pan->where('pStatus', '0')->countAllResults(), 'totalApproved' => $this->pan->where('pStatus', '1')->countAllResults(), 'totalRejected' => $this->pan->where('pStatus', '3')->countAllResults()];

        return view('_admin/adminDash', $data);
    }

    public function panUpdate()
    {
        if (!$this->validate(["panNo" => ["label" => "PAN No", "rules" => "trim|required|alpha_numeric|min_length[10]|max_length[10]"]])) {
            $response = ['type' => 0, 'msg' => $this->validation->listErrors()];
        } else {
            $this->pan->save(['pId' => $this->request->getVar('userId'), 'pPAN' => $this->request->getVar('panNo'), 'pStatus' => '1']);

            $this->session->setFlashdata('msg', 'Request Approved!');
            $response = ['type' => 1];
        }
        return $this->response->setJSON($response);
    }

    public function rejectUser()
    {
        $panId = $this->request->getVar('panId');

        $panData = $this->pan->where('pId', $panId)->find();
        $uId = $panData[0]['uId'];

        $userData = $this->user->where('uId', $uId)->find();
        $currentAmt = $userData[0]['uWallet'] + 15;

        $this->pan->save(['pId' => $panId, 'pStatus' => '3']);
        $this->user->save(['uId' => $uId, 'uWallet' => $currentAmt]);
    }
}