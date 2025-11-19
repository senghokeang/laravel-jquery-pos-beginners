
// Laravel POS With jQuery @ https://laravelcenter.com
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.min.css';
import '../css/style.css';
import '../css/app.css';
import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';
import XlsxPopulate from "xlsx-populate/browser/xlsx-populate";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import ApexCharts from 'apexcharts';
window.bootstrap = bootstrap;
window.XlsxPopulate = XlsxPopulate;
window.$ = jQuery;
window.ApexCharts = ApexCharts;


document.addEventListener('DOMContentLoaded', function () {
    $("body").show();
    // Easy selector helper function
    const select = (el, all = false) => {
        el = el.trim()
        if (all) {
            return [...document.querySelectorAll(el)]
        } else {
            return document.querySelector(el)
        }
    }

    //   Easy event listener function
    const on = (type, el, listener, all = false) => {
        if (all) {
            select(el, all).forEach(e => e.addEventListener(type, listener))
        } else {
            select(el, all).addEventListener(type, listener)
        }
    }

    //  Easy on scroll event listener 
    const onscroll = (el, listener) => {
        el.addEventListener('scroll', listener)
    }

    // Sidebar toggle
    if (select('.toggle-sidebar-btn')) {
        on('click', '.toggle-sidebar-btn', function (e) {
            select('body').classList.toggle('toggle-sidebar')
        })
    }

    //    Navbar links active state on scroll
    let navbarlinks = select('#navbar .scrollto', true)
    const navbarlinksActive = () => {
        let position = window.scrollY + 200
        navbarlinks.forEach(navbarlink => {
            if (!navbarlink.hash) return
            let section = select(navbarlink.hash)
            if (!section) return
            if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
                navbarlink.classList.add('active')
            } else {
                navbarlink.classList.remove('active')
            }
        })
    }
    window.addEventListener('load', navbarlinksActive)
    onscroll(document, navbarlinksActive)

    //   Toggle .header-scrolled class to #header when page is scrolled
    let selectHeader = select('#header')
    if (selectHeader) {
        const headerScrolled = () => {
            if (window.scrollY > 100) {
                selectHeader.classList.add('header-scrolled')
            } else {
                selectHeader.classList.remove('header-scrolled')
            }
        }
        window.addEventListener('load', headerScrolled)
        onscroll(document, headerScrolled)
    }

    $(function () {
        $(window).on("hashchange", function () {
            hashChange();
        });
        hashChange();
    });
});

const formModalId = document.getElementById("formModal");
if (formModalId) {
    const formModal = new bootstrap.Modal(formModalId);
    formModalId.addEventListener("shown.bs.modal", (event) => {
        $("#autofocus").trigger("focus").trigger("select");
    });
    formModalId.addEventListener("hide.bs.modal", (event) => {
        document.activeElement?.blur();
    });
}

const deleteModal = document.getElementById("confirmDelete");
if (deleteModal) {
    deleteModal.addEventListener("show.bs.modal", (event) => {
        var data = $(event.relatedTarget).data();
        $("input#delete_id").val(data.recordId);
        document.querySelector('form#deleteForm').action = data.recordUrl;
    });
    deleteModal.addEventListener("hide.bs.modal", (event) => {
        document.activeElement?.blur();
    });
}

const errorModalId = document.getElementById("errorModal");
if (errorModalId) {
    const errorModal = new bootstrap.Modal(errorModalId);
    errorModalId.addEventListener("hide.bs.modal", (event) => {
        document.activeElement?.blur();
    });
}

const successModalId = document.getElementById("successModal");
if (successModalId) {
    const successModal = new bootstrap.Modal(successModalId);
    successModalId.addEventListener("hide.bs.modal", (event) => {
        document.activeElement?.blur();
    });
}

$(document).on("click", "a.page-link", function (event) {
    event.preventDefault();
    ajaxLoad($(this).attr("href"));
});

$(document).on("submit", "form#submitForm", function (event) {
    event.preventDefault();
    $(".loading").show();
    var form = $(this);
    var data = new FormData(form[0]);
    var url = form.attr("action");
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            $(".is-invalid").removeClass("is-invalid");
            $("span.invalid-feedback").remove();
            if (!data.success) {
                for (var control in data.errors) {
                    $("[name='" + control + "']").addClass("is-invalid");
                    $("<span class='invalid-feedback'>" + data.errors[control] + "</span>").insertAfter($("[name='" + control + "']"));
                    $("#autofocus").trigger("focus");
                }
            } else {
                formModal.hide();
                $('.modal-body p', successModalId).text("Data has been saved successfully");
                successModal.show();
                if (data.redirect_url) {
                    ajaxLoad(data.redirect_url);
                }
            }
            $(".loading").hide();
        },
        error: function (xhr, textStatus, errorThrown) {
            showError(xhr.responseJSON.message);
        },
    });
    return false;
});

