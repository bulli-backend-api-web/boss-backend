"use strict";
var KTUsersUpdatePassword = function() {
    const t = document.getElementById("kt_modal_update_password"),
        e = t.querySelector("#kt_modal_update_password_form"),
        n = new bootstrap.Modal(t);

    return {
        init: function() {
            (() => {
                var o = FormValidation.formValidation(e, {
                    fields: {
                        current_password: {
                            validators: {
                                notEmpty: {
                                    message: "Current password is required"
                                }
                            }
                        },
                        new_password: {
                            validators: {
                                notEmpty: {
                                    message: "The password is required"
                                },
                                callback: {
                                    message: "Please enter valid password",
                                    callback: function(input) {
                                        if (input.value.length > 0) return validatePassword();
                                    }
                                }
                            }
                        },
                        confirm_password: {
                            validators: {
                                notEmpty: {
                                    message: "The password confirmation is required"
                                },
                                identical: {
                                    compare: function() {
                                        return e.querySelector('[name="new_password"]').value;
                                    },
                                    message: "The password and its confirm are not the same"
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

                // cancel + close handlers (same as before)...
                t.querySelector('[data-kt-users-modal-action="close"]').addEventListener("click", cancelHandler);
                t.querySelector('[data-kt-users-modal-action="cancel"]').addEventListener("click", cancelHandler);

                function cancelHandler(ev) {
                    ev.preventDefault();
                    Swal.fire({
                        text: "Are you sure you would like to cancel?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, cancel it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function(result) {
                        if (result.value) {
                            e.reset();
                            n.hide();
                        } else if (result.dismiss === "cancel") {
                            Swal.fire({
                                text: "Your form has not been cancelled!",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                }

                // submit handler with AJAX
                const a = t.querySelector('[data-kt-users-modal-action="submit"]');
                a.addEventListener("click", function(ev) {
                    ev.preventDefault();
                    o && o.validate().then(function(status) {
                        if (status === "Valid") {
                            a.setAttribute("data-kt-indicator", "on");
                            a.disabled = true;

                            let formData = new FormData(e);
                            formData.append("action", "update_password");

                            fetch(e.getAttribute('action'), {
                                method: "POST",
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                a.removeAttribute("data-kt-indicator");
                                a.disabled = false;

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
                                            // Optionally refresh
                                            // window.location.reload();
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
                                a.removeAttribute("data-kt-indicator");
                                a.disabled = false;
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
            })();
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTUsersUpdatePassword.init();
});
