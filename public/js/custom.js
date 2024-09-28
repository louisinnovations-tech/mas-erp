"use strict";
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

var site_url = $('meta[name="base-url"]').attr("content");

$(document).ready(function () {
    comman_function();

    if ($(".dataTable-desc").length > 0) {
        $(".dataTable-desc").DataTable({
            order: [[3, "desc"]],
        });
    }

    if ($(".dataTable").length > 0) {
        $(".dataTable").DataTable({
            language: {
                url: site_url + '/resources/lang/datatables/' + window.currentLocale + '.json'
            }
        });
    }

    if ($(".dataTable2").length > 0) {
        $(".dataTable2").DataTable();
    }

    if ($(".dataTable3").length > 0) {
        $(".dataTable3").DataTable();
    }

    if ($(".dataTable4").length > 0) {
        $(".dataTable4").DataTable();
    }

    if ($(".dataTable-5").length > 0) {
        $(".dataTable-5").DataTable({
            pageLength : 5,
          });
    }
});
$(document).ready(function () {
    var table = $(".dataTable1").DataTable({
        lengthChange: false,
        buttons: ["copy", "excel", "pdf", "print"],
    });

    table
        .buttons()
        .container()
        .appendTo("#DataTables_Table_0_wrapper .col-md-6:eq(0)");
});

$(document).on("input", ".autogrow", function () {
    $(this)
        .height("auto")
        .height($(this)[0].scrollHeight - 18);
});

$(document).on("click",'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]',
    function () {
        var title = $(this).data("title");
        var size = $(this).attr("data-size") == "" ? "md" : $(this).attr("data-size");
        var url = $(this).data("url");

        $("#commanModel .modal-title").html(title);
        $("#commanModel .modal-dialog").removeClass("modal-lg").removeClass("modal-md").removeClass("modal-sm");
        $("#commanModel .modal-dialog").addClass("modal-" + size);

        $.ajax({
            url: url,
            success: function (data) {
                $("#commanModel .extra").html(data);
                $("#commanModel").modal("show");

                $("#theme_id").trigger("change");

                // Product Page
                $("#enable_product_variant").trigger("change");
                $("#variant_tag").trigger("change");
                $("#maincategory").trigger("change");

                // Review Page
                $("#category_id").trigger("change");

                // coupone Code Page
                $(".code").trigger("click");
                comman_function();
                flat_picker();

                if ($(".multi-select").length > 0) {
                    $($(".multi-select")).each(function (index, element) {
                        var id = $(element).attr("id");
                        var multipleCancelButton = new Choices("#" + id, {
                            removeItemButton: true,
                        });
                    });
                }

                if ($(".dataTable").length > 0) {
                    $(".dataTable").DataTable();
                }

            },
            error: function (data) {
                data = data.responseJSON;
            },
        });


    }
);

function flat_picker() {

    var today = new Date();
    var cuur_time = today.getHours() + ":" + today.getMinutes();

    $("#due_date").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d h:i:s",
        mode: "range",
        locale: {
            firstDayOfWeek: 7, // set start day of week to Sunday
        },
        time_24hr: true,
        minDate: today,

        onChange: function (selectedDates, dateStr, instance) {},
    });

    $("#timesheet_date").flatpickr({
        dateFormat: "d-m-Y",
        locale: {
            firstDayOfWeek: 7, // set start day of week to Sunday
        },

        maxDate: today,

        onChange: function (selectedDates, dateStr, instance) {},
    });

    $(".single-date").flatpickr({
        enableTime: true,
        dateFormat: "d-m-Y h:i:s",
        time_24hr: true,
        onChange: function (selectedDates, dateStr, instance) {},
    });
}
function multi_select() {
    if ($(".select2").length > 0) {
        $($(".select2")).each(function (index, element) {
            var id = $(element).attr("id");
            var multipleCancelButton = new Choices("#" + id, {
                removeItemButton: true,
            });
        });
    } else {
    }
}

$(document).on("click", ".bs-pass-para", function () {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
    });
    swalWithBootstrapButtons
        .fire({
            title: $(this).data("confirm"),
            text: $(this).data("text"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            reverseButtons: false,
        })
        .then((result) => {
            if (result.isConfirmed) {
                $("#" + $(this).data("confirm-yes")).trigger("submit");
            } else if (result.dismiss === Swal.DismissReason.cancel) {
            }
        });
});

function comman_function() {
    if ($('[data-role="tagsinput"]').length > 0) {
        $('[data-role="tagsinput"]').each(function (index, element) {
            var obj_id = $(this).attr("id");
            var textRemove = new Choices(document.getElementById(obj_id), {
                delimiter: ",",
                editItems: true,
                removeItemButton: true,
            });
        });
    }
}

