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
$qry = $conn->query("SELECT o.*,CONCAT(o.lastname, ', ', o.firstname, ', ', o.middlename) as fullname, p.position FROM official_list o inner join `position_list` p on o.position_id = p.position_id where o.official_id = '{$_GET['id']}'");
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
            <dt>Appointed Official Name:</dt>
            <dd><?php echo $fullname ?></dd>
            <dt>Contact #:</dt>
            <dd><?php echo $contact ?></dd>
            <dt>Position:</dt>
            <dd><?php echo $position ?></dd>
            
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