<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/../DBConnection.php");
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] <= 0) {
    http_response_code(403);
    exit('Access denied.');
}
$conn = new DBConnection();
if(isset($_GET['id'])){
$qry = $conn->query("SELECT h.*,CONCAT(h.lastname, ', ', h.firstname, ', ', h.middlename) as fullname, p.purok FROM household_list h inner join `purok_list` p on h.purok_id = p.purok_id where h.household_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="container-fluid">
    <div id="outprint">
        <dl class="row w-100">
            <dt class="text-muted col-md-4">Household #:</dt>
            <dd class="col-md-8"><?php echo $house_no ?></dd>
            <dt class="text-muted col-md-4">Resident Name:</dt>
            <dd class="col-md-8"><?php echo $fullname ?></dd>
            <dt class="text-muted col-md-4">Contact #:</dt>
            <dd class="col-md-8"><?php echo $contact ?></dd>
            <dt class="text-muted col-md-4">Email:</dt>
            <dd class="col-md-8"><?php echo $email ?></dd>
            <dt class="text-muted col-md-4">Occupation:</dt>
            <dd class="col-md-8"><?php echo $occupation ?></dd>
            <dt class="text-muted col-md-4">Address:</dt>
            <dd class="col-md-8"><?php echo $purok ?>, <?php echo isset($_SESSION['system_info']['barangay_name']) ? $_SESSION['system_info']['barangay_name'] : '' ?>, <?php echo isset($_SESSION['system_info']['city']) ? $_SESSION['system_info']['city'] : '' ?>, <?php echo isset($_SESSION['system_info']['province']) ? $_SESSION['system_info']['province'] : '' ?>, <?php echo isset($_SESSION['system_info']['zip_code']) ? $_SESSION['system_info']['zip_code'] : '' ?></dd>
        </dl>
    </div>
    <div class="row justify-content-end">
        <div class="col-auto me-1">
            <button class="btn btn-sm btn-success rounded-0" id='print' type="button"><i class="fa fa-print"></i> Print</button>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-dark rounded-0" data-bs-dismiss='modal' type="button"><i class="fa fa-times"></i> Close</button>
        </div>
    </div>    
    <div class="clearfix mb-1"></div>
</div>
<noscript>
    <div class="d-flex w-100 align-items-center">
        <div class="col-2 px-3">
            <center><img src="<?php echo is_file('./../uploads/logo.png') ? './../uploads/logo.png' : './../images/no-image-available.png' ?>" alt="Barangay Logo" class="img-fluid rounded-0" width="100px" height="100px"></center>
        </div>
        <div class="col-8 flex-grow-1 lh-1">
            <p class="m-0 text-center">Republic of the Philippines</p>
            <p class="m-0 text-center"><?php echo $_SESSION['system_info']['city'] ?></p>
            <div class="clearfix"></div>
            <p class="fw-bold text-center"><large><?php echo $_SESSION['system_info']['barangay_name'] ?></large></p>
            <p class="fw-bold text-center">Household Resident Information</p>
        </div>
        <div class="col-2">

        </div>
    </div>
    <hr>
</noscript>
<script>
$(function(){
    $('#print').click(function(){
        var _p = $('#outprint').clone()
        var _h = $('head').clone()
        var _header = $('noscript').html()
        var el = $('<div>')
        el.append(_h)
        el.append(_header)
        el.append(_p)
        
        var nw = window.open("","_blank","width=1000,height=900,top=50,left=250")
                    nw.document.write(el.html())
                    nw.document.close()
                    setTimeout(() => {
                    nw.print()
                        setTimeout(() => {
                            nw.close()
                        }, 200);
                    }, 500);
    })
})
</script>