function show_toastr(title, message, type) {
    var o, i;
    var icon = "";
    var cls = "";
    if (type == "success") {
        cls = "primary";
        notifier.show(
            "Success",
            message,
            "success",
            site_url + "/public/assets/images/notification/ok-48.png",
            4000
        );
    } else {
        cls = "danger";
        notifier.show(
            "Error",
            message,
            "danger",
            site_url +
                "/public/assets/images/notification/high_priority-48.png",
            4000
        );
    }
}

PurposeStyle = function () {
    var e = getComputedStyle(document.body);
    return {
        colors: {
            gray: {
                100: "#f6f9fc",
                200: "#e9ecef",
                300: "#dee2e6",
                400: "#ced4da",
                500: "#adb5bd",
                600: "#8898aa",
                700: "#525f7f",
                800: "#32325d",
                900: "#212529",
            },
            theme: {
                primary: e.getPropertyValue("--primary")
                    ? e.getPropertyValue("--primary").replace(" ", "")
                    : "#6e00ff",
                info: e.getPropertyValue("--info")
                    ? e.getPropertyValue("--info").replace(" ", "")
                    : "#00B8D9",
                success: e.getPropertyValue("--success")
                    ? e.getPropertyValue("--success").replace(" ", "")
                    : "#36B37E",
                danger: e.getPropertyValue("--danger")
                    ? e.getPropertyValue("--danger").replace(" ", "")
                    : "#FF5630",
                warning: e.getPropertyValue("--warning")
                    ? e.getPropertyValue("--warning").replace(" ", "")
                    : "#FFAB00",
                dark: e.getPropertyValue("--dark")
                    ? e.getPropertyValue("--dark").replace(" ", "")
                    : "#212529",
            },
            transparent: "transparent",
        },
        fonts: { base: "Nunito" },
    };
};

var PurposeStyle = PurposeStyle();

/********* Cart Popup ********/
$(".wish-header").on("click", function (e) {
    e.preventDefault();
    setTimeout(function () {
        $("body").addClass("no-scroll wishOpen");
        $(".overlay").addClass("wish-overlay");
    }, 50);
});

$("body").on("click", ".overlay.wish-overlay, .closewish", function (e) {
    e.preventDefault();
    $(".overlay").removeClass("wish-overlay");
    $("body").removeClass("no-scroll wishOpen");
});

if ($(".multi-select").length > 0) {
    $($(".multi-select")).each(function (index, element) {
        var id = $(element).attr("id");
        var multipleCancelButton = new Choices("#" + id, {
            removeItemButton: true,
        });
    });
}

function image_upload_bar(type = "") {

    $("#progressContainer").css('display', '')
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const resultDiv = document.getElementById('result');

    let progress = 0;
    let interval = 20; // Change from const to let

    if (type == 'pdf' || type == 'php' ||type == 'word' ) {
        interval = 100; // Adjust the interval duration for the desired animation speed
    }
    if (type == 'zip') {
        interval = 400;

    }


    function simulateUpload() {
        if (progress <= 100) {
            progressBar.value = progress;
            progressText.textContent = progress.toFixed(2) + '%';
            progress += 5; // Simulate progress increase

            setTimeout(simulateUpload, interval);
        } else {
            resultDiv.textContent = 'Simulation completed';
        }
    }

    simulateUpload(); // Start the simulation when the page loads
}

$(document).on("click", ".bs-pass-para-user-delete", function () {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
    });
    swalWithBootstrapButtons
        .fire({
            title: $(this).data("confirm"),
            text: $(this).data("text"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            reverseButtons: false,
        })
        .then((result) => {
            if (result.isConfirmed) {
                swalWithBootstrapButtons.fire({
                    title:'Please Comfirm Action',
                    input: 'text',
                    inputLabel:'What action you want to perform?',
                    inputPlaceholder: 'Delete',
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    reverseButtons: false,
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        if($('#swal2-input').val()=='delete' || $('#swal2-input').val()=='Delete' || $('#swal2-input').val()=='DELETE')
                        {
                            $("#" + $(this).data("confirm-yes")).trigger("submit");
                        }
                        else{
                            alert('Your Defined Action Is Invalid.')
                        }
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                    }
                });

            } else if (result.dismiss === Swal.DismissReason.cancel) {
            }
        });
});

