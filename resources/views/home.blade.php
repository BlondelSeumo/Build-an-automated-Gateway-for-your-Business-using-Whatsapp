<x-layout-dashboard title="Home">

    <div class="app-content">
        <div class="content-wrapper">
            <div class="container">

                @if (session()->has('alert'))
                    <x-alert>
                        @slot('type', session('alert')['type'])
                        @slot('msg', session('alert')['msg'])
                    </x-alert>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">Total Devices</p>
                                        <h4 class="mb-0">{{ $user->devices_count }}</h4>
                                        <p class="mb-0 mt-2 font-13">
                                            <span>Your limit device : {{ $user->limit_device }}</span>
                                        </p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-primary text-white">
                                        <i class="bi bi-whatsapp"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">Blast/Bulk</p>
                                        <p class="mb-0 badge bg-warning">{{ $user->blasts_pending }} Wait </p>
                                        <p class="mb-0 badge bg-success">{{ $user->blasts_success }} Sent </p>
                                        <p class="mb-0 badge bg-danger">{{ $user->blasts_failed }} Fail </p>
                                        <p class="mb-0 mt-2 font-13">
                                            From {{ $user->campaigns_count }} Campaigns
                                            </span></p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-success text-white">
                                        <i class="bi bi-broadcast"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">Subscription Status</p>
                                        <h4 class="mb-0">{{ $user->subscription_status }}</h4>
                                        <p class="mb-0 mt-2 font-13"><span>
                                                Expired : {{ $user->expired_subscription_status }}
                                            </span></p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-orange text-white">
                                        <i class="bi bi-emoji-heart-eyes"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">All Messages Sent</p>
                                        <h4 class="mb-0">{{ $user->message_histories_count }}</h4>
                                        <p class="mb-0 mt-2 font-13"><span>From messages histories</span></p>
                                    </div>
                                    <div class="ms-auto widget-icon bg-info text-white">
                                        <i class="bi bi-chat-left-text"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end row-->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">Whatsapp Account</h5>
                            <form class="ms-auto position-relative">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addDevice"><i class="bi bi-plus"></i> Add Device</button>

                            </form>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table align-middle">
                                <thead>
                                    <th>Number</th>
                                    <th>Webhook URL</th>
                                    <th>Messages Sent</th>
                                    <th>status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @if ($numbers->total() == 0)
                                        <x-no-data colspan="5" text="No Device added yet" />
                                    @endif
                                    @foreach ($numbers as $number)
                                        <tr>

                                            <td>{{ $number['body'] }}</td>
                                            <td>
                                                <form action="" method="post">
                                                    @csrf
                                                    <input type="text"
                                                        class="form-control form-control-solid-bordered webhook-url-form"
                                                        data-id="{{ $number['body'] }}" name=""
                                                        value="{{ $number['webhook'] }}" id="">
                                                </form>
                                            </td>
                                            <td>{{ $number['message_sent'] }}</td>
                                            <td><span
                                                    class="badge bg-{{ $number['status'] == 'Connected' ? 'success' : 'danger' }}">{{ $number['status'] }}</span>
                                            </td>
                                            <td>
                                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                    <a href="{{ route('connect-via-code', $number->body) }}"
                                                        class="text-primary" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title="Connect Via Code"><i
                                                            class="bi bi-phone"></i></a>
                                                    <a href="{{ route('scan', $number->body) }}" class="text-primary"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="connect Via QR"><i class="bi bi-qr-code"></i></a>
                                                    <form action="{{ route('deleteDevice') }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <input name="deviceId" type="hidden"
                                                            value="{{ $number['id'] }}">
                                                        <button type="submit" name="delete"
                                                            class="btn  text-danger outline-none"><i
                                                                class="bi bi-trash"></i></button>
                                                    </form>
                                                </div>


                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>

                            </table>
                        </div>

                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li class="page-item {{ $numbers->currentPage() == 1 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $numbers->previousPageUrl() }}">Previous</a>
                                </li>

                                @for ($i = 1; $i <= $numbers->lastPage(); $i++)
                                    <li class="page-item {{ $numbers->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $numbers->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                <li
                                    class="page-item {{ $numbers->currentPage() == $numbers->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $numbers->nextPageUrl() }}">Next</a>
                                </li>
                            </ul>
                        </nav>

                    </div>
                </div>


            </div>
        </div>
    </div>





    <div class="modal fade" id="addDevice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('addDevice') }}" method="POST">
                        @csrf
                        <label for="sender" class="form-label">Number</label>
                        <input type="number" name="sender" class="form-control" id="nomor" required>
                        <p class="text-small text-danger">*Use Country Code ( without + )</p>
                        <label for="urlwebhook" class="form-label">Link webhook</label>
                        <input type="text" name="urlwebhook" class="form-control" id="urlwebhook">
                        <p class="text-small text-danger">*Optional</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layout-dashboard>
<script>
    var typingTimer; //timer identifier
    var doneTypingInterval = 1000;

    $('.webhook-url-form').on('keyup', function() {
        clearTimeout(typingTimer);
        let value = $(this).val();
        let number = $(this).data('id');

        typingTimer = setTimeout(function() {

            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('setHook') }}',
                data: {
                    csrf: $('meta[name="csrf-token"]').attr('content'),
                    number: number,
                    webhook: value
                },
                dataType: 'json',
                success: (result) => {
                    toastr.success('Webhook URL has been updated');
                },
                error: (err) => {
                    console.log(err);
                }
            })
        }, doneTypingInterval);
    })
</script>
