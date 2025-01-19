<x-layout-dashboard title="Auto Replies">

    {{-- <link href="{{asset('plugins/datatables/datatables.min.css')}}" rel="stylesheet"> --}}
    {{-- <link href="{{asset('plugins/select2/css/select2.css')}}" rel="stylesheet"> --}}
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">


    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Whatsapp</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Auto Reply</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="ms-auto my-4">
        <div class="btn-group">
            <button data-bs-toggle="modal" data-bs-target="#addAutoRespond" type="button"
                class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i>New Auto Reply
            </button>

        </div>
    </div>
    <!--end breadcrumb-->
    {{-- alert --}}
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
    {{--  --}}
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0">Lists auto respond
                    {{ Session::has('selectedDevice') ? 'for ' . Session::get('selectedDevice')['device_body'] : '' }}
                </h5>
                <form class="ms-auto position-relative">
                    <button class="btn  position-absolute top-50 translate-middle-y search-icon px-4"><i
                            class="bi bi-search"></i></button>
                    <input value="{{ request()->has('keyword') ? request()->get('keyword') : '' }}" name="keyword"
                        class="form-control ps-5 px-4" type="text" placeholder="search">

                </form>
            </div>
            <div class="table-responsive mt-3">
                <table class="table align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Keyword</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Quoted</th>
                            <th>Type</th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (Session::has('selectedDevice'))
                            @if ($autoreplies->total() == 0)
                                <x-no-data colspan="5" text="No Autoreplies added yet" />
                            @endif
                            @foreach ($autoreplies as $autoreply)
                                <tr>


                                    <td>
                                        <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                            class="form-control keyword-update" data-id="{{ $autoreply->id }}"
                                            type="text" name="id" value="{{ $autoreply->keyword }}">
                                    </td>
                                    <td class="py-1">
                                        <p> Will respond if Keyword <span
                                                class="badge bg-success">{{ $autoreply['type_keyword'] }}</span></p>
                                        <p>

                                            & when the sender is <span
                                                class="badge bg-warning">{{ $autoreply['reply_when'] }}</span>
                                        </p>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                                class="form-check-input toggle-status" type="checkbox"
                                                data-id="{{ $autoreply->id }}"
                                                {{ $autoreply->status == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="toggle-switch">{{ $autoreply->status }}</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                                class="form-check-input toggle-quoted" type="checkbox"
                                                data-id="{{ $autoreply->id }}"
                                                {{ $autoreply->is_quoted ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="toggle-switch">{{ $autoreply->is_quoted ? 'Yes' : 'No' }}</label>
                                        </div>
                                    </td>
                                    <td>{{ $autoreply['type'] }}</td>

                                    <td>
                                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                            <a onclick="viewReply({{ $autoreply->id }})" href="javascript:;"
                                                class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Views"><i class="bi bi-eye-fill"></i></a>
                                            <form action={{ route('autoreply.delete') }} method="POST">
                                                @method('delete')
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $autoreply->id }}">
                                                <button type="submit" name="delete"
                                                    class="btn text-sm btn-sm text-danger"><i
                                                        class="bi bi-trash-fill"></i></button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">Please select device</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item {{ $autoreplies->currentPage() == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $autoreplies->previousPageUrl() }}">Previous</a>
                    </li>

                    @for ($i = 1; $i <= $autoreplies->lastPage(); $i++)
                        <li class="page-item {{ $autoreplies->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $autoreplies->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    <li
                        class="page-item {{ $autoreplies->currentPage() == $autoreplies->lastPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $autoreplies->nextPageUrl() }}">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>







    <!-- Modal -->
    <div class="modal fade" id="addAutoRespond" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Auto Reply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data" id="formautoreply">
                        @csrf
                        <label for="device" class="form-label">Whatsapp Account</label>
                        @if (Session::has('selectedDevice'))
                            {{-- hidden device_id --}}
                            <input type="hidden" name="device"
                                value="{{ Session::get('selectedDevice')['device_id'] }}">
                            {{-- hidden device_body --}}
                            <input type="text" name="device_body" id="device" class="form-control"
                                value="{{ Session::get('selectedDevice')['device_body'] }}" readonly>
                        @else
                            <input type="text" name="devicee" id="device" class="form-control"
                                value="Please select device" readonly>
                        @endif

                        <div class="form-group">
                            <label for="keyword" class="form-label">Type Keyword</label><br>
                            <input type="radio" value="Equal" name="type_keyword" checked class="mr-2"><label
                                class="form-label">Equal</label>
                            <input type="radio" value="Contain" name="type_keyword"><label
                                class="form-label">Contains</label>
                        </div>
                        <div class="form-group">
                            <label for="keyword" class="form-label">Only reply when sender is </label><br>
                            <input type="radio" value="Group" name="reply_when" class="mr-2"><label
                                class="form-label">Group</label>
                            <input type="radio" value="Personal" name="reply_when"><label
                                class="form-label">Personal</label>
                            <input type="radio" value="All" checked name="reply_when"><label
                                class="form-label">All</label>
                        </div>
                        <label for="keyword" class="form-label">Keyword</label>
                        <input type="text" name="keyword" class="form-control" id="keyword" required>
                        <label for="type" class="form-label">Type Reply</label>
                        <select name="type" id="type" class="js-states form-control" tabindex="-1"
                            required>
                            <option selected disabled>Select One</option>
                            <option value="text">Text Message</option>
                            <option value="media">Media Message</option>
                            {{-- <option value="image">Image Message</option> --}}
                            <option value="list">List Message (Ios & Android) </option>
                            <option value="button">Button Message ( Ios & Android )</option>



                        </select>
                        <div class="ajaxplace"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Auto Reply Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body showReply">
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    {{-- <script src="{{asset('js/pages/datatables.js')}}"></script> --}}
    {{-- <script src="{{asset('js/pages/select2.js')}}"></script> --}}
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    {{-- <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>

    <script src="{{ asset('js/autoreply.js') }}"></script>

</x-layout-dashboard>
