"use strict";
var KTUsersUpdatePermissions = function() {
    const modalEl = document.getElementById("kt_modal_update_role"),
          formEl  = modalEl.querySelector("#kt_modal_update_role_form"),
          modal   = new bootstrap.Modal(modalEl);

    return {
        init: function() {
            // ======================
            // Form Validation
            // ======================
            const validator = FormValidation.formValidation(formEl, {
                fields: {
                    role_name: {
                        validators: {
                            notEmpty: { message: "Role name is required" }
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

            // ======================
            // Close button
            // ======================
            modalEl.querySelector('[data-kt-roles-modal-action="close"]').addEventListener("click", e => {
                e.preventDefault();
                Swal.fire({
                    text: "Are you sure you would like to close?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, close it!",
                    cancelButtonText: "No, return",
                    customClass: { confirmButton: "btn btn-primary", cancelButton: "btn btn-active-light" }
                }).then(function(res) {
                    if (res.value) modal.hide();
                });
            });

            // ======================
            // Cancel button
            // ======================
            modalEl.querySelector('[data-kt-roles-modal-action="cancel"]').addEventListener("click", e => {
                e.preventDefault();
                Swal.fire({
                    text: "Are you sure you would like to cancel?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, cancel it!",
                    cancelButtonText: "No, return",
                    customClass: { confirmButton: "btn btn-primary", cancelButton: "btn btn-active-light" }
                }).then(function(res) {
                    if (res.value) {
                        formEl.reset();
                        modal.hide();
                    } else if (res.dismiss === "cancel") {
                        Swal.fire({
                            text: "Your form has not been cancelled!",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            });

            // ======================
            // Submit button
            // ======================
            const submitBtn = modalEl.querySelector('[data-kt-roles-modal-action="submit"]');
            submitBtn.addEventListener("click", function(e) {
                e.preventDefault();

                validator.validate().then(function(status) {
                    if (status === "Valid") {
                        submitBtn.setAttribute("data-kt-indicator", "on");
                        submitBtn.disabled = true;

                        // Prepare FormData
                        let formData = new FormData(formEl);
                        formData.append("action", "update_role"); // Specify action = update

                        // Send AJAX request
                        fetch(formEl.getAttribute('action'), {
                            method: "POST",
                            body: formData
                        })
                        .then(res => res.json())
                        .then(response => {
                            submitBtn.removeAttribute("data-kt-indicator");
                            submitBtn.disabled = false;

                            if (response.status === "success") {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-primary" }
                                }).then(res => {
                                    if (res.isConfirmed) {
                                        location.reload(); // refresh page to show updated role
                                    }
                                });
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-primary" }
                                });
                            }
                        })
                        .catch(err => {
                            submitBtn.removeAttribute("data-kt-indicator");
                            submitBtn.disabled = false;
                            console.error(err);
                            Swal.fire({
                                text: "Something went wrong. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            });
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

            // ======================
// Delete Role Button
// ======================
document.querySelectorAll(".deleteRoleBtn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();
        let roleId = this.getAttribute("data-role-id");

        Swal.fire({
            title: "Are you sure?",
            text: "This role will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel",
            buttonsStyling: false,
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(result => {
            if (result.isConfirmed) {
                // Send AJAX request
                fetch("https://vastranand.com/admin/add-update-role", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=delete_role&role_id=${roleId}`
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status === "success") {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                });
            }
        });
    });
});



        } // init
    };
}();

KTUtil.onDOMContentLoaded(function() {
    KTUsersUpdatePermissions.init();
});
