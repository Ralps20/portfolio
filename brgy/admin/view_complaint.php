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
$qry = $conn->query("SELECT * FROM complaint_list where complaint_id = '{$_GET['id']}'");
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
        <dl class="">
            <dt>Complainant:</dt>
            <dd><?php echo $complainant_name ?></dd>
            <dt>Apellant:</dt>
            <dd><?php echo $appellant ?></dd>
            <dt>Complaint Description:</dt>
            <dd><?php echo $description ?></dd>
            <dt>Status:</dt>
            <dd><?php echo $status ?></dd>
            <dt>Filling Date/Time:</dt>
            <dd><?php echo date("M d, Y H:i",strtotime($date_created)) ?></dd>
        </dl>
    </div>
    <div class="row justify-content-end">
        <div class="col-auto">
            <button class="btn btn-sm btn-dark rounded-0" data-bs-dismiss='modal' type="button"><i class="fa fa-times"></i> Close</button>
        </div>
    </div>    
    <div class="clearfix mb-1"></div>
</div>
<script>
$(function(){
   
})
</script>