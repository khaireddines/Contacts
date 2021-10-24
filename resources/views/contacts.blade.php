<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contacts</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>

</head>
<body>
<div class="container-fluid">
    <div class="row justify-content-between my-4">
        <div class="col-2">
            <a class="btn btn-outline-info" href="{{route('getClientCode')}}">Authenticate me</a>
        </div>
        <div class="col">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <!-- Button trigger modal -->
        <div class="col-2">
            <button type="button" class="btn btn-outline-success" style="float: right" data-bs-toggle="modal"
                    data-bs-target="#createContact">
                ADD New Contact
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table id="table_id" class="display table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Fax</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['contacts'] as $contact)
                    <tr>
                        <td>{{$contact['id']}}</td>
                        <td>{{$contact['given_name']}}</td>
                        <td>{{$contact['family_name']}}</td>
                        <td>{{(!empty($contact['email_addresses']['0']['email'])) ? $contact['email_addresses']['0']['email'] : '' }}</td>
                        <td>{{(!empty($contact['phone_numbers']['0']['number'])) ? $contact['phone_numbers']['0']['number'] : '' }}</td>
                        <td>{{(!empty($contact['fax_numbers']['0']['number'])) ? $contact['fax_numbers']['0']['number'] : '' }}</td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#updateModel" data-bs-contact='@json($contact)'>
                                Update
                            </button>
                            <form method="post" action="{{url('contacts/'.$contact['id'])}}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="createContact" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{url('contacts')}}" method="POST" enctype="application/x-www-form-urlencoded">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">ADD Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Jhon" value="{{@old('first_name')}}" required>

                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Doe" value="{{@old('last_name')}}" required>

                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{@old('email')}}" required>

                        <label for="phone" class="form-label">Phone</label>
                        <input type="number" class="form-control" id="phone" name="phone" placeholder="123456789" value="{{@old('phone')}}" required>

                        <label for="fax" class="form-label">Fax</label>
                        <input type="number" class="form-control" id="fax" name="fax" placeholder="123456789" value="{{@old('fax')}}" required>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{url('contacts')}}" id="updateForm" method="POST" enctype="application/x-www-form-urlencoded">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Jhon"  required>

                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Doe"  required>

                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"  required>

                        <label for="phone" class="form-label">Phone</label>
                        <input type="number" class="form-control" id="phone" name="phone" placeholder="123456789"  required>

                        <label for="fax" class="form-label">Fax</label>
                        <input type="number" class="form-control" id="fax" name="fax" placeholder="123456789"  required>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#table_id').DataTable();
    });
    let updateModel = document.getElementById('updateModel');
    let initUpdateForm = updateModel.querySelector('.modal-content form').attributes.action.value;

    updateModel.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget
        let contactData = JSON.parse(button.getAttribute('data-bs-contact'));
        let form = updateModel.querySelector('.modal-content form')
        form.attributes.action.value = initUpdateForm+'/'+contactData.id
        form.elements['first_name'].value = contactData.given_name
        form.elements['last_name'].value = contactData.family_name
        form.elements['email'].value = contactData.email_addresses[0].email
        form.elements['phone'].value = contactData.phone_numbers[0].number
        form.elements['fax'].value = contactData.fax_numbers[0].number
    })
</script>

</body>
</html>
