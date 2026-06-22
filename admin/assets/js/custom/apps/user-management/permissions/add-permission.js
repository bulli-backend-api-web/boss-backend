"use strict";
var KTUsersAddPermission = function() {
    const modalEl = document.getElementById("kt_modal_add_permission"),
          formEl  = modalEl.querySelector("#kt_modal_add_permission_form"),
          modal   = new bootstrap.Modal(modalEl);

    return {
        init: function() {
            // ======================
            // Form Validation
            // ======================
            const validator = FormValidation.formValidation(formEl, {
                fields: {
                    permission_name: {
                        validators: {
                            notEmpty: { message: "Permission name is required" }
                        }
                    },
                    permission_category: {
                        validators: {
                            notEmpty: { message: "Permission Category is required" }
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
            modalEl.querySelector('[data-kt-permissions-modal-action="close"]').addEventListener("click", e => {
                e.preventDefault();
                Swal.fire({
                    text: "Are you sure you would like to close?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, close it!",
                    cancelButtonText: "No, return",
                    customClass: { confirmButton: "btn btn-primary", cancelButton: "btn btn-active-light" }
                }).then(res => {
                    if(res.value) modal.hide();
                });
            });

            // ======================
            // Cancel button
            // ======================
            modalEl.querySelector('[data-kt-permissions-modal-action="cancel"]').addEventListener("click", e => {
                e.preventDefault();
                Swal.fire({
                    text: "Are you sure you would like to cancel?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, cancel it!",
                    cancelButtonText: "No, return",
                    customClass: { confirmButton: "btn btn-primary", cancelButton: "btn btn-active-light" }
                }).then(res => {
                    if(res.value) {
                        formEl.reset();
                        modal.hide();
                    }
                });
            });

            // ======================
            // Submit button with AJAX
            // ======================
            const submitBtn = modalEl.querySelector('[data-kt-permissions-modal-action="submit"]');
            submitBtn.addEventListener("click", e => {
                e.preventDefault();

                validator.validate().then(status => {
                    if(status === "Valid") {
                        submitBtn.setAttribute("data-kt-indicator", "on");
                        submitBtn.disabled = true;

                        // Prepare FormData
                        let formData = new FormData(formEl);
                        formData.append("action", "add_permission"); // Optional: server action

                        // 🔹 AJAX call
                        fetch(formEl.getAttribute('action'), {
                            method: "POST",
                            body: formData
                        })
                        .then(res => res.json())
                        .then(response => {
                            submitBtn.removeAttribute("data-kt-indicator");
                            submitBtn.disabled = false;

                            if(response.status === "success") {
                                Swal.fire({
                                    text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-primary" }
                                }).then(() => {
                                    modal.hide();
                                    formEl.reset();
                                    // Optional: reload page or refresh table
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    text: response.message || "Something went wrong.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-primary" }
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            submitBtn.removeAttribute("data-kt-indicator");
                            submitBtn.disabled = false;
                            Swal.fire({
                                text: "AJAX error. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: { confirmButton: "btn btn-primary" }
                            });
                        });

                    } else {
                        Swal.fire({
                            text: "Please correct the errors in the form before submitting.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            });
        }
    }
}();

KTUtil.onDOMContentLoaded(function() {
    KTUsersAddPermission.init();
});
