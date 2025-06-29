@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="text-primary">User Access</h3>
    <!-- ปุ่มเปิด Modal เพิ่ม -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
        ➕ Add User
    </button>

    <!-- ตารางผู้ใช้ -->
    <table class="table table-bordered" id ="data">
        <thead class="table-primary">
            <tr>
                <th>Username</th>
                <th>ชื่อ-สกุล</th>
                <th class="text-center" width = "5%">Role</th>
                <th class="text-center" width = "10%">Del_Product</th>
                <th class="text-center" width = "10%">H-Rims</th>
                <th class="text-center" width = "20%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user_access as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->ptname }}</td>
                    <td class="text-center">{{ $user->role }}</td>
                    <td class="text-center">{{ $user->del_product }}</td>
                    <td class="text-center">{{ $user->h_rims }}</td>
                    <td>
                        <!-- ปุ่ม Edit -->
                        <button class="btn btn-warning btn-sm btn-edit"                            
                            data-username="{{ $user->username }}"
                            data-ptname="{{ $user->ptname }}"
                            data-role="{{ $user->role }}"
                            data-del_product="{{ $user->del_product }}"
                            data-h_rims="{{ $user->h_rims }}"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal">
                            Edit
                        </button>

                        <!-- ปุ่ม Delete -->
                        <form class="d-inline delete-form" method="POST" action="{{ route('admin.user_access.destroy', $user->username) }}">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal Create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.user_access.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create User Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input name="username" type="text" class="form-control mb-2" placeholder="username" required>
                    <input name="ptname" type="text" class="form-control mb-2" placeholder="ptname" required>                  
                    <input type="hidden" name="role" value="user">
                    <br>                                  
                    <input type="checkbox" name="del_product" id="del_product" value="Y">
                    <label for="del_product">Del_product</label>
                    <br>                                  
                    <input type="checkbox" name="h_rims" id="h_rims" value="Y">
                    <label for="h_rims">H-RiMS</label>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editForm" class="modal-content">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control mb-2" id="editusername" name="username" type="text"  required>
                    <input class="form-control mb-2" id="editptname" name="ptname" type="text"   required>
                    <select class="form-select" id="editrole" name="role" >
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>    
                    <br>                                  
                    <input type="checkbox" name="del_product" id="editdel_product" value="Y"
                        {{ $user->del_product === 'Y' ? 'checked' : '' }}>
                    <label for="editdel_product">Del_product</label>
                    <br>                                  
                    <input type="checkbox" name="h_rims" id="edith_rims" value="Y"
                        {{ $user->h_rims === 'Y' ? 'checked' : '' }}>
                    <label for="edith_rims">H-RiMS</label>
                </div>
  
                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert สำหรับ Success -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif

    <!-- JavaScript -->
    <script>
        // Set ข้อมูลใน Edit Modal
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                const username = this.dataset.username;
                const ptname = this.dataset.ptname; 
                const role = this.dataset.role;
                const activedel_product = document.getElementById('editdel_product');
                activedel_product.checked = (this.dataset.del_product === 'Y');
                const activeh_rims= document.getElementById('edith_rims');
                activeh_rims.checked = (this.dataset.h_rims === 'Y');

                document.getElementById('editusername').value = username;
                document.getElementById('editptname').value = ptname; 
                document.getElementById('editrole').value = role;
                // document.getElementById('editForm').action = `/admin/user_access/${username}`;
                document.getElementById('editForm').action = "{{ url('admin/user_access') }}/" + username;
            });
        });

        // SweetAlert ยืนยันลบ
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

</div>
@endsection
