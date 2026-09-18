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
        <h3 class="card-title">Individual Clearance List</h3>
        <div class="card-tools align-middle">
            <a class="btn btn-dark btn-sm py-1 rounded-0" href="javascript:void(0)" id="create_new">Add New</a>
            <button class="btn btn-success btn-sm py-1 rounded-0" type="button" id="print"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card-body">
        <div id="outprint">
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
                    <th class="text-center p-0">Date Issued</th>
                    <th class="text-center p-0">Name</th>
                    <th class="text-center p-0">Contact</th>
                    <th class="text-center p-0">Purpose</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT *, CONCAT(lastname, ', ', firstname, ', ', middlename) as fullname FROM clearance_list order by `fullname` asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($qry && $row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1 text-end"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                    <td class="py-0 px-1"><?php echo $row['fullname'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['contact'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['purpose'] ?></td>
                    <td class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id="<?php echo $row['clearance_id'] ?>" href="javascript:void(0)">View</a></li>
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['clearance_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['clearance_id'] ?>' data-name='<?php echo $row['fullname'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<noscript>
    <div class="d-flex w-100 align-items-center">
        <div class="col-2 px-3">
            <center><img src="<?php echo is_file('./../uploads/logo.png') ? './../uploads/logo.png' : './../images/no-image-available.png' ?>" alt="Barangay Logo" class="img-fluid rounded-0" width="100px" height="100px"></center>
        </div>
        <div class="col-8 flex-grow-1 lh-1">
            <p class="m-0 text-center">Republic of the Philippines</p>
            <p class="m-0 text-center"><?php echo $_SESSION['system_info']['city'] ?></p>
            <div class="clearfix"></div>
            <p class="fw-bold text-center"><large><?php echo $_SESSION['system_info']['barangay_name'] ?></large></p>
            <p class="fw-bold text-center">Individual Clearance List</p>
        </div>
        <div class="col-2">

        </div>
    </div>
    <hr>
</noscript>
<script>
    var dtable;
    $(function(){
        
        $('#create_new').click(function(){
            uni_modal('Add Clearance',"manage_clearance.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Clearance\'s Details',"manage_clearance.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Clearance Details',"view_clearance.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Clearance List?",'delete_data',[$(this).attr('data-id')])
        })
        $('table td,table th').addClass('align-middle py-1 px-2')
        dtable = $('#tbl-list').dataTable({
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            language: {
                emptyTable: "No clearance records yet."
            }
        })
        $('#print').click(function(){
            dtable.fnDestroy()
            var _p = $('#outprint').clone()
            var _h = $('head').clone()
            var _header = $('noscript').html()
            var el = $('<div>')
            _p.find('#tbl-list tr').each(function(){
                $(this).find('td,th').last().remove()
            })
            el.append(_h)
            el.append(_header)
            el.append(_p)
            
            var nw = window.open("","_blank","width=1000,height=900,top=50,left=250")
                        nw.document.write(el.html())
                        nw.document.close()
                        setTimeout(() => {
                        nw.print()
                            setTimeout(() => {
                                nw.close()
                                dtable = $('#tbl-list').dataTable({
                                    columnDefs: [
                                        { orderable: false, targets: [5] }
                                    ],
                                    language: {
                                        emptyTable: "No clearance records yet."
                                    }
                                })
                            }, 200);
                        }, 500);
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_clearance',
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
