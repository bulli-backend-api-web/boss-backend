"use strict";

var KTUsersAddRole = function () {
    const t = document.getElementById("kt_modal_add_role"),
        e = t.querySelector("#kt_modal_add_role_form"),
        n = new bootstrap.Modal(t);

    return {
        init: function () {
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
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: ".fv-row",
                            eleInvalidClass: "",
                            eleValidClass: ""
                        })
                    }
                });

                // 🔹 Submit button
                const r = t.querySelector('[data-kt-roles-modal-action="submit"]');
                r.addEventListener("click", function (ev) {
                    ev.preventDefault();
                    o && o.validate().then(function (status) {
                        if (status === "Valid") {
                            r.setAttribute("data-kt-indicator", "on");
                            r.disabled = true;

                            var formData = new FormData(e);
                            formData.append("action", "add_role");

                            fetch(e.getAttribute('action'), {
                                method: "POST",
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === "success") {
                                        Swal.fire({
                                            text: data.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: { confirmButton: "btn btn-primary" }
                                        }).then(() => {
                                            n.hide();   // close modal
                                            e.reset();  // reset form
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            text: data.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: { confirmButton: "btn btn-primary" }
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.error("AJAX Error:", err);
                                    Swal.fire({
                                        text: "Something went wrong. Please try again.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    });
                                })
                                .finally(() => {
                                    r.removeAttribute("data-kt-indicator");
                                    r.disabled = false;
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
                });

                // 🔹 Close (X button)
                t.querySelector('[data-kt-roles-modal-action="close"]').addEventListener("click", ev => {
                    ev.preventDefault();
                    Swal.fire({
                        text: "Are you sure you would like to close?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, close it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            e.reset();
                            n.hide();
                        }
                    });
                });

                // 🔹 Cancel (Discard) button
                t.querySelector('[data-kt-roles-modal-action="cancel"]').addEventListener("click", ev => {
                    ev.preventDefault();
                    Swal.fire({
                        text: "Are you sure you would like to discard changes?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, discard it!",
                        cancelButtonText: "No, return",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-active-light"
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            e.reset();
                            n.hide();
                        }
                    });
                });
            })();

            // 🔹 Select all checkbox logic
            (() => {
                const t = e.querySelector("#kt_roles_select_all"),
                    n = e.querySelectorAll('[type="checkbox"]');
                if (t) {
                    t.addEventListener("change", ev => {
                        n.forEach(chk => {
                            chk.checked = ev.target.checked;
                        });
                    });
                }
            })();
        }
    }
}();

KTUtil.onDOMContentLoaded(function () {
    KTUsersAddRole.init();
});
