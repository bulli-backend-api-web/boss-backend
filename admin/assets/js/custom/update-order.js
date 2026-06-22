"use strict";

var KTB2COrderDetails = function () {
    var form, validator, submitButton;

    return {
        init: function () {
            form = document.getElementById("kt_update_b2c_order_form");
            submitButton = document.getElementById("kt_b2c_update_order_details_submit");
            validator = FormValidation.formValidation(form, {
                fields: {
                    fullname: {
                        validators: {
                            notEmpty: { message: "Please enter customer name" },
                        },
                    },
                    
                    cmobile : {
                        validators: {
                            notEmpty: { message: "Please enter Mobile Number" },
                            regexp: {
                                regexp: /^[0-9]{10}$/,
                                message: "Enter a valid 10-digit number",
                            },
                        },
                    },
                    
                    zipcode: {
                        validators: {
                            notEmpty: { message: "Please enter Pincode" },
                        },
                    },
                    
                    state: {
                        validators: {
                            notEmpty: { message: "Please Select State" },
                        },
                    },
                    city: {
                        validators: {
                            notEmpty: { message: "Please Enter city Name" },
                        },
                    },
                    reject_remarks: {
                        validators: {
                            callback: {
                                message: "Please enter remarks",
                                callback: function(input) {

                                    var status = $('#status').val();

                                    if(status == '3') {
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
                    // ❌ removed DefaultSubmit so page won’t reload
                },
            });

            

            // On submit button click
            submitButton.addEventListener("click", function (e) {
                e.preventDefault();
               
                validator.validate().then(function (status) {
                    if (status === "Valid") {
                        submitButton.setAttribute("data-kt-indicator", "on");
                        submitButton.disabled = true;
                        submitButton.innerHTML = 'Please wait...';
                        let form1 = document.getElementById("kt_update_b2c_order_form");
                        let finalData = new FormData(form1);
                        finalData.append('action','update-order');
                        $.ajax({
                            url: form.getAttribute('action'),
                            type: "POST",
                            data: finalData,
                            processData: false,
                            contentType: false,
                            dataType: "json",
                            success: function (response) {
                                submitButton.removeAttribute("data-kt-indicator");
                                submitButton.disabled = false;
                                submitButton.innerHTML = 'Submit';

                                if(response.status === "success") {
                                Swal.fire({
                                        text: response.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                        customClass: { confirmButton: "btn btn-primary" }
                                    }).then(() => {
                                        form.reset();
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
    KTB2COrderDetails.init();
});
