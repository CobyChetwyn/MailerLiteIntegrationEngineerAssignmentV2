<!DOCTYPE html>
<html>
<head>
    <title>MailerLite Integration Engineer Assignment | Subscriber Table</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"/>
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">MailerLite Subscribers</h2>

    <button class="mb-4 add-new btn btn-primary">Add New Subscriber</button>

    <table id="subscriberTable" class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Country</th>
            <th>Subscribe Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
        <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="demoModalLabel">Edit Subscriber</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="add-new-user pt-0" id="addNewUserForm">
                            <input type="hidden" name="id" id="user_id">
                            <div class="form-group">
                                <label for="add-user-fullname">Name</label>
                                <input type="text" name="name" class="form-control" id="add-user-fullname" placeholder="John Doe" required>
                            </div>
                            <div class="form-group">
                                <label for="add-user-email">Email Address</label>
                                <input type="email" name="email" class="form-control" id="add-user-email" placeholder="name@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="country" name="country" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Belarus">Belarus</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="Canada">Canada</option>
                                    <option value="China">China</option>
                                    <option value="France">France</option>
                                    <option value="Germany">Germany</option>
                                    <option value="India">India</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Israel">Israel</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Japan">Japan</option>
                                    <option value="Korea">Korea, Republic of</option>
                                    <option value="Mexico">Mexico</option>
                                    <option value="Philippines">Philippines</option>
                                    <option value="Russia">Russian Federation</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="Thailand">Thailand</option>
                                    <option value="Turkey">Turkey</option>
                                    <option value="Ukraine">Ukraine</option>
                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                            <button type="button" class="btn btn-label-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </table>
</div>

</body>

<!-- Jquery Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<!-- Bootstrap Scripts -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
<!-- Custom Scripts -->
<script type="text/javascript">
    $(function () {
        // Prepare DataTable
        var table = $('#subscriberTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('subscriber-management.index') }}",
            columns: [
                {data: 'name', name: 'name', searchable: false, orderable: true},
                {data: 'email', name: 'email', searchable: true, orderable: true},
                {data: 'country', name: 'country', searchable: false, orderable: true},
                {data: 'subscribed_at', name: 'Subscribe Date', searchable: false, orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                sLengthMenu: '_MENU_',
                search: '',
                searchPlaceholder: 'Search..'
            },
        });

        // Delete record
        $(document).on('click', '.delete-record', function () {
            var user_id = $(this).data('id');
            $.ajax({
                type: 'DELETE',
                url: `/subscriber-management/${user_id}`,
                success: function () {
                    table.draw();
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });

        // Edit Subscriber
        $(document).on('click', '.edit-record', function () {
            var user_id = $(this).data('id');

            $('#addNewUserForm')[0].reset();

            // changing the title of offcanvas
            $('#demoModalLabel').html('Edit Subscriber');

            // get data
            $.get(`/subscriber-management\/${user_id}\/edit`, function (data) {
                $('#user_id').val(data.body.data.id);

                $('#add-user-fullname').val(data.body.data.fields.name);

                $('#add-user-email').val(data.body.data.email);
                $( "#add-user-email" ).prop( "disabled", true );

                $('#country').val(data.body.data.fields.country);
                $('#country').trigger('change');
            });

            $('#demoModal').modal('show')
        });

        // Create Subscriber
        $('.add-new').on('click', function () {
            $('#addNewUserForm')[0].reset();
            $('#demoModalLabel').html('Add Subscriber');
            $( "#add-user-email" ).prop( "disabled", false);
            $('#demoModal').modal('show')
        });

        // Subscriber Form
        $("#addNewUserForm").submit(function(event){
            event.preventDefault();
            $.ajax({
                data: $('#addNewUserForm').serialize(),
                url: `/subscriber-management`,
                type: 'POST',
                success: function (status) {
                    table.draw();
                    $('#demoModal').modal('hide')

                    // sweetalert
                    Swal.fire({
                        icon: 'success',
                        title: `Successfully ${status}!`,
                        text: `Subscriber ${status} Successfully.`,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                },
                error: function (err) {
                    $('#demoModal').modal('hide')
                    Swal.fire({
                        title: 'Duplicate Entry!',
                        text: 'This email is already subscribed.',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });
    });
</script>
</html>
