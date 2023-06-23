<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Login Page</title>
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
                <div class="col-lg-12 login-title">
                    USER REGISTER
                </div>

                <div class="col-lg-12 login-form">
                    <div class="col-lg-12 login-form">
                        <form method="post" action="<?= base_url('UserControl/validateUserRegister') ?>">
                        <input type="hidden"  name="referId"  value="<?= $referId ?>">
                        <div class="form-group">
                                <label class="form-control-label">Enter your Name</label>
                                <input type="text" class="form-control" name="uName" value="<?= old('uName'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Enter your Phone</label>
                                <input type="text" class="form-control" name="uPhone" value="<?= old('uPhone'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Enter your Email</label>
                                <input type="email" class="form-control" name="uEmail" value="<?= old('uEmail'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Enter your Password</label>
                                <input type="password" class="form-control" name="uPass" value="<?= old('uPass'); ?>">
                            </div>
                            <?php if (session()->getFlashdata('msg') !== NULL){ ?>
                                <div class="alert <?= session()->getFlashdata('alert'); ?>" role="alert">
                                    <?= session()->getFlashdata('msg'); ?>
                                </div>
                            <?php } ?>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="">
                                    <button type="submit" class="btn btn-outline-primary">Register Here</button>
                                </div>
                                <div class="signin-button">
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