$(document).on("submit", "form#deleteForm", function (event) {
    event.preventDefault();
    $(".loading").show();
    var form = $(this);
    var data = new FormData(form[0]);
    var url = form.attr("action");
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            $("#content").html(data);
            $(".loading").hide();
        },
        error: function (xhr, textStatus, errorThrown) {
            showError(xhr.responseJSON.message);
        },
    });
    return false;
});



$(document).on("change", "#image", function (event) {
    if ($(this).val() != '') {
        var selectedFile = validateFile(this, 1, [".jpg", ".jpeg", ".bmp", ".gif", ".png",
            ".webp"
        ]);
        if (selectedFile) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(selectedFile);
        }
    }
});

$(document).on("submit", "form#search_form", function (event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const queryString = new URLSearchParams(formData).toString();
    const url = form.getAttribute("action") + "?" + queryString;
    ajaxLoad(url);
});

window.hashChange = function () {
    const url = window.location.hash;
    let hash = url.split('?')[0];

    if (!hash) hash = "#dashboard";
    $("ul#sidebar-nav a.nav-link").each(function () {
        var nav = hash;
        var pos = hash.indexOf("/");
        if (pos > 0) nav = hash.substring(0, pos);
        if (nav == "#report") nav = hash;
        $(this)[$(this).attr("href") === nav ? "removeClass" : "addClass"](
            "collapsed"
        );
    });

    ajaxLoad(hash.replace("#", ""));
};

window.ajaxLoad = function (filename, content) {
    content = typeof content !== "undefined" ? content : "content";
    $(".loading").show();
    $.ajax({
        type: "GET",
        url: filename,
        contentType: false,
        success: function (data) {
            $("#" + content).html(data);
        },
        error: function (xhr, status, error) {
            showError(xhr.responseJSON.message);
        },
        complete: function () {
            $(".loading").hide();
        }
    });
};

window.ajaxPopup = function (url, bigModal = false) {
    $(".loading").show();
    $.ajax({
        type: "GET",
        url: url,
        contentType: false,
        success: function (data) {
            if (bigModal)
                $("#formModal .modal-dialog").removeClass('modal-lg').addClass('modal-lg');
            else
                $("#formModal .modal-dialog").removeClass('modal-lg');
            $("#formModal .modal-content").html(data);
            $(".loading").hide();
            formModal.show();
        },
        error: function (xhr, status, error) {
            showError(xhr.responseJSON.message);
        },
    });
};

window.validateFile = (event, maxFileSize = 1, validFileExtension = [".jpg", ".jpeg", ".bmp", ".gif",
    ".png",
    ".webp"
]) => {
    var file = null;
    var files = event.files;
    if (files && files.length > 0) {
        $("input[name=image]").next('span').remove();
        var filesize = ((files[0].size / 1024) / 1024).toFixed(4); // MB
        if (filesize <= maxFileSize) {
            var blnValid = false;
            for (var j = 0; j < validFileExtension.length; j++) {
                var sCurExtension = validFileExtension[j];
                if (files[0].name.substr(files[0].name.length - sCurExtension.length, sCurExtension
                    .length)
                    .toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }

            files[0].name.substr(files[0].name.length - sCurExtension.length, sCurExtension.length)
            var fileName = files[0].name.substr(0, files[0].name.length - sCurExtension.length)
                .trim();
            fileName = fileName.replace(/[^a-zA-Z0-9-_\s]/g, '');

            if (fileName != files[0].name.substr(0, files[0].name.length - sCurExtension.length)) {
                $("input[name=image]").addClass("is-invalid");
                $("<span class='invalid-feedback'>Invalid filename. Filename only allow alphanumeric chatacters.</span>").insertAfter($("input[name=image]"));
                event.value = '';
            } else if (blnValid) {
                file = files[0];
            } else {
                $("input[name=image]").addClass("is-invalid");
                $("<span class='invalid-feedback'>" + files[0].name +
                    " is invalid, allowed extensions are:" +
                    validFileExtension.join(", ") + "</span>").insertAfter($("input[name=image]"));
                event.value = '';
            }
        } else {
            if (maxFileSize < 1) {
                $("input[name=image]").addClass("is-invalid");
                $("<span class='invalid-feedback'>Maximum file size is " + (maxFileSize * 1000).toString() + "KB.</span>").insertAfter($("input[name=image]"));
                event.value = '';
            } else {
                $("input[name=image]").addClass("is-invalid");
                $("<span class='invalid-feedback'>Maximum file size is " + maxFileSize.toString() + "MB.</span>").insertAfter($("input[name=image]"));
                event.value = '';
            }
        }
    }
    return file;
};

window.removeProfile = () => {
    $('#img_preview').attr('src', 'images/default.png');
    $("#is_deleted_image").val(1);
};

window.showError = (msg) => {
    if (errorModalId) {
        $('.modal-body p', errorModalId).text(msg);
        errorModal.show();
    }
}