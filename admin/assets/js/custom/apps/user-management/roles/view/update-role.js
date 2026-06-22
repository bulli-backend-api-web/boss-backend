"use strict";
var KTUsersUpdatePermissions = function() {
    const t = document.getElementById("kt_modal_update_role"),
        e = t.querySelector("#kt_modal_update_role_form"),
        n = new bootstrap.Modal(t);
    return {
        init: function() {
            (() => {
                var o = FormValidation.formValidation(e, {
                    fields: {
                        role_name: {
                            validators: {
                                notEmpty: {
                                    message: "Role name is required"
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
                t.querySelector('[data-kt-roles-modal-action="close"]').addEventListener("click", t => {
                    t.preventDefault(), Swal.fire({
                        text: "Are you sure you would like to close?",
                        icon: "warning",
                        showCancelButton: !0,
                        buttonsStyling: !1,
                        confirmButtonText: "Yes, close it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function(t) {
                        t.value && n.hide()
                    })
                }), t.querySelector('[data-kt-roles-modal-action="cancel"]').addEventListener("click", t => {
                    t.preventDefault(), Swal.fire({
                        text: "Are you sure you would like to cancel?",
                        icon: "warning",
                        showCancelButton: !0,
                        buttonsStyling: !1,
                        confirmButtonText: "Yes, cancel it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(function(t) {
                        t.value ? (e.reset(), n.hide()) : "cancel" === t.dismiss && Swal.fire({
                            text: "Your form has not been cancelled!.",
                            icon: "error",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        })
                    })
                });
                const i = t.querySelector('[data-kt-roles-modal-action="submit"]');
i.addEventListener("click", function (t) {
    t.preventDefault();

    o && o.validate().then(function (status) {
        if (status === "Valid") {
            // Show loading indicator
            i.setAttribute("data-kt-indicator", "on");
            i.disabled = true;

            // Collect form data
            let formData = new FormData(e);
            formData.append("action", "update_role"); 
             // Send AJAX request
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
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                n.hide();
                                location.reload(); // refresh page
                            }
                        });
                    } else {
                        Swal.fire({
                            text: data.message || "Something went wrong!",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                })
                .catch(error => {
                    i.removeAttribute("data-kt-indicator");
                    i.disabled = false;

                    Swal.fire({
                        text: "AJAX request failed!",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                    console.error("Error:", error);
                });
        } else {
            Swal.fire({
                text: "Sorry, looks like there are some errors detected, please try again.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: { confirmButton: "btn btn-primary" }
            });
        }
    });
})

            })(), (() => {
                const t = e.querySelector("#kt_roles_select_all"),
                    n = e.querySelectorAll('[type="checkbox"]');
                t.addEventListener("change", t => {
                    n.forEach(e => {
                        e.checked = t.target.checked
                    })
                })
            })()
        }
    }
}();
KTUtil.onDOMContentLoaded(function() {
    KTUsersUpdatePermissions.init()
});