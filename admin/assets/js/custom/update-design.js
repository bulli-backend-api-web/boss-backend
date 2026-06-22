"use strict";

var KTupdateDesignDetails = function () {
    var form, validator, submitButton;

    return {
        init: function () {
            form = document.getElementById("kt_update_design_form");
            submitButton = document.getElementById("kt_update_design_details_submit");

            validator = FormValidation.formValidation(form, {
                fields: {
                    design_name: {
                        validators: {
                            notEmpty: { message: "Please enter design name" },
                        },
                    },
                    design_code: {
                        validators: {
                            notEmpty: { message: "Please enter design Code" },
                        },
                    },
                    
                    occasion: {
                        validators: {
                            notEmpty: { message: "Please Enter Occasion" },
                        },
                    },
                    
                   
                    style: {
                        validators: {
                            notEmpty: { message: "Please Enter Style" },
                        },
                    },
                    color: {
                        validators: {
                            notEmpty: { message: "Please Enter Color" },
                        },
                    },
                    minimum_sketch : {
                        validators: {
                            notEmpty: { message: "Please enter minimum sketches" },
                        },
                    },
                    assign_to : {
                        validators: {
                            notEmpty: { message: "Plase select assing to" },
                        },
                    },
                    remarks: {
                        validators: {
                            callback: {
                                message: "Please enter remarks",
                                callback: function(input) {
                                    var status = $('#status').val();
                                    // remarks required only when status is 2 or 3
                                    if (status == 2 || status == 3) {
                                        return input.value.trim() !== '';
                                    }
                                    return true;
                                }
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
                        let formElement = document.getElementById("kt_update_design_form");
                        let formData = new FormData(formElement);
                        formData.append('action', 'update-design-details');
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
    KTupdateDesignDetails.init();
});
