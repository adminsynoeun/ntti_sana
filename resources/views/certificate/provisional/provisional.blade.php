<style>
    body :not(nav):not(nav *) {
        font-family: 'Khmer OS Battambang', Tahoma, sans-serif !important;
    }

    /* customize css modal */
    .modal-content {
        border: 0px solid #e4e9f0 !important;
    }

    /* customize css modal */
</style>

@extends('app_layout.app_layout')

@section('content')
    <x-breadcrumbs :array="[
        ['route' => request()->path(), 'title' => $arr_module[0]->name_kh],
        ['route' => 'certificate/module-menu', 'title' => 'ត្រួតពិនិត្យលិខិតបញ្ជាក់'],
        ['route' => 'department-menu', 'title' => 'ប្រព័ន្ឋគ្រប់គ្រងលិខិតបញ្ជាក់'],
    ]" />

    <div class="row">
        <div class="page-header flex-wrap" style="border-bottom: 0px solid #dfdcdc;z-index:20;">
            <div class="header-left p-3">
            </div>
            <div class="d-grid d-md-flex justify-content-md-end p-3">
                <a class="btn btn-primary btn-icon-text btn-sm mb-2 mb-md-0 me-2" data-toggle="collapse"
                    href="#collapse_search" role="button" aria-expanded="false" aria-controls="collapseExample"
                    name="btn_filter" id="btn_filter">
                    Fliter <i class="mdi mdi-arrow-up-bold"></i>
                </a>
            </div>
        </div>
        <div class="collapse show" id="collapse_search" style="margin-top: -55px;">
            <div class="card card-body">
                <form id="fm_search_student" role="form" class="form-horizontal" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <span class="form-label">ដេប៉ាតឺម៉ង់</span>
                                    <div class="input-group">
                                        <select class="select2-search" id="sch_dept" name="sch_dept" style="width: 100%"
                                            placeholder="សូមជ្រើសរើសដេប៉ាតឺម៉ង់">
                                            <option value="all">ជ្រើសរើសដេប៉ាតឺម៉ង់ទាំងអស់</option>
                                            @foreach ($record_dept as $item)
                                                <option value="{{ $item->code }}">
                                                    {{ $item->name_2 }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <span class="form-label">ក្រុម</span>
                                    <div class="input-group">
                                        <select class="select2-search" id="sch_class_spec" name="sch_class_spec"
                                            style="width: 100%" placeholder="ជ្រើសរើសក្រុមទាំងអស់">
                                            <option value="all">ជ្រើសរើសក្រុមទាំងអស់</option>
                                            @foreach ($record_class as $item)
                                                <option value="{{ $item->code }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <span class="form-label">កម្រិត</span>
                                    <div class="input-group">
                                        <select class="select2-search sch_level" id="sch_level" name="sch_level"
                                            style="width: 100%">
                                            <option value="all">ជ្រើសរើសកម្រិតទាំងអស់</option>
                                            @foreach ($record_level as $item)
                                                <option value="{{ $item->code }}">{{ $item->name_2 }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <span class="form-label">វេន</span>
                                    <div class="input-group">
                                        <select class="select2-search" id="sch_shift" name="sch_shift" style="width: 100%">
                                            <option value="all">ជ្រើសរើសវេនទាំងអស់</option>
                                            @foreach ($record_shift as $item)
                                                <option value="{{ $item->code }}">{{ $item->name_2 }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <span class="form-label">ជំនាញ</span>
                                    <div class="input-group">
                                        <select class="select2-search" id="sch_skill" name="sch_skill" style="width: 100%">
                                            <option value="all">ជ្រើសរើសជំនាញទាំងអស់</option>
                                            @foreach ($record_skill as $item)
                                                <option value="{{ $item->code }}">{{ $item->name_2 }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="col-md-3 pull-left">
                                <input type="text" class="form-control mb-2 mb-md-0 me-2" name="sch_info_student"
                                    id="sch_info_student" placeholder="ស្វែងរក អត្តលេខ ឈ្មោះខ្មែរ ឈ្មោះអង់គ្លេស"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-2">
        <div class="row">
            <div class="control-table table-responsive custom-data-table-wrapper2">
                <div class="col-md-12 mt-0">
                    <table class="table table-striped" id="tbl_card_stu_list">
                        <thead>
                            <tr class="general-data">
                                <th>ល.រ</th>
                                <th>អត្តលេខ</th>
                                <th>គោត្តនាម និងនាម</th>
                                <th>ឈ្មោះជាឡាតាំង</th>
                                <th>ក្រុម/ថ្នាក់</th>
                                <th>វគ្គសិក្សា</th>
                                <th>លេខ</th>
                                <th>ថ្ងៃបោះពុម្ភ</th>
                                <th>ស្ថានភាព</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="pagination_list" class="mt-3 mb-5"></div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const notyf = new Notyf({
                duration: 2000,
                ripple: true,
                dismissible: true,
                position: {
                    x: 'right',
                    y: 'top',
                }
            });

            const $loader = $(".loader");
            $loader.css({
                position: "fixed",
                top: "50%",
                left: "50%",
                transform: "translate(-50%, -50%)",
                "z-index": "1000",
            }).fadeIn();

            const $sch_dept = $("#sch_dept");
            const $sch_class_spec = $("#sch_class_spec");
            const $sch_level = $("#sch_level");
            const $sch_shift = $("#sch_shift");
            const $sch_skill = $("#sch_skill");
            const $sch_info_student = $("#sch_info_student");
            const $tbl = $("#tbl_card_stu_list");
            const $tbody = $("#tbl_card_stu_list tbody");
            const $pagination_list = $("#pagination_list");

            let cache = {};
            let currentPageList = 1;

            function show() {
                const requestData = {
                    dept_code: $sch_dept.val(),
                    class_code: $sch_class_spec.val(),
                    qualification: $sch_level.val(),
                    sections_code: $sch_shift.val(),
                    skills_code: $sch_skill.val(),
                    search: $sch_info_student.val(),
                    page: currentPageList,
                    rows_per_page: parseInt($("#pagination_list .rows_per_page").val() ?? 50),
                };

                const cacheKey = JSON.stringify(requestData);

                // Check if data is already in cache
                if (cache[cacheKey]) {
                    renderTable(cache[cacheKey]);
                    return;
                }

                $.ajax({
                    url: "{{ route('provisional.show') }}",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(requestData),
                    cache: true,
                    success: function(response) {
                        cache[cacheKey] = response; // Store response in cache
                        renderTable(response);
                    },
                    error: function(xhr, status, error) {
                        notyf.error(xhr.statusText);
                    },
                });
            }

            function renderTable(response) {
                $tbody.empty();
                let html = ``;
                const currentPage = response.current_page;
                const rowsPerPage = response.page;
                const json = response.data;

                if (json && json.length > 0) {
                    $.each(json, function(index, item) {
                        const record_assign_no = item.record_assign_no ?? [];
                        const record_print = item.record_print ?? [];
                        const photo = item.photo_status === false ?
                            '/asset/NTTI/images/faces/default_User.jpg' :
                            `/uploads/student/${item.stu_photo}`;

                        html += `<tr>`;
                        html += `<td height="40">${index + 1 + (currentPage - 1) * rowsPerPage}</td>`;
                        html +=
                            `<td><div class="hover-photo"><img src="${photo}" alt="Student Photo"> ${item.code}</div></td>`;
                        html += `<td>${item.name_2}</td>`;
                        html += `<td>${item.name}</td>`;
                        html +=
                            `<td>${item.class == null ? "No Class" : item.class} <label class="fw-bold">|</label> ${item.section_type}</td>`;
                        html +=
                            `<td>${record_assign_no.years ? `ឆ្នាំទីៈ ${record_assign_no.years} | ឆមាសទីៈ ${record_assign_no.semester} | ${record_assign_no.qualification} | ${item.skill}` : '<label>---</label>'}</td>`;
                        html +=
                            `<td>${record_print.reference_code ? `${record_print.reference_code}` : '<label>---</label>'}</td>`;
                        html +=
                            `<td>${record_print.print_date ? new Date(record_print.print_date).toISOString().split('T')[0] : '<label>---</label>'}</td>`;
                        html +=
                            `<td class="align-items-center">${record_print.status == 1 ? `<i class="mdi mdi-check-decagram mdi-24px text-success"></i> បានផ្តល់ជូនបេក្ខជន` : `<i class="mdi mdi-close-octagon mdi-24px text-danger"></i>​មិនទាន់បានផ្តល់ជូនបេក្ខជន`}</td>`;
                        html += `<td>
                        <div class="dropdown">
                            <button type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" title="More Options"
                                class="btn btn-outline-secondary btn-rounded btn-icon" style="width: 35px; height: 35px; margin-right: 5px">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" name="btn_modal_transcript" id="btn_modal_transcript" data-keyword="${item.keyword}"><i class="mdi mdi-printer btn-icon-append"></i>
                                        បោះពុម្ភ</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" name="btn_view_info" id="btn_view_info" data-keyword="${item.keyword}" onclick="window.open('/certificate/transcript/show-info/${item.keyword}', '_blank')" title="មើល" ><i class="mdi mdi-account-search"></i> មើល</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" name="btn_modal_add_transcript" id="btn_modal_add_transcript"
                                        data-dept_code="${item.department_code}" data-class_code="${item.class_code}" data-stu_code="${item.code}"><i class="mdi mdi-plus btn-icon-append"></i>
                                        បង្កើត&ផ្តល់ជូន</a>
                                </li>
                            </ul>
                        </div>
                    </td>`;
                        html += `</tr>`;
                    });
                }

                $tbody.html(html);
                $("#pagination_list").html(response.links);
            }

            $("body").on("click", "#btn_modal_transcript", function(e) {
                e.preventDefault();
                const key = $(this).data("keyword");
                const url = "{{ route('provisional.print', ':key') }}".replace(':key', key);
                window.open(url, '_blank');
            });


            show();
        });
    </script>
@endpush
