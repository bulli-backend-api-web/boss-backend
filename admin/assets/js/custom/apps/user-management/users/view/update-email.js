"use strict";
var KTUsersUpdateEmail = function() {
    const t = document.getElementById("kt_modal_update_email"),
        e = t.querySelector("#kt_modal_update_email_form"),
        n = new bootstrap.Modal(t);

    return {
        init: function() {
            (() => {
                var o = FormValidation.formValidation(e, {
                    fields: {
                        profile_email: {
                            validators: {
                                notEmpty: {
                                    message: "Email address is required"
                                },
                                emailAddress: {
                                    message: "Please enter a valid email address"
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger,
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: ".fv-row",
                            eleInvalidClass: "",
                            eleValidClass: ""
                        })
                    }
                });

                // cancel + close (same as before) ...

                const i = t.querySelector('[data-kt-users-modal-action="submit"]');
                i.addEventListener("click", function(ev) {
                    ev.preventDefault();

                    o && o.validate().then(function(status) {
                        if (status === "Valid") {
                            i.setAttribute("data-kt-indicator", "on");
                            i.disabled = true;

                            // Prepare form data
                            let formData = new FormData(e);
                            formData.append("action", "update_email");

                            // AJAX call
                            fetch(e.getAttribute('action'), {
                                method: "POST",
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                i.removeAttribute("data-kt-indicator");
                                i.disabled = false;

                                if (data.status === "success") {
                                    Swal.fire({
                                        text: data.message,
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    }).then(function(res) {
                                        if (res.isConfirmed) {
                                            n.hide();
                                            e.reset();
                                            // refresh page
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        text: data.message,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                i.removeAttribute("data-kt-indicator");
                                i.disabled = false;
                                Swal.fire({
                                    text: "Something went wrong: " + error,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            });
                        }
                    });
                });
            })()
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTUsersUpdateEmail.init()
});
