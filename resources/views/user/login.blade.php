<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>{{ env('APP_NAME') }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    @vite(['resources/js/app.js'])
</head>

<body style="display: none;">
    <div class="loading"></div>
    <section class="vh-100"
        style="background-image: url('images/bg.jpg');background-position: center; background-repeat: no-repeat; background-size: cover;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem; background: lightsteelblue;">
                        <div class="card-body p-5">
                            <img src="./images/favicon.png"
                                style="position: absolute; top: 10px; left: 10px; height: 80px;" />
                            <h1 class="mb-4 text-center fw-bold">Sign In</h1>
                            <form id="frmLogin" method="post" action="{{ url('./login') }}">
                                @csrf
                                <div class="form-outline mb-4">
                                    <input type="text" class="form-control form-control-lg" placeholder="Username"
                                        id="username" name="username" autofocus />
                                </div>

                                <div class="form-outline mb-4">
                                    <input type="password" class="form-control form-control-lg" placeholder="Password"
                                        id="password" name="password" />
                                </div>
                                <div class="d-grid mb-2">
                                    <button class="btn btn-primary btn-lg" type="submit">
                                        Login
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $("body").show();
        $("input[autofocus]").focus();
        $(document).on("submit", "form#frmLogin", function(event) {
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
                success: function(data) {
                    $(".is-invalid").removeClass("is-invalid");
                    $("span.invalid-feedback").remove();
                    if (!data.success) {
                        for (var control in data.errors) {
                            $("#" + control).addClass("is-invalid");
                            $(
                                "<span class='invalid-feedback'>" +
                                data.errors[control] +
                                "</span>"
                            ).insertAfter($("#" + control));
                        }
                        $("input[autofocus]").focus();
                    } else {
                        window.location.href = '/';
                    }
                    $(".loading").hide();
                },
                error: function(xhr, textStatus, errorThrown) {
                    alert("Error: " + errorThrown);
                },
            });
            return false;
        });
    });
</script>

</html>
