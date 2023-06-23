<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Reset Password</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') . "?v=" . time() ?>">
</head>

<body class="userAuth">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-2"></div>
            <div class="col-lg-6 col-md-8 login-box">
                <div class="col-lg-12 login-key">
                    <i class="fa fa-key" aria-hidden="true"></i>
                </div>
                <div class="col-lg-12 login-title">
                    USER RESET PASSWORD
                </div>

                <div class="col-lg-12 login-form">
                    <div class="col-lg-12 login-form">
                        <form method="post"  action="<?= base_url('UserControl/validateUserResetPass') ?>">
                            <input type="hidden" class="form-control" name="uId" value="<?= $uId ?>">
                            <div class="form-group">
                                <label class="form-control-label">Enter New Password</label>
                                <input type="password" class="form-control" name="uPass1" value="<?= old('uPass1'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="uPass2" value="<?= old('uPass2'); ?>">
                            </div>
                            <?php if (session()->getFlashdata('msg') !== NULL){ ?>
                                <div class="alert <?= session()->getFlashdata('alert'); ?>" role="alert">
                                    <?= session()->getFlashdata('msg'); ?>
                                </div>
                            <?php } ?>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="">
                                    <button type="submit" class="btn btn-outline-primary">Reset Now</button>
                                </div>
                                <div class="register-button">
                                    <a href="<?= base_url('login') ?>">Login Now</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>