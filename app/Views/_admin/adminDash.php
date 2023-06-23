<?php
if (!session()->get('aEmail')) {
    echo "<script> window.location.href = '".base_url('AdminControl/showAdminAuth')."'; </script>";
}
?>
<!doctype html>
  <html>
  <head>
    <title>Admin Dashboard | Pan Portal</title>
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" name="viewport"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') . "?v=" . time() ?>">

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.6/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="https://kit.fontawesome.com/8e9765b699.js"></script>
  </head>
  <body class="container panRequest">
    <?php if(session()->getFlashdata('msg')){ ?>
      <script type="text/javascript">
        Swal.fire("Alert!", "<?= session()->getFlashdata('msg') ?>", "success");
      </script>
    <?php } ?>
    <div class="panHeadd d-flex justify-content-center align-items-center flex-column">
      <h2 class="m-4">Pan Admin Dashboard</h2>
      <div>
        <button class="btn btn-outline-secondary">Total Pending : <?= $totalPending ?></button>
        <button class="btn btn-outline-secondary">Total Approved : <?= $totalApproved ?></button>
        <button class="btn btn-outline-secondary">Total Rejected : <?= $totalRejected ?></button>
      </div>
    </div>
    <table id="mytable" class="mt-4">
      <thead>
        <tr>
          <th>Sl. No.</th>
          <th scope="col">Name</th>
          <th scope="col">Aadhaar No</th>
          <th scope="col">Date & Time</th>
          <th scope="col">Pan Update</th>
          <th scope="col">Reject User</th>
        </tr>
      </thead>
      <tbody>
        <?php $c = 1; foreach($panData as $pd){ ?>
        <tr>
          <td data-label="Sl. No."><?= $c++ ?></td>
          <td data-label="Name"><?= $pd['pName'] ?></td>
          <td data-label="Aadhaar No"><?= $pd['pAdhar'] ?></td>
          <td data-label="Date & Time"><?= $pd['pDateTime'] ?></td>
          <td id="panNo">
            <form class="d-flex gap-2">
              <input type="hidden" name="userId" value="<?= $pd['pId'] ?>">
              <input name="panNo" class="form-control" placeholder="Enter PAN No">
              <button type="submit" class="btn btn-outline-primary">Approve</button>
            </form>
          </td>
          <td data-label="Reject User"><i onclick="rejectUser(this)" data-id="<?= $pd['pId'] ?>" class="fa-regular fa-circle-xmark text-danger fs-2"></i></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>

    <script type="text/javascript">
      if ($("#panNo form").length > 0) {
        $("#panNo form").validate({
          rules: {
            panNo: {
              required: true,
              minlength: 10,
              maxlength: 10,
            },
          },

          submitHandler: function(form) {
            var panData = $(form).serialize();
            $.ajax({
              url: "<?= base_url('AdminControl/panUpdate') ?>",
              type: "POST",
              cache: false,
              data: panData,
              processData: false,
              dataType: "JSON",
              success: function(response) {
                if (response.type == 0) {
                  Swal.fire('Alert!', response.msg, 'error');
                }else{
                  $("body").load("<?= base_url('admin') ?>");
                }
              },
            });
          }
        })
      }



      function rejectUser(elem) {
        Swal.fire({
        title: 'Are you sure?',
        text: "After Reject, you won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Reject!'
      }).then((result) => {
        if (result.isConfirmed) {
          var panId = $(elem).attr("data-id");
          $.ajax({
            url: "<?= base_url('AdminControl/rejectUser') ?>",
            type: "POST",
            data: {panId:panId},
            success: function(response) {
              $("body").load("<?= base_url('admin') ?>");
            },
          });
        }
      })
    }
    </script>
  </body>
  </html>