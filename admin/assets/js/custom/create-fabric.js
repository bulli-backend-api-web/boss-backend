"use strict";

var KTcreateFabric = function () {
    var form, validator, submitButton;

    var bindValidation = function () {
        // If a validator already exists on this form, tear it down first
        // so re-running init() never creates a second instance/listener set.
        if (validator) {
            validator.destroy();
        }

        validator = FormValidation.formValidation(form, {
            fields: {
                fabric_name: {
                    validators: {
                        notEmpty: { message: "Fabric name is required" }
                    }
                },
                fabric_type: {
                    validators: {
                        notEmpty: { message: "Fabric type is required" }
                    }
                },
                gsm: {
                    validators: {
                        numeric: { message: 'GSM must be a number', thousandsSeparator: '' }
                    }
                },
                width: {
                    validators: {
                        numeric: { message: 'Width must be a number', thousandsSeparator: '' }
                    }
                },
                default_rate: {
                    validators: {
                        numeric: { message: 'Rate must be a number', thousandsSeparator: '' }
                    }
                },
                stock_qty: {
                    validators: {
                        numeric: { message: 'Stock quantity must be a number', thousandsSeparator: '' }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    rowSelector: ".fv-row",
                    eleInvalidClass: "",
                    eleValidClass: ""
                }),
                submitButton: new FormValidation.plugins.SubmitButton()
            }
        });
    };

    return {
        init: function () {
            form = document.getElementById("kt_create_fabric_form");
            if (!form) return;

            // Hard stop against double-binding: if this form was already
            // wired up (duplicate script include, duplicate init call, etc.),
            // skip — this is what causes the error message to show twice.
            if (form.dataset.fvBound === "1") {
                return;
            }
            form.dataset.fvBound = "1";

            submitButton = document.getElementById("kt_create_fabric_details_submit");
            bindValidation();

            submitButton.addEventListener("click", function (e) {
                e.preventDefault();

                validator.validate().then(function (status) {
                    if (status === "Valid") {
                        submitButton.setAttribute("data-kt-indicator", "on");

                        var formData = new FormData(form);
                        formData.append('action', 'add-fabric');

                        $.ajax({
                            url: form.getAttribute('action'),
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: "json",
                            success: function (response) {
                                submitButton.removeAttribute("data-kt-indicator");

                                if (response.status === "success") {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    }).then(() => {
                                        form.reset();
                                        window.location.reload();
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
                            },
                            error: function () {
                                submitButton.removeAttribute("data-kt-indicator");

                                Swal.fire({
                                    text: "Something went wrong. Please try again.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-danger" }
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            text: "Sorry, please fix the errors in the form.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTcreateFabric.init();
});