// ChatGPT
$(document).on('click', 'a[data-ajax-popup-over="true"], button[data-ajax-popup-over="true"], div[data-ajax-popup-over="true"]', function () {

    var validate = $(this).attr('data-validate');
    var id = '';
    if (validate) {
        id = $(validate).val();
    }

    var title = $(this).data('title');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var url = $(this).data('url');

    $("#commonModalOver .modal-title").html(title);
    $("#commonModalOver .modal-dialog").addClass('modal-' + size);

    $.ajax({
        url: url + '?id=' + id,
        success: function (data) {
            $('#commonModalOver .modal-body').html(data);
            $("#commonModalOver").modal('show');
            taskCheckbox();
        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });

});

/***********new Code *****************/

$(document).ready(function () {
    LetterAvatar.transform();
    setTimeout(function () {
        getSummurNote()
    }, 600);


    common_bind();
    //common_bind_select();
    $('[data-confirm]').each(function () {
        var me = $(this),
            me_data = me.data('confirm');
        me_data = me_data.split("|");
        me.fireModal({
            title: me_data[0],
            body: me_data[1],
            buttons: [{
                text: me.data('confirm-text-yes') || 'Yes',
                class: 'btn btn-sm btn-primary btn-icon rounded-pill',
                handler: function () {
                    eval(me.data('confirm-yes'));
                }
            },
            {
                text: me.data('confirm-text-cancel') || 'Cancel',
                class: 'btn btn-sm btn-danger btn-icon rounded-pill',
                handler: function (modal) {
                    $.destroyModal(modal);
                    eval(me.data('confirm-no'));
                }
            }
            ]
        })
    });
});


$(document).ready(function () {
    $('.custom-list-group-item').on('click', function () {
        var href = $(this).attr('data-href');
        $('.tabs-card').addClass('d-none');
        $(href).removeClass('d-none');
        $('#tabs .custom-list-group-item').removeClass('text-primary');
        $(this).addClass('text-primary');
    });
});

var DatatableBasic = (function () {
    // Variables
    var $dtBasic = $('#datatable-basic');
    // Methods
    var dataTableLang = {
        paginate: {previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"},
        lengthMenu: "{{__('Show')}} _MENU_ {{__('entries')}}",
        zeroRecords: "{{__('No data available in table.')}}",
        info: "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
        infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
        infoFiltered: "{{ __('(filtered from _MAX_ total entries)') }}",
        search: "{{__('Search:')}}",
        thousands: ",",
        loadingRecords: "{{ __('Loading...') }}",
        processing: "{{ __('Processing...') }}"
    }
    function init($this) {

        // Basic options. For more options check out the Datatables Docs:
        // https://datatables.net/manual/options

        var options = {
            keys: !0,
            select: {
                style: "multi"
            },

            language: dataTabelLang,
        };

        // Init the datatable

        var table = $this.on('init.dt', function () {
            $('div.dataTables_length select').removeClass('custom-select custom-select-sm');

        }).DataTable(options);
    }


    // Events

    if ($dtBasic.length) {
        init($dtBasic);
    }

})();


function toastrs(title, message, type) {

    var f = document.getElementById('liveToast');
    var a = new bootstrap.Toast(f).show();
    if (type == 'success') {
        $('#liveToast').addClass('bg-primary');
    } else {
        $('#liveToast').addClass('bg-danger');
    }
    $('#liveToast .toast-body').html(message);
}

$(document).on('click', '.local_calender .fc-day-grid-event', function (e) {
    // if (!$(this).hasClass('project')) {
    e.preventDefault();
    var event = $(this);
    var title = $(this).find('.fc-content .fc-title').html();
    var size = 'md';
    var url = $(this).attr('href');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);
    $.ajax({
        url: url,
        success: function (data) {
            $('#commonModal .modal-body').html(data);
            $("#commonModal").modal('show');
            common_bind();
            setTimeout(function () {
                getSummurNote()
            }, 600);


        },
        error: function (data) {
            data = data.responseJSON;
            toastrs('Error', data.error, 'error')
        }
    });
    // }
});


$(document).on('click', 'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]', function () {
    var title = $(this).data('title');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var url = $(this).data('url');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);
    $.ajax({
        url: url,
        success: function (data) {
            $('#commonModal .modal-body').html(data);
            $("#commonModal").modal('show');

            taskCheckbox();
            // ddatetime_range();
            common_bind("#commonModal");
            //common_bind_select("#commonModal");
            setTimeout(function () {
                getSummurNote()
            }, 600);


            },
            error: function (data) {
                data = data.responseJSON;
                toastrs('Error', data.error, 'error')
            }
        });

});

