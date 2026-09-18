<?php require_once(__DIR__.'/header.php'); ?>
<style>
    #logo-img{
        width:100px;
        height:100px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
</style>
<div class="container py-3">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Barangay and System Information Management
            </h3>
        </div>
        <div class="card-body">
            <form action="" id="sys-info">
                <div class="form-group">
                    <label for="barangay_name" class="control-label">Barangay Name</label>
                    <input type="text" name="barangay_name" class="form-control form-control-sm rounded-0" value="<?php echo isset($_SESSION['system_info']['barangay_name']) ? $_SESSION['system_info']['barangay_name'] : "" ?>" required>
                </div>
                <div class="form-group">
                    <label for="city" class="control-label">City/Municipality</label>
                    <input type="text" name="city" class="form-control form-control-sm rounded-0" value="<?php echo isset($_SESSION['system_info']['city']) ? $_SESSION['system_info']['city'] : "" ?>" required>
                </div>
                <div class="form-group">
                    <label for="province" class="control-label">Province</label>
                    <input type="text" name="province" class="form-control form-control-sm rounded-0" value="<?php echo isset($_SESSION['system_info']['province']) ? $_SESSION['system_info']['province'] : "" ?>" required>
                </div>
                <div class="form-group">
                    <label for="zip_code" class="control-label">Zip Code</label>
                    <input type="text" name="zip_code" class="form-control form-control-sm rounded-0" value="<?php echo isset($_SESSION['system_info']['zip_code']) ? $_SESSION['system_info']['zip_code'] : "" ?>" required>
                </div>
                <div class="form-group">
                    <label for="logo" class="control-label">Logo</label>
                    <input type="file" name="logo" id="logo" class="form-control form-control-sm rounded-0" accept="image/png,image/jpeg" onclick="display_img(this)">
                </div>
                <div class="form-group text-center mt-2">
                    <img src="<?php echo is_file('./../uploads/logo.png') ? './../uploads/logo.png' : './../images/no-image-available.png' ?>" id="logo-img" alt="Barangay Logo">
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <button class="btn btn-sm btn-primary rounded-0" type="submit" form="sys-info">Save</button>
            <button class="btn btn-sm btn-dark rounded-0" type="reset" form="sys-info">Reset</button>
        </div>
    </div>
</div>
<script>
    function display_img(input){
        if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#logo-img').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
    }
    $(function(){
        $('#sys-info').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('.card button').attr('disabled',true)
            $('.card button[type="submit"]').text('saving data...')
            $.ajax({
                url:'./../Actions.php?a=save_settings',
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
                     $('.card button').attr('disabled',false)
                     $('.card button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                            location.reload()
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('.card button').attr('disabled',false)
                     $('.card button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
<?php require_once(__DIR__.'/footer.php'); ?>
