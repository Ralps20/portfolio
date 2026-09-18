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
$qry = $conn->query("SELECT * FROM `clearance_list` where clearance_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="clearance-form">
        <input type="hidden" name="id" value="<?php echo isset($clearance_id) ? $clearance_id : '' ?>">
        <div class="form-group row">
            <div class="col-md-4">
                <label for="lastname" class="control-label">Last Name</label>
                <input type="text" name="lastname" id="lastname" class="form-control form-control-sm rounded-0" value="<?php echo isset($lastname) ? $lastname : '' ?>" required>
            </div>
            <div class="col-md-4">
                <label for="firstname" class="control-label">Firstname</label>
                <input type="text" name="firstname" id="firstname" class="form-control form-control-sm rounded-0" value="<?php echo isset($firstname) ? $firstname : '' ?>" required>
            </div>
            <div class="col-md-4">
                <label for="middlename" class="control-label">Middlename</label>
                <input type="text" name="middlename" id="middlename" class="form-control form-control-sm rounded-0" value="<?php echo isset($middlename) ? $middlename : '' ?>" placeholder="(optional)">
            </div>
        </div>
        <div class="form-group col-md-4">
            <label for="age" class="control-label">Age</label>
            <input type="text" pattern="[0-9]+" name="age" id="age" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($age) ? $age : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="contact" class="control-label">Contact #</label>
            <input type="text" pattern="[0-9]+" name="contact" id="contact" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($contact) ? $contact : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="or_no" class="control-label">OR #</label>
            <input type="text" pattern="[0-9]+" name="or_no" id="or_no" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($or_no) ? $or_no : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="purpose" class="control-label">Purpose</label>
            <textarea rows="2" name="purpose" id="purpose" class="form-control form-control-sm rounded-0 col-md-6" required style="resize:none"><?php echo isset($purpose) ? $purpose : '' ?></textarea>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#clearance-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_clearance',
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