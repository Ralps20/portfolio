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
$qry = $conn->query("SELECT * FROM `household_list` where household_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="household-form">
        <input type="hidden" name="id" value="<?php echo isset($household_id) ? $household_id : '' ?>">
        <div class="form-group">
            <label for="house_no" class="control-label">Household #</label>
            <input type="text" pattern="[0-9]+" name="house_no" id="house_no" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($house_no) ? $house_no : '' ?>" required>
        </div>
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
        <div class="form-group">
            <label for="contact" class="control-label">Contact #</label>
            <input type="text" pattern="[0-9]+" name="contact" id="contact" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($contact) ? $contact : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="control-label">Email</label>
            <input type="email" name="email" id="email" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($email) ? $email : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="occupation" class="control-label">Occupation</label>
            <input type="text" name="occupation" id="occupation" class="form-control form-control-sm rounded-0 col-md-6" value="<?php echo isset($occupation) ? $occupation : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="purok_id" class="control-label">Purok</label>
            <select name="purok_id" id="purok_id" class="form-select form-select-sm rounded-0 select2">
                <option value="" disabled <?php echo !isset($purok_id) ? 'selected' : '' ?>></option>
                <?php 
                $purok = $conn->query("SELECT * FROM purok_list");
                while($row = $purok->fetchArray()):
                ?>
                <option value="<?php echo $row['purok_id'] ?>" <?php echo isset($purok_id) && $purok_id == $row['purok_id'] ? 'selected' : "" ?>><?php echo $row['purok'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#household-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_household',
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