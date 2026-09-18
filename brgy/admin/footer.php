  </div><!-- /.content -->
</div><!-- /.main -->

<!-- Generic modal used by uni_modal() to load manage_*.php / view_*.php forms -->
<div class="modal fade" id="uni_modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" id="uni_modal_dialog">
    <div class="modal-content rounded-0">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="uni_modal_title">Modal Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="uni_modal_body">
        <div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-dark rounded-0" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-sm btn-primary rounded-0" id="uni_modal_save_btn">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Generic confirmation modal used by _conf() for delete actions -->
<div class="modal fade" id="confirm_modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-0">
      <div class="modal-header py-2">
        <h5 class="modal-title">Please Confirm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirm_modal_body"></div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-dark rounded-0" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-sm btn-danger rounded-0" id="confirm_modal_yes">Yes, Proceed</button>
      </div>
    </div>
  </div>
</div>

<script>
    // ── Sidebar toggle (mobile) ──
    $(function(){
        $('#hamburger').on('click', function(){
            $('#sidebar').toggleClass('show');
            $('#backdrop').toggleClass('show');
        });
        $('#backdrop').on('click', function(){
            $('#sidebar').removeClass('show');
            $('#backdrop').removeClass('show');
        });

        $('#logout_btn').on('click', function(){
            _conf("Are you sure you want to logout?", 'do_logout', []);
        });

        $('#my_account').on('click', function(){
            uni_modal('My Account', 'manage_account.php');
        });
    });

    function do_logout(){
        window.location.href = './../Actions.php?a=logout';
    }

    /**
     * uni_modal(title, url, size)
     * Loads `url` (an AJAX-returned HTML fragment, typically a manage_*.php
     * or view_*.php form) into the shared #uni_modal and shows it.
     *
     * size: '' (default), 'mid-large', or 'large'
     */
    function uni_modal(title, url, size){
        var $dialog = $('#uni_modal_dialog');
        $dialog.removeClass('modal-lg modal-xl');
        if(size === 'mid-large') $dialog.addClass('modal-lg');
        if(size === 'large') $dialog.addClass('modal-xl');

        $('#uni_modal_title').text(title);
        $('#uni_modal_body').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        $('#uni_modal_save_btn').show();

        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('uni_modal'));
        modal.show();

        $.ajax({
            url: url,
            method: 'GET',
            error: function(err){
                console.log(err);
                $('#uni_modal_body').html('<div class="alert alert-danger">Failed to load content.</div>');
            },
            success: function(resp){
                $('#uni_modal_body').html(resp);
                // View-only screens (view_*.php) hide the footer/save button themselves via CSS
                if($('#uni_modal_body form').length === 0){
                    $('#uni_modal_save_btn').hide();
                }
            }
        });

        // Clicking the shared footer "Save" button submits whatever form
        // was loaded into the modal body.
        $('#uni_modal_save_btn').off('click').on('click', function(){
            $('#uni_modal_body form').first().trigger('submit');
        });
    }

    /**
     * _conf(message, callback_fn_name, args)
     * Shows a confirmation modal; on confirm, calls window[callback_fn_name](...args)
     */
    function _conf(message, callback, args){
        $('#confirm_modal_body').html(message);
        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirm_modal'));
        modal.show();

        $('#confirm_modal_yes').off('click').on('click', function(){
            if(typeof window[callback] === 'function'){
                window[callback].apply(null, args || []);
            }
        });
    }
</script>
</body>
</html>
