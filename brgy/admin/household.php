<?php require_once(__DIR__.'/header.php'); ?>

<style>
    .logo-img{
        width:45px;
        height:45px;
        object-fit:scale-down;
        background : var(--bs-light);
        object-position:center center;
        border:1px solid var(--bs-dark);
        border-radius:50% 50%;
    }
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Household List</h3>
        <div class="card-tools align-middle">
            <a class="btn btn-dark btn-sm py-1 rounded-0" href="javascript:void(0)" id="create_new">Add New</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered" id="tbl-list">
            <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Date Added</th>
                    <th class="text-center p-0">Name</th>
                    <th class="text-center p-0">Contact</th>
                    <th class="text-center p-0">Info</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT h.*, CONCAT(h.lastname, ', ', h.firstname, ', ', h.middlename) as fullname, p.purok
                        FROM household_list h
                        INNER JOIN `purok_list` p ON h.purok_id = p.purok_id
                        ORDER BY fullname ASC";
                $qry = $conn->query($sql);
                $i = 1;
                if ($qry):
                    while ($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1 text-end"><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                    <td class="py-0 px-1"><?php echo $row['fullname'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['contact'] ?></td>
                    <td class="py-0 px-1 lh-1">
                        <small><span class="text-muted">#: </span><?php echo $row['house_no'] ?></small><br>
                        <small><span class="text-muted">Purok: </span><?php echo $row['purok'] ?></small>
                    </td>
                    <td class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item view_data" data-id="<?php echo $row['household_id'] ?>" href="javascript:void(0)">View</a></li>
                                <li><a class="dropdown-item edit_data" data-id='<?php echo $row['household_id'] ?>' href="javascript:void(0)">Edit</a></li>
                                <li><a class="dropdown-item delete_data" data-id='<?php echo $row['household_id'] ?>' data-name='<?php echo $row['fullname'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php
                    endwhile;
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add Household', "manage_household.php", 'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Household Details', "manage_household.php?id=" + $(this).attr('data-id'), 'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Household Details', "view_household.php?id=" + $(this).attr('data-id'), 'mid-large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>" + $(this).attr('data-name') + "</b> from Household List?", 'delete_data', [$(this).attr('data-id')])
        })
        $('#tbl-list td,#tbl-list th').addClass('align-middle py-1 px-2')
        $('#tbl-list').dataTable({
            columnDefs: [{ orderable: false, targets: [4, 5] }],
            language: {
                emptyTable: "No households recorded yet."
            }
        })
    })
    function delete_data(id){
        $('#confirm_modal button').attr('disabled', true)
        $.ajax({
            url: '../Actions.php?a=delete_household',
            method: 'POST',
            data: { id: id },
            dataType: 'JSON',
            error: function(err){
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled', false)
            },
            success: function(resp){
                if (resp.status == 'success'){
                    location.reload()
                } else {
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled', false)
                }
            }
        })
    }
</script>

<?php require_once(__DIR__.'/footer.php'); ?>
