"use strict";

var KTcreateStaff = function () {
    var form, validator, submitButton;

    return {
        init: function () {
            form = document.getElementById("regForm");
            submitButton = document.getElementById("kt_create_staff_details_submit");

            validator = FormValidation.formValidation(form, {
                fields: {
                    firstName: {
                        validators: {
                            notEmpty: { message: "Please enter First Name" }
                        }
                    },
                    lastName: {
                        validators: {
                            notEmpty: { message: "Please enter Last Name" }
                        }
                    },
                    gender : {
                        validators: {
                            notEmpty: { message: "Please Select Gender" }
                        }
                    },
                    employment_type : {
                        validators: {
                            notEmpty: { message: "Please Select Employment Type" }
                        }
                    },
                    mobile1 : {
                        validators: {
                            notEmpty: { message: "Please Enter Mobile number" }
                        }
                    },
                    email : {
                        validators: {
                            notEmpty: { message: "Please Enter Email Address" }
                        }
                    },
                    address : {
                        validators: {
                            notEmpty: { message: "Please Enter Address" }
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
                        let formElement = document.getElementById("regForm");
                        let formData = new FormData(formElement);
                        formData.append('action', 'add-staff-details');
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
    KTcreateStaff.init();
});
