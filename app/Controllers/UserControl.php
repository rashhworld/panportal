<?php

namespace App\Controllers;

class UserControl extends BaseController
{

    public function showUserLogin()
    {
        return view('_user/userLogin');
    }

    public function validateUserLogin()
    {
        if (!$this->validate(["uCred" => ["label" => "Email or Phone", "rules" => "trim|required|"], "uPass" => ["label" => "Password", "rules" => "trim|required|min_length[6]"]])) {

            $this->session->setFlashdata(['msg' => $this->validation->listErrors(), 'alert' => 'alert-danger']);
            return redirect()->back()->withInput();
        } else {
            $uCred = $this->request->getVar('uCred');
            $uPass = $this->request->getVar('uPass');
            if (strpos($uCred, '@')) {
                $data = $this->user->where('uEmail', $uCred)->first();
            } else {
                $data = $this->user->where('uPhone', $uCred)->first();
            }

            if ($data) {
                $verify_pass = password_verify($uPass, $data['uPass']);
                if ($verify_pass) {
                    $ses_data = ['uId' => $data['uId'], 'uName' => $data['uName'], 'uEmail' => $data['uEmail']];
                    $this->session->set($ses_data);
                    return redirect()->route('/');
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

    public function showUserRegister()
    {
        if ($this->request->getVar('r') != null) {
            if ($this->user->where('uReferralCode', $this->request->getVar('r'))->first()) {
                $data['referId'] = $this->request->getVar('r');
            } else {
                $this->session->setFlashdata(['msg' => 'Invalid Referral Code!', 'alert' => 'alert-danger']);
                return redirect()->to(base_url('register'));
            }
        } else {
            $data['referId'] = '0';
        }
        return view('_user/userRegister', $data);
    }

    public function validateUserRegister()
    {
        if (!$this->validate(["uName" => ["label" => "Name", "rules" => "required"], "uPhone" => ["label" => "Phone", "rules" => "required|numeric|min_length[10]|max_length[10]"], "uEmail" => ["label" => "Email", "rules" => "trim|required|valid_email"], "uPass" => ["label" => "Password", "rules" => "trim|required|min_length[6]"]])) {

            $this->session->setFlashdata(['msg' => $this->validation->listErrors(), 'alert' => 'alert-danger']);
            return redirect()->back()->withInput();
        } else {
            $uName = $this->request->getVar('uName');
            $uPhone = $this->request->getVar('uPhone');
            $uEmail = $this->request->getVar('uEmail');
            $uPass = $this->request->getVar('uPass');

            if ($this->user->where('uPhone', $uPhone)->first() || $this->user->where('uEmail', $uEmail)->first()) {
                $this->session->setFlashdata(['msg' => 'User already exists. Try another!', 'alert' => 'alert-danger']);
                return redirect()->back()->withInput();
            } else {
                if ($this->request->getVar('referId') == '0') {
                    $this->user->save(['uName' => $uName, 'uPhone' => $uPhone, 'uEmail' => $uEmail, 'uPass' => password_hash($uPass, PASSWORD_DEFAULT), 'uReferralCode' => rand(100000, 999999)]);
                } else {
                    $referId = $this->request->getVar('referId');
                    if ($userData = $this->user->where('uReferralCode', $referId)->first()) {
                        $this->user->save(['uName' => $uName, 'uPhone' => $uPhone, 'uEmail' => $uEmail, 'uPass' => password_hash($uPass, PASSWORD_DEFAULT), 'uReferralCode' => rand(100000, 999999), 'uReferedBy' => $userData['uId']]);
                    } else {
                        $this->session->setFlashdata(['msg' => 'Invalid Referral Code!', 'alert' => 'alert-danger']);
                        return redirect()->back()->withInput();
                    }
                }
                $this->session->setFlashdata(['msg' => 'Registration Successful. Please Login!', 'alert' => 'alert-success']);
                return redirect()->to(base_url('login'));
            }
        }
    }

    public function showUserForgotPass()
    {
        return view('_user/userForgotPass');
    }

    public function validateUserForgotPass()
    {
        if (!$this->validate(["uEmail" => ["label" => "Email", "rules" => "trim|required|valid_email"]])) {

            $this->session->setFlashdata(['msg' => $this->validation->listErrors(), 'alert' => 'alert-danger']);
            return redirect()->back()->withInput();
        } else {
            $uEmail = $this->request->getVar('uEmail');
            $uToken = rand(11111, 99999) . md5($uEmail);

            if ($userData = $this->user->where('uEmail', $uEmail)->first()) {
                $this->user->save(['uId' => $userData['uId'], 'uToken' => $uToken]);

                $subject = "Reset your Password";
                $message = "Hello, " . $userData['uName'] . "<br><br>
                We got a request to reset your password, if you did this then just click the below link to reset your password, if not just ignore this email.<br><br>
                
                <a href='" . base_url('UserControl/showUserResetPass') . "/" . $uToken . "'>click here to reset your password</a><br><br>
                Thank you :)";
                $this->sendmail($uEmail, $subject, $message);

                $this->session->setFlashdata(['msg' => 'Email with reset link has been sent to your Email. Please check your inbox or spam folder!', 'alert' => 'alert-success']);
                return redirect()->back()->withInput();
            } else {
                $this->session->setFlashdata(['msg' => 'No User Exist with this Email!', 'alert' => 'alert-danger']);
                return redirect()->back()->withInput();
            }
        }
    }

    public function showUserResetPass()
    {
        if ($userData = $this->user->where('uToken', $this->request->uri->getSegment(3))->first()) {
            $data['uId'] = $userData['uId'];
            return view('_user/userResetPass', $data);
        } else {
            $this->session->setFlashdata(['msg' => 'You have attempted an invalid link. Please try again!', 'alert' => 'alert-danger']);
            return redirect()->to(base_url('UserControl/showUserForgotPass'));
        }
    }

    public function validateUserResetPass()
    {
        if (!$this->validate(["uPass1" => ["label" => "Password", "rules" => "trim|required|min_length[6]"], "uPass2" => ["label" => "Confirm Password", "rules" => "trim|required|matches[uPass1]|min_length[6]"]])) {

            $this->session->setFlashdata(['msg' => $this->validation->listErrors(), 'alert' => 'alert-danger']);
            return redirect()->back()->withInput();
        } else {
            $uId = $this->request->getVar('uId');
            $uPass = $this->request->getVar('uPass2');
            $this->user->save(['uId' => $uId, 'uPass' => password_hash($uPass, PASSWORD_DEFAULT)]);

            $this->session->setFlashdata(['msg' => 'Password changed. Please Login!', 'alert' => 'alert-success']);
            return redirect()->to(base_url('login'));
        }
    }

    public function showUserDash()
    {
        $data = [
            'userData' => $this->user->where('uId', $this->session->get('uId'))->find(),
            'panData' => $this->pan->where('uId', $this->session->get('uId'))->orderBy("pDateTime", "desc")->find(),
            'referralCount' => $this->user->where('uReferedBy', $this->session->get('uId'))->countAllResults(),
            'referralData' => $this->user->where('uReferedBy', $this->session->get('uId'))->find(),
            'helpDeskData' => $this->help->where('uId', $this->session->get('uId'))->orderBy('hDate', 'desc')->find(),
        ];

        return view('_user/userDash', $data);
    }

    public function requestPan()
    {
        if (!$this->validate(["pName" => ["label" => "User's Name", "rules" => "required"], "pAdhar" => ["label" => "Aadhaar No", "rules" => "trim|required|min_length[12]|max_length[12]"]])) {
            $response = ['type' => 0, 'msg' => $this->validation->listErrors()];
        } else {
            $this->pan->save(['uId' => $this->session->get('uId'), 'pName' => $this->request->getVar('pName'), 'pAdhar' => $this->request->getVar('pAdhar')]);

            $userData = $this->user->where('uId', $this->session->get('uId'))->first();
            $currentAmt = $userData['uWallet'] - 20;
            $this->user->save(['uId' => $this->session->get('uId'), 'uWallet' => $currentAmt]);

            $this->session->setFlashdata(['msg' => 'Request Successful!<br> Please give us sometime to approve!', 'msgType' => 'success']);
            $response = ['type' => 1];
        }
        return $this->response->setJSON($response);
    }

    public function requestRation()
    {
        if (!$this->validate(["raName" => ["label" => "User's Name", "rules" => "required"], "raState" => ["label" => "User's State", "rules" => "required"], "raNumber" => ["label" => "Ration Number", "rules" => "required"]])) {
            $response = ['type' => 0, 'msg' => $this->validation->listErrors()];
        } else {
            $this->ration->save(['uId' => $this->session->get('uId'), 'raName' => $this->request->getVar('raName'), 'raState' => $this->request->getVar('raState'), 'raNumber' => $this->request->getVar('raNumber')]);

            $userData = $this->user->where('uId', $this->session->get('uId'))->first();
            $currentAmt = $userData['uWallet'] - 20;
            $this->user->save(['uId' => $this->session->get('uId'), 'uWallet' => $currentAmt]);

            $this->session->setFlashdata(['msg' => 'Request Successful!<br> Please give us sometime to approve!', 'msgType' => 'success']);
            $response = ['type' => 1];
        }
        return $this->response->setJSON($response);
    }

    public function showCaptcha()
    {
        $response = file_get_contents("https://digiapi.xyz/pan3/pan.php");
        return $this->response->setJSON($response);
    }

    public function fetchPanDetails()
    {
        $api_url = "https://digiapi.xyz";

        $response = file_get_contents($api_url . '/pan3/pan-api.php?pancard&_aadhar=' . $this->request->getVar('aadhar') . '&_pan=' . $this->request->getVar('pan') . '&_month=' . $this->request->getVar('month') . '&_year=' . $this->request->getVar('year') . '&_captcha=' . $this->request->getVar('captcha') . '&_jsession=' . $this->request->getVar('jssession') . '&_paam=' . $this->request->getVar('paam'));

        return $this->response->setJSON($response);
    }

    public function requestAyushman()
    {
        $stid = $this->request->getVar('s1');
        $flno = $this->request->getVar('p1');
        $mob = $this->request->getVar('p3');

        if ($mob == "R") {
            $type = "familyid";
        } else if ($mob == "S") {
            $type = "mob";
        } else if ($mob == "A") {
            $type = "pmj";
        }

        $response = file_get_contents('https://digiapi.xyz/api5/ap1.php?' . $type . '=' . $flno . '&stateid=' . $stid);
        return $this->response->setJSON($response);
    }

    public function ayushmanCardDl()
    {
        $userData = $this->user->where('uId', $this->session->get('uId'))->first();

        if ($userData['uWallet'] >= 5) {

            $name = $this->request->uri->getSegment(3);
            $familyid = $this->request->uri->getSegment(4);
            $id = $this->request->uri->getSegment(5);
            $stateid = $this->request->uri->getSegment(6);
            $file = $name . ".pdf";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://digiapi.xyz/api5/ap2.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('familyid' => $familyid, 'id' => $id, 'stateid' => $stateid),
            ));

            $response = curl_exec($curl);
            curl_close($curl);


            if ($response == 'NO RECORD FOUND' || $response == '' || $response == 'Temporarily unable to process your request, Please try again') {
                echo ("<script>alert('We found an issue with your state server, please retry after sometime!!!'); </script>");
                echo ("<script> window.location.href = '/'; </script>");
            } else {
                $currentAmt = $userData['uWallet'] - 5;
                $this->user->save(['uId' => $this->session->get('uId'), 'uWallet' => $currentAmt]);

                header("Content-type:application/pdf");
                header('Content-Disposition: attachment; filename="' . $file . '"');
                echo $response;
            }
        } else {
            echo ("<script>alert('Low Wallet balance! Please Recharge!'); </script>");
            echo ("<script> window.location.href = '/'; </script>");
        }
    }

    public function viewPanNo()
    {
        $panId = $this->request->getVar('panId');
        $uId = $this->session->get('uId');

        $userData = $this->user->where('uId', $uId)->first();
        $currentAmt = $userData['uWallet'] - 30;

        if ($userData['uWallet'] >= 30) {
            $this->pan->save(['pId' => $panId, 'pStatus' => '2']);
            $this->user->save(['uId' => $uId, 'uWallet' => $currentAmt]);

            if ($userData['uReferedBy'] != '0') {
                $referData = $this->user->where('uId', $userData['uReferedBy'])->first();
                $referAmt = $referData['uWallet'] + 5;

                $this->user->save(['uId' => $referData['uId'], 'uWallet' => $referAmt]);
            }
        } else {
            $this->session->setFlashdata(['msg' => 'Insufficient Balance! Wallet amount should be greater than Rs.30', 'msgType' => 'error']);
        }
    }

    public function requestPayment()
    {
        $checkSum     = "";
        $paramList     = array();

        $ORDER_ID           = $this->request->getVar('orderId');
        $CUST_ID             = $this->request->getVar('custId');
        $TXN_AMOUNT         = $this->request->getVar('orderAmt');
        $INDUSTRY_TYPE_ID     = 'Retail';
        $CHANNEL_ID         = 'WEB';

        $paramList["MID"]                 = PAYTM_MID;
        $paramList["ORDER_ID"]             = $ORDER_ID;
        $paramList["CUST_ID"]             = $CUST_ID;
        $paramList["INDUSTRY_TYPE_ID"]     = $INDUSTRY_TYPE_ID;
        $paramList["CHANNEL_ID"]         = $CHANNEL_ID;
        $paramList["TXN_AMOUNT"]         = $TXN_AMOUNT;
        $paramList["WEBSITE"]             = 'DEFAULT';
        $paramList["CALLBACK_URL"]         = base_url('UserControl/paymentUpdate');

        $data['checkSum'] = getChecksumFromArray($paramList, PAYTM_MERCHANT_KEY);
        $data['paramList'] = $paramList;

        return view('_user/pmtChk', $data);
    }

    public function paymentUpdate()
    {
        $paytmChecksum         = "";
        $paramList             = array();
        $isValidChecksum     = "FALSE";

        $paramList = $_POST;
        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

        $isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.

        if ($isValidChecksum == "TRUE") {

            if ($_POST["STATUS"] == "TXN_SUCCESS") {
                $amount = $_POST["TXNAMOUNT"];
                $str = explode("_", $_POST['ORDERID']);
                $userid = $str[1];

                $userData = $this->user->where('uId', $userid)->find();
                $finalAmt = $userData[0]['uWallet'] + $amount;

                $this->user->save(['uId' => $userid, 'uWallet' => $finalAmt]);

                // set_cookie('msg', 'Your Transaction is successfull. For security reasons you have been logged out, Please Login again to continue using our service!');
                // set_cookie('alert', 'alert-success');
                return redirect()->to(base_url('/'));
            } else {
                return redirect()->to(base_url('/'));
            }

            if (isset($_POST) && count($_POST) > 0) {
                foreach ($_POST as $paramName => $paramValue) {
                    echo "<br/>" . $paramName . " = " . $paramValue;
                }
            }
        } else {
            echo "<b>Checksum mismatched.</b>";
        }
    }

    public function changePass()
    {
        if (!$this->validate(["newPass" => ["label" => "Password", "rules" => "trim|required|min_length[6]"]])) {
            $response = ['type' => 0, 'msg' => $this->validation->listErrors()];
        } else {
            $this->user->save(['uId' => $this->session->get('uId'), 'uPass' => password_hash($this->request->getVar('newPass'), PASSWORD_DEFAULT)]);

            $response = ['type' => 1, 'msg' => 'Password updated successfully!'];
            $this->session->setFlashdata(['msg' => 'Password Changed Successful!', 'msgType' => 'success']);
        }

        return $this->response->setJSON($response);
    }

    public function helpDesk()
    {
        $userData = $this->user->where('uId', $this->session->get('uId'))->first();

        $this->help->save(['uId' => $userData['uId'], 'hName' => $userData['uName'], 'hMsg' => $this->request->getVar('hMsg')]);
        return redirect()->route('/');
    }

    public function signOut()
    {
        $this->session->remove(["uId", "uName", "uEmail"]);
        return redirect()->route('/');
    }

    public function sendMail($to, $subject, $message)
    {
        $this->email->setTo($to);
        $this->email->setFrom('no-reply@rashhworld.in', 'Pan Portal');
        $this->email->setSubject($subject);
        $this->email->setMessage($message);
        $this->email->send();
    }
}
