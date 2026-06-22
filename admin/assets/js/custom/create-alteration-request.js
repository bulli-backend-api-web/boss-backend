"use strict";

var KTcreateDesignDetails = function () {
    var form, validator, submitButton;

    return {
        init: function () {
            form = document.getElementById("kt_alteration_form");
            submitButton = document.getElementById("kt_create_alteration_details_submit");

            validator = FormValidation.formValidation(form, {
                fields: {
                    barcode: {
                        validators: {
                            callback: {
                                message: 'Either Barcode or Product is required',
                                callback: function(input) {

                                    let barcode = $('#barcode').val().trim();
                                    let product = $('#product_id').val();

                                    if (barcode !== '' || product !== '') {
                                        return true;
                                    }

                                    return false;
                                }
                            }
                        }
                    },

                    product_id: {
                        validators: {
                            callback: {
                                message: 'Either Barcode or Product is required',
                                callback: function(input) {

                                    let barcode = $('#barcode').val().trim();
                                    let product = $('#product_id').val();

                                    if (barcode !== '' || product !== '') {
                                        return true;
                                    }

                                    return false;
                                }
                            }
                        }
                    },
                    new_size: {
                        validators: {
                            notEmpty: {
                                message: "Please select size",
                            }
                        }
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: "",
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                },
            });

            // On submit button click
            submitButton.addEventListener("click", function (e) {
                e.preventDefault();
                
                validator.validate().then(function (status) {
                    if (status === "Valid") {
                        submitButton.setAttribute("data-kt-indicator", "on");
                        let formElement = document.getElementById("kt_alteration_form");
                        let formData = new FormData(formElement);
                        formData.append('action', 'alteration-request');
                        $.ajax({
                            url: form.getAttribute('action'),
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: "json", 
                            success: function (response) {
                                submitButton.removeAttribute("data-kt-indicator");
                                if(response.status === "success") {
                                    Swal.fire({
                                        text: response.message,
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    }).then(() => {
                                        form.reset();
                                        window.location.href = response.redirect_page;
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
                                    customClass: { confirmButton: "btn btn-danger" },
                                });
                            },
                        });
                    } else {
                        Swal.fire({
                            text: "Sorry, please fix the errors in the form.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" },
                        });
                    }
                });
            });
        },
    };
}();

// Init on DOM load
KTUtil.onDOMContentLoaded(function () {
    KTcreateDesignDetails.init();
});
