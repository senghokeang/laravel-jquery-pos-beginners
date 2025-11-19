
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.min.css';
import '../css/style.css';
import '../css/app.css';
import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';
window.bootstrap = bootstrap;
window.$ = jQuery;
import printJS from 'print-js';

$(function () {
    $("body").show();
    getProduct();
    selectTable();
});

window.selectAll = (event) => {
    $('.item').prop('checked', event.target.checked);
    $('#change_table').prop('disabled', !event.target.checked);
};

window.checkItem = () => {
    const total = $('.item').length;
    const checked = $('.item:checked').length;

    if (checked === 0) {
        $('#selectAll').prop('checked', false).prop('indeterminate', false);
        $('#change_table').prop('disabled', true);
    } else if (checked === total) {
        $('#selectAll').prop('checked', true).prop('indeterminate', false);
        $('#change_table').prop('disabled', false);
    } else {
        $('#selectAll').prop('checked', false).prop('indeterminate', true);
        $('#change_table').prop('disabled', false);
    }
};

const formModalId = document.getElementById("formModal");
const formModal = new bootstrap.Modal(formModalId);
if (formModalId) {
    formModalId.addEventListener("shown.bs.modal", (event) => {
        $("#autofocus").trigger("focus").trigger("select");
    });
    formModalId.addEventListener("hide.bs.modal", (event) => {
        document.activeElement?.blur();
    });
}

window.ajaxPopup = function (url, bigModal = true) {
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
            formModal.show();
        },
        error: function (xhr, status, error) {
            showError(xhr.responseJSON.message);
        },
        complete: function () {
            $(".loading").hide();
        }
    });
};

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
const errorModal = new bootstrap.Modal(errorModalId);
if (errorModalId) {
    errorModalId.addEventListener("hide.bs.modal", (event) => {
        document.activeElement?.blur();
    });
}

window.addToOrder = function (product_id) {
    ajaxSubmit("cashier/add-to-order", { id: product_id, }, "orderList");
};

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
            $("#orderList").html(data);
            $(".loading").hide();
        },
        error: function (xhr, textStatus, errorThrown) {
            showError(xhr.responseJSON.message);
        },
    });
    return false;
});

window.ajaxLoad = function (url, content) {
    content = typeof content !== "undefined" ? content : "content";
    $(".loading").show();
    $.ajax({
        type: "GET",
        url: url,
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

window.getProduct = (event = null) => {
    if (event) {
        $("span.menu-item").removeClass('active');
        $(event.target).addClass('active');
    }
    var category = $('.menu-item.active').data('category');
    var search = $('#search_product').val();
    ajaxLoad("cashier/product/" + category + "?search=" + search, "productList");
};

window.selectTable = function (new_table_id = 0, old_table_id = 0) {
    formModal.hide();
    let ids = $('.item:checked').map(function () {
        return $(this).val();
    }).get().join();
    ajaxSubmit("cashier/select-table", {
        old_table_id: old_table_id,
        new_table_id: new_table_id,
        ids: ids
    }, "orderList");
};

window.ajaxSubmit = function (url, data, content = "orderList") {
    $(".loading").show();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function (data) {
            if (data == "NOTABLE")
                showError("Please select any table first!");
            else
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

window.ajaxPrint = function (url, data, content = "printContent") {
    $(".loading").show();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function (data) {
            $("#" + content).html(data);
        },
        error: function (xhr, status, error) {
            showError(xhr.responseJSON.message);
        },
        complete: function () {
            printJS({
                printable: content,
                type: "html",
                scanStyles: false,
                style: "#" + content + "{ display: block !important; }"
            });
            $(".loading").hide();
        }
    });
};

$(document).on("submit", "form#paymentForm", function (event) {
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
            if (!data.success) {
                $("#receive_amount_error").text(data.errors['receive_amount'][0]);
                $("#autofocus").trigger("focus").trigger("select");
            } else {
                formModal.hide();
                $("#printContent").html(data.content);
                printJS({
                    printable: "printContent",
                    type: "html",
                    scanStyles: false,
                    style: "#printContent{ display: block !important; }"
                });
                selectTable();
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            showError(xhr.responseJSON.message);
        },
        complete: function () {
            $(".loading").hide();
        }
    });
    return false;
});

window.showError = (msg) => {
    $('.modal-body p', errorModalId).text(msg);
    errorModal.show();
}

const successModalId = document.getElementById("successModal");
const successModal = new bootstrap.Modal(successModalId);
if (successModalId) {
    successModalId.addEventListener("hide.bs.modal", (event) => {
        document.activeElement?.blur();
    });
}

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