@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header ">
            <div class="head">
                <h1>Phone Book Apps</h1>
            </div>
        </div>
    </div>
    <br />
    <div class="card">
        <div class="card-header">
            <h5 id='texthead'>Add Form</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger" id="error-alert">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <script>
                setTimeout(function () {
                    $('#error-alert').fadeOut();
                }, 5000);
            </script>
            <form action="{{route('users.create')}}" method="POST">
                @csrf
                <div class="row g-1 align-items-center">
                    <div class="col-auto">
                        <label htmlFor="name" class="col-form-label">Name</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" id="name" name='name' class="form-control" placeholder='name' required
                            autocomplete="off" />
                    </div>

                    <div class="col-auto">
                        <label htmlFor="phone" class="col-form-label">Phone</label>
                    </div>
                    <div class="col-auto">
                        <input type="tel" id="phone" name='phone' class="form-control" placeholder='phone' required
                            autocomplete="off" />
                    </div>
                    <div class="col-auto">
                        <button id='btnsave' class='btn btn-light'><i class="fa-regular fa-circle-check"></i>
                            save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br />
    <div class="card">
        <div class="card-header">
            <h5 id='texthead'>Search Form</h5>
        </div>
        <div class="card-body">
            <form id="searchForm" action="{{ route('users.search') }}" method="GET">
                <div class="row g-1 align-items-center">
                    <div class="col-auto">
                        <label htmlFor="name" class="col-form-label">Name</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" id="name" name='name' class="form-control" placeholder='name'
                            autocomplete="off" />
                    </div>

                    <div class="col-auto">
                        <label htmlFor="phone" class="col-form-label">Phone</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" id="phone" name='phone' class="form-control" placeholder='phone'
                            autocomplete="off" />
                    </div>
                    <div class="col-auto">
                        <button class='btn btn-primary'><i class="fa-regular fa-circle-check"></i> search</button>
                        <button class='btn btn-dark btn-reset'><i class="fa-solid fa-rotate"></i>
                            reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br />
    <div>
        <table class="table">
            <thead>
                <tr>
                    <th>Chcek</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault"></label>
                    </td>
                    <td>
                        {{$index + 1}}
                    </td>
                    <td>
                        <span class="display-value">{{ $item->name }}</span>
                        <input type="text" class="form-control edit-input" id="name" name="name" placeholder="name"
                            value="{{ $item->name }}" style="display:none;">
                    </td>
                    <td id="phone-{{$index + 1}}">
                        <span class="display-value">{{ $item->phone }}</span>
                        <input type="text" class="form-control edit-input" id="phone" name="phone" placeholder="phone"
                            value="{{ $item->phone }}" style="display:none;">
                    </td>
                    <td>
                        <button id="btnedit" class="btn btn-light btn-edit" data-id="{{ $item->id }}">
                            <i class="fa-sharp fa-solid fa-pen"></i> edit
                        </button>
                        <form action="{{ route('users.delete', $item->id) }}" method="POST" style="display:inline;"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class='btn btn-danger btn-delete'>
                                <i class="fa-solid fa-ban"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td></td>
                        <td></td>
                        <td id="total-phone"></td>
                    </tr>
                </tfoot>
            </tfoot>
        </table>
    </div>
</div>
</body>

<script>
    $(document).ready(function () {
        var row;
        $(".btn-edit").click(function () {
            row = $(this).closest("tr");
            var id = $(this).data("id");

            // Menyembunyikan tampilan nilai dan menampilkan formulir input edit
            row.find(".display-value").hide();
            row.find(".edit-input").show();

            // Menambahkan logika penyimpanan perubahan saat formulir di-submit
            row.find(".btn-edit").hide();
            row.find(".btn-delete").hide();
            row.find("form").append('<button type="button" class="btn btn-info btn-save me-2" data-id="' + id + '">Save</button>');
            row.find("form").append('<button type="button" class="btn btn-warning btn-cancel" data-id="' + id + '">Cancel</button>');
        });

        $(document).on("click", ".btn-save", function () {
            var id = $(this).data("id");
            var name = row.find(".edit-input[name='name']").val();
            var phone = row.find(".edit-input[name='phone']").val();

            // Ajax untuk mengirim data ke server
            $.ajax({
                url: '/users/update/' + id,
                type: 'PUT',
                cache: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    phone: phone
                },
                success: function (response) {
                    // Handle response dari server
                    if (response.success) {
                        // Tampilkan pesan sukses atau lakukan aksi lain
                        console.log(response.message);
                        // Lakukan tindakan setelah penyimpanan berhasil, misalnya, menyembunyikan formulir edit
                        row.find(".display-value").show();
                        row.find(".edit-input").hide();
                        row.find(".btn-edit").show();
                        row.find(".btn-delete").show();
                        row.find(".btn-save").remove();
                        row.find(".btn-cancel").remove();
                        window.location.reload();
                    } else {
                        // Tampilkan pesan kesalahan jika penyimpanan gagal
                        console.log(response.error);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error, tampilkan pesan kesalahan pada konsol
                    console.error(error);
                }
            });
        });

        $(document).on("click", ".btn-cancel", function () {
            // Menampilkan kembali tampilan nilai dan menyembunyikan formulir input edit
            row.find(".display-value").show();
            row.find(".edit-input").hide();

            // Menampilkan kembali tombol edit dan tombol delete
            row.find(".btn-edit").show();
            row.find(".btn-delete").show();

            // Menghapus tombol save dan tombol cancel
            row.find(".btn-save").remove();
            row.find(".btn-cancel").remove();
        });

        var searchForm = $('#searchForm');
        var resetButton = $('.btn-reset');

        resetButton.on('click', function (event) {
            // Reset form fields
            searchForm[0].reset();

            // Redirect to the home page
            window.location.href = '{{ route("users.index") }}';

            // Prevent form submission and default behavior
            event.preventDefault();
        });

        $('.form-check-input').change(function () {
            updateTotal();
        });

        function updateTotal() {
            var total = 0;

            // Iterasi melalui setiap checkbox
            $('.form-check-input').each(function (index, checkbox) {
                if ($(checkbox).prop('checked')) {
                    // Jika checkbox dicentang, tambahkan nilai phone ke total
                    var phoneValue = $('#phone-' + (index + 1)).text();
                    total += parseInt(phoneValue);
                }
            });

            // Update nilai total pada elemen HTML
            $('#total-phone').html(total);
        }
    });

</script>
@endsection