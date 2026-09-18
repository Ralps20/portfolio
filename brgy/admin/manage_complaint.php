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
$qry = $conn->query("SELECT * FROM `complaint_list` where complaint_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="complaint-form">
        <input type="hidden" name="id" value="<?php echo isset($complaint_id) ? $complaint_id : '' ?>">
        <div class="form-group">
            <label for="complainant_name" class="control-label">Complainant</label>
            <input type="text" name="complainant_name" id="complainant_name" class="form-control form-control-sm rounded-0" value="<?php echo isset($complainant_name) ? $complainant_name : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="appellant" class="control-label">Appellant</label>
            <input type="text" name="appellant" id="appellant" class="form-control form-control-sm rounded-0" value="<?php echo isset($appellant) ? $appellant : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="description" class="control-label">Complaint Description</label>
            <textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0 col-md-6" required style="resize:none"><?php echo isset($description) ? $description : '' ?></textarea>
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Complaint Description</label>
            <select name="status" id="status" class="form-select form-select-sm rounded-0" required>
                <option <?php echo isset($status) && $status =='Not Settled' ? 'selected' : '' ?>>Not Settled</option>
                <option <?php echo isset($status) && $status =='Settled' ? 'selected' : '' ?>>Settled</option>
            </select>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#complaint-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./../Actions.php?a=save_complaint',
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