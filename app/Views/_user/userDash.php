<?php
if (!session()->get('uEmail')) {
  echo "<script> window.location.href = '" . base_url('login') . "'; </script>";
} else {
?>
  <!doctype html>
  <html>

  <head>
    <title><?= session()->get('uName') ?>'s Dashboard | Pan Portal</title>
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" name="viewport" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') . "?v=" . time() ?>">

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.6/dist/sweetalert2.all.min.js"></script>
  </head>

  <body class="container panRequest">
    <?php if (session()->getFlashdata('msg')) { ?>
      <script type="text/javascript">
        Swal.fire("Alert!", "<?= session()->getFlashdata('msg') ?>", "<?= session()->getFlashdata('msgType') ?>");
      </script>
    <?php } ?>

    <div class="d-flex flex-column mb-5">
      <h2 class="text-center mt-4 mb-4">Pan Portal</h2>
      <div class="d-flex  justify-content-between">
        <div>
          <button class="btn btn-outline-secondary">Wallet Amount : <?= $userData[0]['uWallet'] ?></button>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addWallet">Add Wallet</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#referral">Refer & Earn</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#requestPan">Request Pan</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#requestNSDLPan" onclick="showCaptcha()">Request NSDL Pan</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#requestAyushman">Request Ayushman</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#rationFind">Ration Find</button>
        </div>
        <div>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePass">Change Password</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#helpDesk">Help Desk</button>
          <a href="<?= base_url('UserControl/signOut') ?>"><button type="button" class="btn btn-outline-secondary">Log Out</button></a>
        </div>
      </div>
    </div>
    <table id="mytable">
      <thead>
        <tr>
          <th>Sl. No.</th>
          <th scope="col">Name</th>
          <th scope="col">Aadhaar No</th>
          <th scope="col">Date & Time</th>
          <th scope="col">Pan Details</th>
        </tr>
      </thead>
      <tbody>
        <?php $c = 1;
        foreach ($panData as $pd) { ?>
          <tr>
            <td data-label="Sl. No."><?= $c++ ?></td>
            <td data-label="Name"><?= $pd['pName'] ?></td>
            <td data-label="Aadhaar No"><?= $pd['pAdhar'] ?></td>
            <td data-label="Date & Time"><?= date("d/m/Y, g:i a", strtotime($pd['pDateTime'])) ?></td>
            <?php if ($pd['pStatus'] == 0) { ?>
              <td data-label="Pan Details" style="color: blue">Waiting for Approval</td>
            <?php } else if ($pd['pStatus'] == 1) { ?>
              <td data-label="Pan Details" style="color: green"><a class="viewPanNo" href="#" onclick="viewPanNo('<?= $pd['pId'] ?>')">View PAN No</a></td>
            <?php } else if ($pd['pStatus'] == 2) { ?>
              <td data-label="Pan Details" style="color: green"><?= $pd['pPAN'] ?></td>
            <?php } else { ?>
              <td data-label="Pan Details" style="color: red">Request Rejected</td>
            <?php } ?>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <table id="ayshmnData" class="d-none">
      <thead>
        <tr>
          <th>Sl. No.</th>
          <th scope="col">Name</th>
          <th scope="col">Father Name</th>
          <th scope="col">Date of Application</th>
          <th scope="col">Download</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>

    <!-- Add wallet modal -->
    <div class="modal fade" id="addWallet" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Add Amount to Wallet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post" action="<?= base_url('UserControl/requestPayment') ?>">
              <div class="mb-3">
                <input type="hidden" name="orderId" value="<?= "PTM" . date('Ymdhis') . rand(999, 99999) . "_" . session()->get('uId'); ?>">
                <input type="hidden" name="custId" value="<?= "CUST" . session()->get('uId'); ?>">
                <select class="form-select" name="orderAmt">
                  <option value="" selected>Choose your Price Option</option>
                  <option value="1">Rs. 5</option>
                  <option value="10">Rs. 10</option>
                  <option value="15">Rs. 15</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">Pay Now</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Request pan modal -->
    <div class="modal fade" id="requestPan" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Request Pan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php if ($userData[0]['uWallet'] >= 20) { ?>
              <form>
                <div class="mb-3">
                  <label for="pName" class="form-label">Enter Requestee Name</label>
                  <input type="text" class="form-control" name="pName" id="pName">
                </div>
                <div class="mb-3">
                  <label for="pAdhar" class="form-label">Enter Requestee Aadhaar No.</label>
                  <input type="number" class="form-control" name="pAdhar" id="pAdhar">
                </div>
                <button type="submit" class="btn btn-primary">Request Now</button>
              </form>
            <?php } else {
              print "You need to add minimum 20 rupees in your wallet to request for Pan!";
            } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Request NSDL Pan modal -->
    <div class="modal fade" id="requestNSDLPan" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">NSDL Pan Instant pdf</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php if ($userData[0]['uWallet'] >= 20) { ?>
              <form class="row g-3" autocomplete="off">
                <div class="col-md-12">
                  <label for="aadhar" class="form-label">Enter Aadhar Number</label>
                  <input type="text" class="form-control" name="aadhar" id="aadhar">
                </div>
                <div class="col-md-12">
                  <label for="pan" class="form-label">Enter PAN Number</label>
                  <input type="text" class="form-control" name="pan" id="pan">
                </div>
                <div class="col-md-6">
                  <label for="month" class="form-label">Enter Birth Month</label>
                  <input type="text" class="form-control" name="month" id="month">
                </div>
                <div class="col-md-6">
                  <label for="year" class="form-label">Enter Birth Year</label>
                  <input type="text" class="form-control" name="year" id="year">
                </div>
                <div class="col-md-6">
                  <label for="captcha" class="form-label">Enter Captcha shown aside</label>
                  <input type="text" class="form-control" name="captcha" id="captcha">
                </div>
                <div class="col-md-6">
                  <label for="captcha" class="form-label">Captcha Image</label>
                  <img id="imgcaptcha" alt="captcha" height="37px" />
                </div>

                <input name="jsession" id="jsession" class="form-input" type="hidden" />
                <input name="paam" id="paam" class="form-input" type="hidden">

                <div class="col-md-12 mt-5 text-center">
                  <button type="submit" class="btn btn-primary">Request Now</button>
                </div>
              </form>
            <?php } else {
              print "You need to add minimum 20 rupees in your wallet to request for Pan!";
            } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Request ayushman modal -->
    <div class="modal fade" id="requestAyushman" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Request Pan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php if ($userData[0]['uWallet'] >= 5) { ?>
              <form>
                <div class="mb-3">
                  <label class="form-label">Select Your State</label>
                  <select name="s1" class="form-select">
                    <option value="">Choose Here</option>
                    <option value="35">ANDAMAN AND NICOBAR ISLANDS</option>
                    <option value="28">ANDHRA PRADESH</option>
                    <option value="12">ARUNACHAL PRADESH</option>
                    <option value="18">ASSAM</option>
                    <option value="10">BIHAR</option>
                    <option value="4">CHANDIGARH</option>
                    <option value="22">CHHATTISGARH</option>
                    <option value="26">DADRA AND NAGAR HAVELI</option>
                    <option value="25">DAMAN AND DIU</option>
                    <option value="7">DELHI</option>
                    <option value="30">GOA</option>
                    <option value="24">GUJARAT</option>
                    <option value="6">HARYANA</option>
                    <option value="2">HIMACHAL PRADESH</option>
                    <option value="1">JAMMU AND KASHMIR</option>
                    <option value="20">JHARKHAND</option>
                    <option value="29">KARNATAKA</option>
                    <option value="32">KERALA</option>
                    <option value="37">LADAKH</option>
                    <option value="31">LAKSHADWEEP</option>
                    <option value="23">MADHYA PRADESH</option>
                    <option value="27">MAHARASHTRA</option>
                    <option value="14">MANIPUR</option>
                    <option value="17">MEGHALAYA</option>
                    <option value="15">MIZORAM</option>
                    <option value="13">NAGALAND</option>
                    <option value="21">ODISHA</option>
                    <option value="34">PUDUCHERRY</option>
                    <option value="3">PUNJAB</option>
                    <option value="8">RAJASTHAN</option>
                    <option value="11">SIKKIM</option>
                    <option value="33">TAMIL NADU</option>
                    <option value="36">TELANGANA</option>
                    <option value="16">TRIPURA</option>
                    <option value="5">UTTARAKHAND</option>
                    <option value="9">UTTAR PRADESH</option>
                    <option value="19">WEST BENGAL</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Select Parameter Type</label>
                  <select name="p3" class="form-select">
                    <option value="">Choose Here</option>
                    <option value="A">AB-PMJAY ID</option>
                    <option value="R">Family-ID/SAMAGRA ID/NFSA Ration Card Number/HHID</option>
                    <option value="S">Mobile Number</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="parameterNo" class="form-label">Enter Parameter Number</label>
                  <input type="text" name="p1" class="form-control" id="parameterNo" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary">Request Now</button>
              </form>
            <?php } else {
              print "You need to add minimum 5 rupees in your wallet to request for Pan!";
            } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Ration find modal -->
    <div class="modal fade" id="rationFind" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Ration Find By Aadhar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php if ($userData[0]['uWallet'] >= 5) { ?>
              <form>
                <div class="mb-3">
                  <label class="form-label">Select Ration Holder State</label>
                  <select name="raState" class="form-select">
                    <option value="">Choose Here</option>
                    <option value="35">ANDAMAN AND NICOBAR ISLANDS</option>
                    <option value="28">ANDHRA PRADESH</option>
                    <option value="12">ARUNACHAL PRADESH</option>
                    <option value="18">ASSAM</option>
                    <option value="10">BIHAR</option>
                    <option value="4">CHANDIGARH</option>
                    <option value="22">CHHATTISGARH</option>
                    <option value="26">DADRA AND NAGAR HAVELI</option>
                    <option value="25">DAMAN AND DIU</option>
                    <option value="7">DELHI</option>
                    <option value="30">GOA</option>
                    <option value="24">GUJARAT</option>
                    <option value="6">HARYANA</option>
                    <option value="2">HIMACHAL PRADESH</option>
                    <option value="1">JAMMU AND KASHMIR</option>
                    <option value="20">JHARKHAND</option>
                    <option value="29">KARNATAKA</option>
                    <option value="32">KERALA</option>
                    <option value="37">LADAKH</option>
                    <option value="31">LAKSHADWEEP</option>
                    <option value="23">MADHYA PRADESH</option>
                    <option value="27">MAHARASHTRA</option>
                    <option value="14">MANIPUR</option>
                    <option value="17">MEGHALAYA</option>
                    <option value="15">MIZORAM</option>
                    <option value="13">NAGALAND</option>
                    <option value="21">ODISHA</option>
                    <option value="34">PUDUCHERRY</option>
                    <option value="3">PUNJAB</option>
                    <option value="8">RAJASTHAN</option>
                    <option value="11">SIKKIM</option>
                    <option value="33">TAMIL NADU</option>
                    <option value="36">TELANGANA</option>
                    <option value="16">TRIPURA</option>
                    <option value="5">UTTARAKHAND</option>
                    <option value="9">UTTAR PRADESH</option>
                    <option value="19">WEST BENGAL</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="raName" class="form-label">Enter Ration Holder Name</label>
                  <input type="text" name="raName" class="form-control" id="raName">
                </div>
                <div class="mb-3">
                  <label for="raNumber" class="form-label">Enter Ration Card Number</label>
                  <input type="number" name="raNumber" class="form-control" id="raNumber">
                </div>
                <button type="submit" class="btn btn-primary">Request Now</button>
              </form>
            <?php } else {
              print "You need to add minimum 5 rupees in your wallet to request for Pan!";
            } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Referral modal -->
    <div class="modal fade" id="referral" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Refer and Earn</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="fw-bold text-danger">Refer your friends and earn Rs.5 directly to your wallet for every PAN request made by them.</p>
            <form>
              <div class="mb-3">
                <input type="text" class="form-control copyLink" value="<?= base_url('register?r=' . $userData[0]['uReferralCode']) ?>" readonly>
              </div>
              <button type="button" class="btn btn-primary copyLinkBtn">Copy Link</button>
            </form>
            <?php if ($referralCount > 0) { ?>
              <div class="card mt-4 p-2">
                <p>You have referred <b><?= $referralCount ?></b> friends.</p>
                <ul>
                  <?php foreach ($referralData as $rd) { ?>
                    <li><?= $rd['uName'] ?></li>
                  <?php } ?>
                </ul>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- change password modal -->
    <div class="modal fade" id="changePass" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Change your Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="newPass" class="form-label">Enter New Password</label>
                <input type="password" class="form-control" name="newPass" id="newPass">
              </div>
              <button type="submit" class="btn btn-primary">Change Now</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Help Desk modal -->
    <div class="modal fade" id="helpDesk" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Chat with Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="post" action="<?= base_url('UserControl/helpDesk') ?>">
              <div class="mb-3">
                <textarea name="hMsg" class="form-control" placeholder="Enter your Message..." required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
            <div class="card mt-3">
              <?php foreach ($helpDeskData as $hd) { ?>
                <div class="mt-2 ps-2 pe-2">
                  <p><b><?= $hd['hName'] ?> : </b><?= $hd['hMsg'] ?><br>
                    <font size="2" color="blue"><?= date("d/m/Y, g:i a", strtotime($hd['hDate'])) ?></font>
                  </p>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript">
      if ($("#requestPan form").length > 0) {
        $("#requestPan form").validate({
          rules: {
            pName: {
              required: true,
            },
            pAdhar: {
              required: true,
              number: true,
              minlength: 12,
              maxlength: 12,
            },
          },

          submitHandler: function(form) {
            Swal.fire({
              title: 'Are you sure?',
              text: "You will be charged Rs.20 for this request!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Please!'
            }).then((result) => {
              if (result.isConfirmed) {
                var requestPanData = $(form).serialize();
                $.ajax({
                  url: "<?= base_url('UserControl/requestPan') ?>",
                  type: "POST",
                  cache: false,
                  data: requestPanData,
                  processData: false,
                  dataType: "JSON",
                  success: function(response) {
                    if (response.type == 0) {
                      Swal.fire('Alert!', response.msg, 'error');
                    } else {
                      $("body").load("<?= base_url('/') ?>");
                    }
                  },
                });
              }
            })
          }
        })
      }

      if ($("#rationFind form").length > 0) {
        $("#rationFind form").validate({
          rules: {
            raName: {
              required: true,
            },
            raState: {
              required: true,
            },
            raNumber: {
              required: true,
            },
          },

          submitHandler: function(form) {
            Swal.fire({
              title: 'Are you sure?',
              text: "You will be charged Rs.20 for this request!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Please!'
            }).then((result) => {
              if (result.isConfirmed) {
                var data = $(form).serialize();
                $.ajax({
                  url: "<?= base_url('UserControl/requestRation') ?>",
                  type: "POST",
                  cache: false,
                  data: data,
                  processData: false,
                  dataType: "JSON",
                  success: function(response) {
                    if (response.type == 0) {
                      Swal.fire('Alert!', response.msg, 'error');
                    } else {
                      $("body").load("<?= base_url('/') ?>");
                    }
                  },
                });
              }
            })
          }
        })
      }

      function showCaptcha() {
        $.ajax({
          url: "<?= base_url('UserControl/showCaptcha') ?>",
          type: "POST",
          cache: false,
          processData: false,
          dataType: "JSON",
          success: function(response) {
            $("#imgcaptcha").attr("src", "https://digiapi.xyz/pan3/captcha/" + response.captcha + ".png");
            $("#jsession").val(response.jsession);
            $("#paam").val(response.paam);
          },
        });
      }

      if ($("#requestNSDLPan form").length > 0) {
        $("#requestNSDLPan form").validate({
          rules: {
            aadhar: {
              required: true,
              number: true,
              minlength: 12,
              maxlength: 12,
            },
            pan: {
              required: true,
              minlength: 10,
              maxlength: 10,
            },
            month: {
              required: true,
              number: true,
              minlength: 2,
              maxlength: 2,
            },
            year: {
              required: true,
              number: true,
              minlength: 4,
              maxlength: 4,
            },
            captcha: {
              required: true,
            },
          },

          submitHandler: function(form) {
            Swal.fire({
              title: 'Are you sure?',
              text: "You will be charged Rs.20 for this request!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Please!'
            }).then((result) => {
              if (result.isConfirmed) {
                var panData = $(form).serialize();
                $.ajax({
                  url: "<?= base_url('UserControl/fetchPanDetails') ?>",
                  type: "POST",
                  cache: false,
                  data: panData,
                  processData: false,
                  dataType: "JSON",
                  success: function(response) {
                    // if (response.type == 0) {
                    //   Swal.fire('Alert!', response.error, 'error');
                    // } else {

                    // }
                    console.log(response);
                  },
                });
              }
            })
          }
        })
      }

      if ($("#requestAyushman form").length > 0) {
        $("#requestAyushman form").validate({
          rules: {
            s1: {
              required: true,
            },
            p1: {
              required: true,
            },
            p3: {
              required: true,
            },
          },

          submitHandler: function(form) {
            var requestAyushmanData = $(form).serialize();
            $.ajax({
              url: "<?= base_url('UserControl/requestAyushman') ?>",
              type: "POST",
              cache: false,
              data: requestAyushmanData,
              processData: false,
              dataType: "JSON",
              success: function(response) {
                if (response.type == 0) {
                  Swal.fire('Alert!', response.msg, 'error');
                } else {
                  $('#mytable').hide();
                  $('#ayshmnData').removeClass('d-none');
                  $('#requestAyushman form').trigger('reset');
                  $('#requestAyushman').modal('hide');

                  var data = '';
                  var sl = 1;
                  $.each(response, function(key, value) {
                    data += '<tr>';
                    data += '<td>' + sl++ + '</td>';
                    data += '<td>' + value.userName + '</td>';
                    data += '<td>' + value.fatherName + '</td>';
                    data += '<td>' + value.createdOn + '</td>';
                    data += '<td>' + "<button class='btn btn-success' onClick='ayushmanCardDl(this)' data-id='" + value.pmrssmid + "' data-username='" + value.userName + "' data-familyid='" + value.pmrssmid + "' data-stid='" + value.stateCode + "'>Download</button>" + '</td>';
                    data += '</tr>';
                  });
                  $('#ayshmnData').append(data);
                }
              },
            });
          }
        })
      }

      function ayushmanCardDl(elem) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You will be charged Rs.5 for this request.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Download!'
        }).then((result) => {
          if (result.isConfirmed) {
            location.href = "<?= base_url('UserControl/ayushmanCardDl') ?>/" + $(elem).data("username") + "/" + $(elem).data("familyid") + "/" + $(elem).data("id") + "/" + $(elem).data("stid");
          }
        })
      }

      function viewPanNo(panId) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You will be charged Rs.30 for this request!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Show!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "<?= base_url('UserControl/viewPanNo') ?>",
              type: "POST",
              data: {
                panId: panId
              },
              success: function(response) {
                $("body").load("<?= base_url('/') ?>");
              },
            });
          }
        })
      }

      if ($("#changePass form").length > 0) {
        $("#changePass form").validate({
          rules: {
            newPass: {
              required: true,
              minlength: 6,
            },
          },

          submitHandler: function(form) {
            var changePassData = $(form).serialize();
            $.ajax({
              url: "<?= base_url('UserControl/changePass') ?>",
              type: "POST",
              cache: false,
              data: changePassData,
              processData: false,
              dataType: "JSON",
              success: function(response) {
                if (response.type == 0) {
                  Swal.fire('Alert!', response.msg, 'error');
                } else {
                  $("body").load("<?= base_url('/') ?>");
                }
              },
            });
          }
        })
      }

      $('.copyLinkBtn').click(function() {
        $('.copyLink').select();
        document.execCommand("copy");
        $('.copyLinkBtn').html('Link Copied!');
        setTimeout(function() {
          $('.copyLinkBtn').html('Copy Link');
        }, 2000);
      });

      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
      }
    </script>
  </body>

  </html>
<?php } ?>