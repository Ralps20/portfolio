<?php require_once(__DIR__.'/header.php'); ?>
<style>
    .update_stat_dept:hover,.update_stat_position:hover{
        color:inherit !important;
        opacity: .95 !important;
        transform:scale(.8);
    }
    .position-row{ cursor:pointer; }
    .position-row:hover{ background:var(--paper); }
    .position-row.active{ background:var(--primary-light); font-weight:600; }
</style>
<div class="card h-100 d-flex flex-column">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Official Positions</h3>
        <div class="card-tools align-middle">
            <!-- <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button> -->
        </div>
    </div>
    <div class="card-body flex-grow-1">
        <div class="col-12 h-100">
            <div class="row h-100">
                <div class="col-md-6 h-100 d-flex flex-column mb-3 mb-md-0">
                    <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                        <div class="fs-5 col-auto flex-grow-1"><b>Position List</b></div>
                        <div class="col-auto flex-grow-0 d-flex justify-content-end">
                            <a href="javascript:void(0)" id="new_position" class="btn btn-dark btn-sm bg-gradient rounded-2" title="Add position"><span class="fa fa-plus"></span></a>
                        </div>
                    </div>
                    <div class="h-100 overflow-auto border rounded-1 border-dark">
                        <ul class="list-group">
                            <?php
                            $dept_qry = $conn->query("SELECT * FROM `position_list` order by `position` asc");
                            $position_count = 0;
                            while($row = $dept_qry->fetchArray()):
                                $position_count++;
                            ?>
                            <li class="list-group-item d-flex position-row" data-id="<?php echo $row['position_id'] ?>">
                                <div class="col-auto flex-grow-1 view_position" data-id="<?php echo $row['position_id'] ?>">
                                    <i class="fa fa-id-badge me-2 text-success"></i><?php echo $row['position'] ?>
                                </div>
                                <div class="col-auto mx-1">
                                    <?php if($row['is_approver'] == 1): ?>
                                        <span class="badge bg-success rounded-pills"><small>As Signatory</small></span>
                                        <?php endif; ?>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <a href="javascript:void(0)" class="edit_position btn btn-sm btn-primary bg-gradient py-0 px-1 me-1" title="Edit Position Details" data-id="<?php echo $row['position_id'] ?>"  data-name="<?php echo $row['position'] ?>"><span class="fa fa-edit"></span></a>

                                    <a href="javascript:void(0)" class="delete_position btn btn-sm btn-danger bg-gradient py-0 px-1" title="Delete position" data-id="<?php echo $row['position_id'] ?>"  data-name="<?php echo $row['position'] ?>"><span class="fa fa-trash"></span></a>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if($position_count === 0): ?>
                                <li class="list-group-item text-center">No data listed yet.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 h-100 d-flex flex-column">
                    <div id="position_officials_panel">
                        <div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
                            <div class="fs-5 col-auto flex-grow-1"><b>Officials</b></div>
                        </div>
                        <div class="h-100 overflow-auto border rounded-1 border-dark d-flex align-items-center justify-content-center text-muted py-4">
                            Click a position on the left to see who holds it.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        // position
        $('#new_position').click(function(){
            uni_modal('Add New Position',"manage_position.php")
        })
        $('.edit_position').click(function(e){
            e.stopPropagation();
            uni_modal('Edit Position Details',"manage_position.php?id="+$(this).attr('data-id'))
        })
        $('.delete_position').click(function(e){
            e.stopPropagation();
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Position List?",'delete_position',[$(this).attr('data-id')])
        })
        // Click a position row (but not its edit/delete buttons) to view who holds it
        $('.view_position').click(function(){
            var id = $(this).attr('data-id');
            $('.position-row').removeClass('active');
            $(this).closest('.position-row').addClass('active');
            $('#position_officials_panel').load('position_officials.php?position_id=' + id);
        })
    })
    function delete_position($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_position',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>
<?php require_once(__DIR__.'/footer.php'); ?>