$(document).on('click', 'a[data-ajax-popup-over="true"], button[data-ajax-popup-over="true"], div[data-ajax-popup-over="true"]', function () {
    var validate = $(this).attr('data-validate');
    var id = '';
    if (validate) {
        id = $(validate).val();
    }
    var title = $(this).data('title');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var url = $(this).data('url');

    $("#commonModalOver .modal-title").html(title);
    $("#commonModalOver .modal-dialog").addClass('modal-' + size);

    $.ajax({
        url: url + '?id=' + id,
        success: function (data) {
            $('#commonModalOver .modal-body').html(data);
            $("#commonModalOver").modal('show');
            taskCheckbox();
          //  ddatetime_range();
          setTimeout(function () {
            getSummurNote()
        }, 600);

        },
        error: function (data) {
            data = data.responseJSON;
            toastrs('Error', data.error, 'error')
        }
    });
});


function arrayToJson(form) {
    var data = $(form).serializeArray();
    var indexed_array = {};

    $.map(data, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

$(document).on("submit", "#commonModalOver form", function (e) {
     e.preventDefault();
    var data = arrayToJson($(this));
    data.ajax = true;

    var url = $(this).attr('action');
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: function (data) {
            toastrs('Success', data.success, 'success');
            $(data.target).append('<option value="' + data.record.id + '">' + data.record.name + '</option>');
            $(data.target).val(data.record.id);
            $(data.target).trigger('change');
            $("#commonModalOver").modal('hide');

            $(".selectric").selectric({
                disableOnMobile: false,
                nativeOnMobile: false
            });

        },
        error: function (data) {
            data = data.responseJSON;
            toastrs('Error', data.error, 'error')
        }
    });
});

function common_bind(selector = "body") {
    var $datepicker = $(selector + ' .datepicker');

    function init($this) {
        var options = {
            disableTouchKeyboard: true,
            autoclose: true,
            format: 'yyyy-mm-dd',
            locale: date_picker_locale,

        };
        $this.datepicker(options);

    }

    if ($datepicker.length) {
        $datepicker.each(function () {
            init($(this));
        });
        $(".datepicker").datepicker('setDate', new Date());
    }

    LetterAvatar.transform();

}


function taskCheckbox() {
    var checked = 0;
    var count = 0;
    var percentage = 0;

    count = $("#check-list input[type=checkbox]").length;
    checked = $("#check-list input[type=checkbox]:checked").length;
    percentage = parseInt(((checked / count) * 100), 10);
    if (isNaN(percentage)) {
        percentage = 0;
    }
    $(".custom-label").text(percentage + "%");
    $('#taskProgress').css('width', percentage + '%');


    $('#taskProgress').removeClass('bg-warning');
    $('#taskProgress').removeClass('bg-primary');
    $('#taskProgress').removeClass('bg-success');
    $('#taskProgress').removeClass('bg-danger');

    if (percentage <= 15) {
        $('#taskProgress').addClass('bg-danger');
    } else if (percentage > 15 && percentage <= 33) {
        $('#taskProgress').addClass('bg-warning');
    } else if (percentage > 33 && percentage <= 70) {
        $('#taskProgress').addClass('bg-primary');
    } else {
        $('#taskProgress').addClass('bg-success');
    }
}

