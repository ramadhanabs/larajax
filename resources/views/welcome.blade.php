@extends('layout/main')
@section('title', 'Ini halaman mahasiswa')

@section('container')
    <div class="container">

    <!-- Main component for a primary marketing message or call to action -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Contact list
                        <a onclick="addForm()" class="btn btn-primary pull-right" style="margin-top: -8px;">Add Contact</a>
                    </h4>
                </div>
                <div class="panel-body">
                    <table id="contact-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30">No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Photo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('form')
    </div> <!-- /container -->
    @endsection

    @section('script')
    <script type="text/javascript">
        var table = $('#contact-table').DataTable({
                    processing:true,
                    serverside:true,
                    ajax:"{{ route('api.contact')}}",
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'email', name: 'email'},
                        {data: 'action', name: 'action', orderable:false, searchable:false},

                    ]
        });

        function addForm() {
            save_method="add";
            $('input[name=_method]').val('POST');
            $('#modal-form').modal('show');
            $('#modal-form form')[0].reset();
            $('.modal-title').text('Add Contact');
        }

        function editForm(id) {
            save_method = 'edit';
            $('input[name=_method]').val('PATCH');
            $('#modal-form form')[0].reset();
            $.ajax({
                url: "{{ url('contact') }}" + '/' + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#modal-form').modal('show');
                    $('.modal-title').text('Edit Contact');
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
            },
                error : function() {
                    alert("Nothing Data");
                }
            });
        }

        function deleteData(id){
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    buttons: true,
                    dangerMode: true,
                }).then((confirmed)=>{
                    if(confirmed){
                        $.ajax({
                            url : "{{ url('contact') }}" + '/' + id,
                            type : "POST",
                            data : {'_method' : 'DELETE', '_token' : csrf_token},
                            success : function(data) {
                                table.ajax.reload();
                                swal({
                                    title: 'Success!',
                                    text: data.message,
                                    type: 'success',
                                    timer: '1500'
                                })
                            },
                            error : function () {
                                swal({
                                    title: 'Oops...',
                                    text: data.message,
                                    type: 'error',
                                    timer: '1500'
                                })
                            }
                        });
                    }else{
                        swal("Cancelled", "Your page has not been deleted !", "error");
                    }
                })
        }

        $(function(){
            $('#modal-form form').validator().on('submit', function (e) {
                if (!e.isDefaultPrevented()){
                    var id = $('#id').val();
                    if (save_method == 'add') url = "{{ url('contact') }}";
                    else url = "{{ url('contact') . '/' }}" + id;

                    $.ajax({
                        url : url,
                        type : "POST",
                        data : $('#modal-form form').serialize(),
                        success : function(data) {
                            $('#modal-form').modal('hide');
                            table.ajax.reload();
                        },
                        error : function(){
                            alert('Something false!');
                        }
                    });
                    return false;
                }
            });
        });
        
    </script>
    @endsection


