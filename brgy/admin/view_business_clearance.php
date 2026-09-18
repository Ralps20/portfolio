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
$qry = $conn->query("SELECT * FROM business_clearance_list where business_clearance_id = '{$_GET['id']}'");
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
    <div id="outprint_modal">
        <dl class="row">
            <dt class="col-md-6">Owner Name</dt>
            <dd class="col-md-6 border-bottom border-dark">: <?php echo $owner_name ?></dd>
            <dt class="col-md-6">Business Name</dt>
            <dd class="col-md-6 border-bottom border-dark">: <?php echo $business_name ?></dd>
            <dt class="col-md-6">Kind of Business</dt>
            <dd class="col-md-6 border-bottom border-dark">: <?php echo $business_type ?></dd>
            <dt class="col-md-6">TIN</dt>
            <dd class="col-md-6 border-bottom border-dark">: <?php echo $tin ?></dd>
            <dt class="col-md-6">OR #</dt>
            <dd class="col-md-6 border-bottom border-dark">: <?php echo $or_no ?></dd>
            <dt class="col-md-6">Date Issued</dt>
            <dd class="col-md-6 border-bottom border-dark">: <?php echo date("M d, Y",strtotime($date_created)) ?></dd>
            <dt class="col-md-6">Issued at</dt>
            <dd class="col-md-6 border-bottom border-dark">: <?php echo $_SESSION['system_info']['barangay_name'] ?>, <?php echo $_SESSION['system_info']['city'] ?>, <?php echo $_SESSION['system_info']['province'] ?></dd>
        </dl>
        <br>
        <br>
        <p>
            <span class="ms-5"></span>This CERTIFICATION is issued upod the request of the above-named person in connection with his/her application for <b class="px-4 border-bottom border-dark">Business Permit</b>.
        </p>
        <p><span class="ms-5">ISSUED this <b><u class="px-1"><?php echo date('dS',strtotime($date_created)) ?></u></b> day of <b><u class="px-1"><?php echo date('F Y',strtotime($date_created)) ?></u></b> at <?php echo $_SESSION['system_info']['barangay_name'] ?>, <?php echo $_SESSION['system_info']['city'] ?>, <?php echo $_SESSION['system_info']['province'] ?>.</p>
        <br>
        <br>
        <br>
        <br>
        <div class="d-flex w-100 justify-content-end">
            <?php 
            $position = $conn->query("SELECT position,position_id from `position_list` where is_approver = 1 ")->fetchArray();
            if(isset($position['position_id']))
            $official = $conn->query("SELECT CONCAT(lastname, ', ', firstname, ', ', middlename) as fullname from `official_list` where position_id = '{$position['position_id']}' ")->fetchArray()['fullname'];

            ?>
            
            <div class="col-4">
                <div class="w-100 text-center border-bottom border-dark"><?php echo isset($official) ? $official : '' ?></div>
                <div class="w-100 text-center"><?php echo ($position['position']) ? $position['position'] : '' ?></div>
            </div>

        </div>
        
    </div>
    <div class="row justify-content-end border-top pt-2">
        <div class="col-auto me-1">
            <button class="btn btn-sm btn-success rounded-0" id='print_data' type="button"><i class="fa fa-print"></i> Print</button>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-dark rounded-0" data-bs-dismiss='modal' type="button"><i class="fa fa-times"></i> Close</button>
        </div>
    </div>    
    <div class="clearfix mb-1"></div>
</div>
<div id="noscript2" class="d-none">
    <style>
        @media print{
            .col-md-6{
                width:50% !important;
            }
        }
    </style>
    <div class="d-flex w-100 align-items-center">
        <div class="col-2 px-3">
            <center><img src="<?php echo is_file('./../uploads/logo.png') ? './../uploads/logo.png' : './../images/no-image-available.png' ?>" alt="Barangay Logo" class="img-fluid rounded-0" width="100px" height="100px"></center>
        </div>
        <div class="col-8 flex-grow-1 lh-1">
            <p class="m-0 text-center">Republic of the Philippines</p>
            <p class="m-0 text-center"><?php echo $_SESSION['system_info']['city'] ?></p>
            <div class="clearfix"></div>
            <p class="fw-bold text-center"><large><?php echo $_SESSION['system_info']['barangay_name'] ?></large></p>
        </div>
        <div class="col-2">

        </div>
    </div>
    <hr>
    <br>
    <h2 class="fw-bold text-center">Barangay Business Clearance</h2>
    <br>
    <br>
    <br>
    <br>
</div>
<script>
$(function(){
    $('#print_data').click(function(){
        var _p = $('#outprint_modal').clone()
        var _h = $('head').clone()
        var _header = $('#noscript2').html()
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