(function ($, window, i) {
    // Bootstrap 4 Modal
    $.fn.fireModal = function (options) {
        var options = $.extend({
            size: 'modal-md',
            center: false,
            animation: true,
            title: 'Modal Title',
            closeButton: true,
            header: true,
            bodyClass: '',
            footerClass: '',
            body: '',
            buttons: [],
            autoFocus: true,
            created: function () { },
            appended: function () { },
            onFormSubmit: function () { },
            modal: {}
        }, options);

        this.each(function () {
            i++;
            var id = 'fire-modal-' + i,
                trigger_class = 'trigger--' + id,
                trigger_button = $('.' + trigger_class);

            $(this).addClass(trigger_class);

            // Get modal body
            let body = options.body;

            if (typeof body == 'object') {
                if (body.length) {
                    let part = body;
                    body = body.removeAttr('id').clone().removeClass('modal-part');
                    part.remove();
                } else {
                    body = '<div class="text-danger">Modal part element not found!</div>';
                }
            }

            // Modal base template
            var modal_template = '   <div class="modal' + (options.animation == true ? ' fade' : '') + '" tabindex="-1" role="dialog" id="' + id + '">  ' +
                '     <div class="modal-dialog ' + options.size + (options.center ? ' modal-dialog-centered' : '') + '" role="document">  ' +
                '       <div class="modal-content">  ' +
                ((options.header == true) ?
                    '         <div class="modal-header">  ' +
                    '           <div class="modal-title font-weight-bolder">' + options.title + '</div>  ' +
                    ((options.closeButton == true) ?
                        '           <button type="button" class="close" data-dismiss="modal" aria-label="Close">  ' +
                        '             <span aria-hidden="true">&times;</span>  ' +
                        '           </button>  ' :
                        '') +
                    '         </div>  ' :
                    '') +
                '         <div class="modal-body">  ' +
                '         </div>  ' +
                (options.buttons.length > 0 ?
                    '         <div class="modal-footer m-3">  ' +
                    '         </div>  ' :
                    '') +
                '       </div>  ' +
                '     </div>  ' +
                '  </div>  ';

            // Convert modal to object
            var modal_template = $(modal_template);

            // Start creating buttons from 'buttons' option
            var this_button;
            options.buttons.forEach(function (item) {
                // get option 'id'
                let id = "id" in item ? item.id : '';

                // Button template
                this_button = '<button type="' + ("submit" in item && item.submit == true ? 'submit' : 'button') + '" class="' + item.class + '" id="' + id + '">' + item.text + '</button>';

                // add click event to the button
                this_button = $(this_button).off('click').on("click", function () {
                    // execute function from 'handler' option
                    item.handler.call(this, modal_template);
                });
                // append generated buttons to the modal footer
                $(modal_template).find('.modal-footer').append(this_button);
            });

            // append a given body to the modal
            $(modal_template).find('.modal-body').append(body);

            // add additional body class
            if (options.bodyClass) $(modal_template).find('.modal-body').addClass(options.bodyClass);

            // add footer body class
            if (options.footerClass) $(modal_template).find('.modal-footer').addClass(options.footerClass);

            // execute 'created' callback
            options.created.call(this, modal_template, options);

            // modal form and submit form button
            let modal_form = $(modal_template).find('.modal-body form'),
                form_submit_btn = modal_template.find('button[type=submit]');

            // append generated modal to the body
            $("body").append(modal_template);

            // execute 'appended' callback
            options.appended.call(this, $('#' + id), modal_form, options);

            // if modal contains form elements
            if (modal_form.length) {
                // if `autoFocus` option is true
                if (options.autoFocus) {
                    // when modal is shown
                    $(modal_template).on('shown.bs.modal', function () {
                        // if type of `autoFocus` option is `boolean`
                        if (typeof options.autoFocus == 'boolean')
                            modal_form.find('input:eq(0)').focus(); // the first input element will be focused
                        // if type of `autoFocus` option is `string` and `autoFocus` option is an HTML element
                        else if (typeof options.autoFocus == 'string' && modal_form.find(options.autoFocus).length)
                            modal_form.find(options.autoFocus).focus(); // find elements and focus on that
                    });
                }

                // form object
                let form_object = {
                    startProgress: function () {
                        modal_template.addClass('modal-progress');
                    },
                    stopProgress: function () {
                        modal_template.removeClass('modal-progress');
                    }
                };

                // if form is not contains button element
                if (!modal_form.find('button').length) $(modal_form).append('<button class="d-none" id="' + id + '-submit"></button>');

                // add click event
                form_submit_btn.click(function () {
                    modal_form.submit();
                });

                // add submit event
                modal_form.submit(function (e) {
                    // start form progress
                    form_object.startProgress();

                    // execute `onFormSubmit` callback
                    options.onFormSubmit.call(this, modal_template, e, form_object);
                });
            }

            $(document).on("click", '.' + trigger_class, function () {
                $('#' + id).modal(options.modal);

                return false;
            });
        });
    }

    // Bootstrap Modal Destroyer
    $.destroyModal = function (modal) {
        modal.modal('hide');
        modal.on('hidden.bs.modal', function () { });
    }
})(jQuery, this, 0);


function postAjax(url, data, cb) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var jdata = { _token: token };

    for (var k in data) {
        jdata[k] = data[k];
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: jdata,
        success: function (data) {
            if (typeof (data) === 'object') {
                cb(data);
            } else {
                cb(data);
            }
        },
    });
}

function deleteAjax(url, data, cb) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var jdata = { _token: token };

    for (var k in data) {
        jdata[k] = data[k];
    }

    $.ajax({
        type: 'DELETE',
        url: url,
        data: jdata,
        success: function (data) {
            if (typeof (data) === 'object') {
                cb(data);
            } else {
                cb(data);
            }
        },
    });
}


function getSummurNote(){
    if ($(".summernote-simple").length) {
        $('.summernote-simple').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 250,
        });



    }
}