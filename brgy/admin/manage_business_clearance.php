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
$qry = $conn->query("SELECT * FROM `business_clearance_list` where business_clearance_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="business_clearance-form">
        <input type="hidden" name="id" value="<?php echo isset($business_clearance_id) ? $business_clearance_id : '' ?>">
        <div class="form-group">
            <label for="owner_name" class="control-label">Owner Name</label>
            <input type="text" name="owner_name" id="owner_name" class="form-control form-control-sm rounded-0" value="<?php echo isset($owner_name) ? $owner_name : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="business_name" class="control-label">Business Name</label>
            <input type="text" name="business_name" id="business_name" class="form-control form-control-sm rounded-0" value="<?php echo isset($business_name) ? $business_name : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="business_type" class="control-label">Business Type</label>
            <input type="text" name="business_type" id="business_type" class="form-control form-control-sm rounded-0" value="<?php echo isset($business_type) ? $business_type : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="tin" class="control-label">TIN</label>
            <input type="text" pattern="[0-9/s-]+" name="tin" id="tin" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($tin) ? $tin : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="or_no" class="control-label">OR #</label>
            <input type="text" pattern="[0-9]+" name="or_no" id="or_no" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($or_no) ? $or_no : '' ?>" required>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#business_clearance-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_business_clearance',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else{
                        _el.addClass('alert alert-danger')
                        _el.text(resp.msg)
                        _el.hide()
                        _this.prepend(_el)
                        _el.show('slow')
                    }
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>