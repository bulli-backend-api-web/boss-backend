"use strict";

var KTcreateSampleDetails = function () {
    var form, validator, submitButton;

    return {
        init: function () {
            form = document.getElementById("kt_create_sample_form");
            submitButton = document.getElementById("kt_create_sample_details_submit");

            validator = FormValidation.formValidation(form, {
                fields: {
                    design_id: {
                        validators: {
                            notEmpty: { message: "Design code required" },
                        },
                    },
                    sample_name: {
                        validators: {
                            notEmpty: { message: "Sample name required" },
                        },
                    },
                    
                    category: {
                        validators: {
                            notEmpty: { message: "Category required" },
                        },
                    },
                    
                   
                    assign_to: {
                        validators: {
                            notEmpty: { message: "Sampler name required" },
                        },
                    },
                    budget: {
                        validators: {
                            notEmpty: { message: "Budget required" },
                        },
                    },
                    target_days : {
                        validators: {
                            notEmpty: { message: "Days Required" },
                        },
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
                        let formElement = document.getElementById("kt_create_sample_form");
                        let formData = new FormData(formElement);
                        formData.append('action', 'add-sample');
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
    KTcreateSampleDetails.init();
});
